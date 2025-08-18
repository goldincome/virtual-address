<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Mail;
use App\Models\User;
use App\Models\MailUsage;
use App\Enums\MailTypeEnum;
use Illuminate\Http\Request;
use App\Enums\MailStatusEnum;
use Illuminate\Validation\Rule;
use App\Enums\PaymentStatusEnum;
use App\Services\MailUsageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MailController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mails = Mail::with('user')->latest()->paginate(10);
        return view('admin.mails.index', compact('mails'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $users = User::orderBy('name')->get();
        $mailTypes = MailTypeEnum::class;
        try{
            // Pre-select user if user_id is in the request from the search page
            $selectedUser = $request->has('user_id') ? User::find($request->user_id) : null;
            if (!$selectedUser) {
                return redirect()->back()->withErrors(['user_id' => 'Selected user does not exist.']);
            }
            //dd($selectedUser->subscription('metered'));
            $subscription = $selectedUser->subscription('default');
            if (!$subscription) {
                throw new Exception('Selected user does not have an active subscription.');
            }
            $subscriptionItem = $subscription->items()->where('quantity', null)->first() ?? 
                 $selectedUser->subscription('metered')->items()->where('quantity', null)->first();
            if (!$subscriptionItem) {
            throw new Exception('Selected user does not have a valid subscription item for mail services.');
            }
            $userMailPlanSetting = $selectedUser->subscription('default')->plan->mailSettings()
            ->where('stripe_product_id', $subscriptionItem->stripe_product)
            ->where('stripe_price_id', $subscriptionItem->stripe_price)
            ->where('status', true)->first();
            if (!$userMailPlanSetting) {
                throw new Exception('Selected user does not have a valid Mail Plan setting configured for their current plan.');
            }
            //dd($userMailPlanSetting , $selectedUser->subscription('default')->items);
            return view('admin.mails.create', compact('users', 'mailTypes', 'selectedUser', 'userMailPlanSetting'));

        } catch (ModelNotFoundException $e) {
            // This specifically catches the User::findOrFail() failure.
            return redirect()->back()->with('error', 'The selected user could not be found.');

        } catch (Exception $e) {
            // This catches all other exceptions thrown in the try block.
            // Redirect back with the user-friendly error message from the thrown exception.
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the user search form and results.
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $users = collect(); // Return an empty collection by default

        if ($query) {
            $users = User::where('name', 'LIKE', "%{$query}%")
                         ->orWhere('email', 'LIKE', "%{$query}%")
                        ->orWhere('phone', 'LIKE', "%{$query}%")
                         ->limit(20)
                         ->get();
        }

        return view('admin.mails.search', compact('users'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'sender_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'mail_type' => ['required', Rule::in(MailTypeEnum::toArray())],
            'scanned_file' => [
                Rule::requiredIf($request->input('mail_type') === MailTypeEnum::Scanned->value),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048' // 2MB Max
            ],
        ]);

        try{
            $selectedUser = User::find($data['user_id']); 

            if (!$selectedUser) {
                return redirect()->back()->withErrors(['user_id' => 'Selected user does not exist.']);
            }
            if($selectedUser->subscribed('default')){
                $data['price']  = $selectedUser->subscription('default')->plan->mailSettings()
                    ->where('mail_type', $data['mail_type'])->first()->price ?? null;
            }
            else {
                //check if user subscription is by DirectDebit and it is active
                $data['price'] = $selectedUser->plan->mailSettings()
                    ->where('mail_type', $data['mail_type'])->first()->price ?? null;
            }
    
            $data['payment_status'] = PaymentStatusEnum::Pending->value; // Default payment status
            $data['mail_status'] = MailStatusEnum::Forwarded->value;
            // Default status
            $data['recieved_at'] = now(); 
            if ($request->hasFile('scanned_file')) {
                $data['mail_status'] = MailStatusEnum::Scanned->value; // Set status to Scanned if file is uploaded
                $path = $request->file('scanned_file')->store('scanned_mails', 'public');
                $data['scan_upload_url'] = $path;
            }

            $mail = Mail::create($data);
            // Create a MailUsage record for the new mail
            if($mail) {
                $subscription = $selectedUser->subscription('default');
                // If the user has a subscription, we can use it to get the mail settings
                $mailPriceSetting = $subscription->plan->mailSettings()->where('mail_type', $data['mail_type'])->first();
                //$usage = $subscription->usageRecordsFor($mailPriceSetting->stripe_price_id);
                // Create a MailUsage record
                app(MailUsageService::class)->createMailUsage($selectedUser, $mailPriceSetting, 1, $mail);
            }
            return redirect()->route('admin.mails.index')
                            ->with('success', 'Mail record created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create mail record: ' . $e->getMessage()]);
        }
    }
   
    /**
     * Display the specified resource.
     */
    public function show(Mail $mail)
    {
        $mail->load('user');
        return view('admin.mails.show', compact('mail'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mail $mail)
    {   
        if($mail->mailUsage()->where('billed', true)->exists()){
           return redirect()->back()
                         ->with('error', 'You cannot delete this mail because it has billed attached to it'); 
        }
        try{
            if ($mail->scanned_file_path) {
                Storage::disk('public')->delete($mail->scan_upload_url);
            }
            $mail->mailUsage()->delete();
            $mail->delete();

            return redirect()->route('admin.mails.index')
                            ->with('success', 'Mail record deleted successfully.');
        }catch(\Exception $e)
        {
            return redirect()->back()
                         ->with('error', 'You cannot delete this mail:'. $e->getMessage()); 
        }
    }

}
