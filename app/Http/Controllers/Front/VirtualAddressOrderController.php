<?php

namespace App\Http\Controllers\Front;

use App\Enums\MailTypeEnum;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Http\Controllers\Controller;

class VirtualAddressOrderController extends Controller
{
    protected $productType = ProductTypeEnum::VIRTUAL_ADDRESS->value;
    public function index()
    {
        $user = auth()->user();
        $subscription = $user->subscription('default');
        if(!$subscription){
            return to_route('virtual-address-orders.show','646');
        }
        //get the current vitual address plan order detail and json decode it
        $jsonPlan = json_decode($user->orderDetails()->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)->latest()->first()->plan);
        //get mail type customer ordered(scan or forwarding)
        $mailType = null;
        if(count($jsonPlan->mail_settings) > 0){
            $mailTypeEnum = MailTypeEnum::tryFrom($jsonPlan->mail_type);
            $mailType = $mailTypeEnum
                ? $mailTypeEnum->label().'('.$mailTypeEnum->deliveryDescription().')'
                : null;
        }
        //get subscription type (yearly or monthly)
        $subscriptionInterval = $jsonPlan->subscription_type;
        //get current active subscription
       
        $plan = $subscription->plan;
        $plan->load('features', 'features.featureSetting');
        $subscriptionType = SubscriptionTypeEnum::class;

        // Latest order containing this virtual address plan, used for the
        // invoice and service agreement PDF downloads.
        $invoiceOrder = $user->orders()
            ->whereHas('orderDetails', function ($query) {
                $query->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value);
            })
            ->latest()
            ->first();

        return view(
            'front.order-details.virtual-address',
            compact( 'plan', 'subscription', 'mailType', 'subscriptionType', 'subscriptionInterval', 'invoiceOrder')
        );
    }

    public function showOrderDetail(string $ref_no)
    {
        return view('front.order-details.no-virtual-address');
    }
}
