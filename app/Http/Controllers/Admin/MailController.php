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
            $meteredSub = $selectedUser->subscription('metered');
            $subscriptionItem = $subscription->items()->where('quantity', null)->first() ??
                 ($meteredSub ? $meteredSub->items()->where('quantity', null)->first() : null);

            if (!$subscriptionItem && $subscription->plan) {
                $userMailPlanSetting = $subscription->plan->mailSettings()->where('status', true)->first();
            } elseif ($subscriptionItem && $subscription->plan) {
                $userMailPlanSetting = $subscription->plan->mailSettings()
                    ->where('stripe_product_id', $subscriptionItem->stripe_product)
                    ->where('stripe_price_id', $subscriptionItem->stripe_price)
                    ->where('status', true)->first();
            } else {
                $userMailPlanSetting = null;
            }

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
                        ->orWhere('customer_number', 'LIKE', "%{$query}%")
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
            'description' => ['required', 'string'],
            'mail_type' => ['required', Rule::in(MailTypeEnum::toArray())],
            'mail_status' => ['required', Rule::in(MailStatusEnum::toArray())],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try{
            $selectedUser = User::find($data['user_id']);

            if (!$selectedUser) {
                return redirect()->back()->withErrors(['user_id' => 'Selected user does not exist.']);
            }

            $mailPriceSetting = null;
            $unitPrice = 0.00;

            if($selectedUser->subscribed('default') && $selectedUser->subscription('default')->plan){
                $mailPriceSetting = $selectedUser->subscription('default')->plan->mailSettings()
                    ->where('mail_type', $data['mail_type'])->first();
                $unitPrice = $mailPriceSetting->price ?? 0.00;
            } elseif ($selectedUser->plan) {
                $mailPriceSetting = $selectedUser->plan->mailSettings()
                    ->where('mail_type', $data['mail_type'])->first();
                $unitPrice = $mailPriceSetting->price ?? 0.00;
            }

            $quantity = (int) $data['quantity'];
            $totalPrice = $unitPrice * $quantity;

            $mailData = [
                'user_id' => $selectedUser->id,
                'sender_name' => 'Virtual Address Mail',
                'description' => $data['description'],
                'mail_type' => $data['mail_type'],
                'mail_status' => $data['mail_status'],
                'price' => $totalPrice,
                'payment_status' => PaymentStatusEnum::Pending->value,
                'recieved_at' => now(),
            ];

            if ($data['mail_status'] === MailStatusEnum::Scanned->value) {
                $mailData['scanned_at'] = now();
            } elseif ($data['mail_status'] === MailStatusEnum::Forwarded->value) {
                $mailData['forwarded_at'] = now();
            }

            $mail = Mail::create($mailData);

            // Create a MailUsage record for the new mail
            if($mail && $mailPriceSetting) {
                MailUsage::create([
                    'user_id' => $selectedUser->id,
                    'mail_id' => $mail->id,
                    'price_id' => $mailPriceSetting->stripe_price_id,
                    'price' => $totalPrice,
                    'quantity' => $quantity,
                    'billed' => false,
                ]);
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
