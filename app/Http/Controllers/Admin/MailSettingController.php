<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\User;
use Stripe\StripeClient;
use App\Enums\MailTypeEnum;
use App\Models\MailSetting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\MailSettings\StoreMailSettingRequest;
use App\Http\Requests\MailSettings\UpdateMailSettingRequest;

class MailSettingController extends Controller
{
    protected StripeClient $stripe;

    public function __construct()
    {
        // Initialize the Stripe client with your secret key.
        // This is automatically handled by Cashier, but we'll be explicit here.
        $this->stripe = new StripeClient(config('cashier.secret'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mailSettings = MailSetting::with('plan')->latest()->paginate(10);
        return view('admin.mail-settings.index', compact('mailSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        $mailTypes = MailTypeEnum::cases();
        return view('admin.mail-settings.create', compact('plans', 'mailTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMailSettingRequest $request)
    {
        $settings = $request->validated();
        $mailTypeLabel = MailTypeEnum::tryFrom($request->mail_type)->label(); 
        $plan = Plan::find($request->plan_id);
        if (!$plan) {
            return redirect()->back()->withErrors(['plan_id' => 'Invalid plan selected.']);
        }
        $settings['name'] = $mailTypeLabel . ' - ' . $plan->name;
        $planName = strtolower($plan->name);
        $settings['stripe_price_name'] = Str::snake(strtolower($mailTypeLabel).'_'.$planName.'_metered') ; //'Mail '.ucwords($request->mail_type).'-'. Plan::find($request->plan_id)->name;

        MailSetting::create($settings);

        return redirect()->route('admin.mail-settings.index')
                         ->with('success', 'Mail setting created successfully.');
    }

    
    /**
     * Display the specified resource.
     */
    public function show(MailSetting $mailSetting)
    {
        $mailSetting->load('plan');
        return view('admin.mail-settings.show', compact('mailSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MailSetting $mailSetting)
    {
        $plans = Plan::where('is_active', true)->get();
        $mailTypes = MailTypeEnum::cases();
        return view('admin.mail-settings.edit', compact('mailSetting', 'plans', 'mailTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMailSettingRequest $request, MailSetting $mailSetting)
    {
        $settings = $request->validated();
        $mailTypeLabel = MailTypeEnum::tryFrom($request->mail_type)->label(); 
        $plan = Plan::find($request->plan_id);
        if (!$plan) {
            return redirect()->back()->withErrors(['plan_id' => 'Invalid plan selected.']);
        }
        $settings['name'] = $mailTypeLabel . ' - ' . $plan->name;
        $planName = strtolower($plan->name);
        $settings['stripe_price_name'] = Str::snake(strtolower($mailTypeLabel).'_'.$planName.'_metered') ;
        
        $mailSetting->update($settings);

        return redirect()->route('admin.mail-settings.index')
                         ->with('success', 'Mail setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MailSetting $mailSetting)
    {
        if($mailSetting->plan->isNotEmpty()) {
            return redirect()->route('admin.mail-settings.index')
                             ->with('error', 'Cannot delete mail setting associated with a plan.');
        }
        try{
            $mailSetting->delete();
        } catch (\Exception $e) {
            return redirect()->route('admin.mail-settings.index')
                             ->with('error', 'Error deleting mail setting: ' . $e->getMessage());
        }

        return redirect()->route('admin.mail-settings.index')
                         ->with('success', 'Mail setting deleted successfully.');
    }

    protected function convertToSnakeCase($string)
    {
        return Str::snake(strtolower($string));
    }
}
