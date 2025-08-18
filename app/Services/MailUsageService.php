<?php
namespace App\Services;

use App\Models\Mail;
use App\Models\MailSetting;
use App\Models\User;
use App\Models\MailUsage;

class MailUsageService
{
   
    public function getTotalMailUsage()
    {
        return MailUsage::sum('usage_count');
    }
    
    public function getTotalUserMailUsage(User $user): int
    {
        return $user->mailUsages()->sum('usage_count');
    }

    public function getAllMailUsage()
    {
        return MailUsage::get();
    }

    public function getAllUserMailUsage(User $user)
    {
        return $user->mailUsages;
    }

    public function createMailUsage(User $user, MailSetting $mailPriceSetting, int $usageCount = 1, Mail $mail): MailUsage
    {
        $mailUsage = MailUsage::create([
            'user_id' => $user->id,
            'mail_id' => $mail->id, // Assuming you will set this later or it can be nullable
            'price_id' => $mailPriceSetting->stripe_price_id,
            'price' => $mailPriceSetting->price,
            'usage_count' => $usageCount,
            'billed' => false, 
        ]);
        return $mailUsage;
    }
    
    public function getUserBilledMailUsage(User $user)
    {
        return $user->mailUsages()->where('billed', true)->get();
    }
    
    public function getUserUnBilledMailUsage(User $user)
    {
        return $user->mailUsages()->where('billed', false)->get();
    }
}