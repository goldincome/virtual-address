<?php
namespace App\Services;

use App\Models\{ UserBilling};


class UserBillingService
{
    public function createOrUpdate(array $billingData ): UserBilling
    { 
        try {
             $userBilling= UserBilling::updateOrCreate(
                ['user_id' => auth()->id()],
                $billingData
            );
            return $userBilling;
        } catch (\Exception $e) {
            throw new \Exception('User Billing Update/Create Error' );
        }
    }

}