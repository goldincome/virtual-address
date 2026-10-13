<?php

namespace App\Services;

use App\Models\Order;
use App\Models\UserBilling;
use App\Enums\SubscriptionTypeEnum;

class ServiceAgreementService
{
    /**
     * Build the data required to render the virtual office service agreement PDF.
     */
    public function forOrder(Order $order, $jsonPlan = null): array
    {
        $isYearly = $jsonPlan && $jsonPlan->subscription_type === SubscriptionTypeEnum::YEARLY->value;

        $agreementDate = now();
        $durationType = $isYearly ? 'Yearly' : 'Monthly';
        $expiryDate = $isYearly ? $agreementDate->copy()->addYear() : $agreementDate->copy()->addMonth();

        $billing = UserBilling::where('user_id', $order->user_id)->orderByDesc('id')->first();
        $clientAddress = $billing
            ? trim(implode(', ', array_filter([$billing->company_name, $billing->billing_address, $billing->postal_code])))
            : '';

        return [
            'order' => $order,
            'clientName' => $order->user->name,
            'clientCustomerNumber' => $order->user->customer_number,
            'clientAddress' => $clientAddress ?: '-',
            'agreementDate' => $agreementDate->format('F j, Y'),
            'durationType' => $durationType,
            'expiryDate' => $expiryDate->format('F j, Y'),
        ];
    }
}
