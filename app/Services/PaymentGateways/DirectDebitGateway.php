<?php
namespace App\Services\PaymentGateways;

use Illuminate\Http\Request;
use App\Interfaces\PaymentGatewayInterface;
use App\Models\UserBilling;
use App\Services\UserBillingService;

class DirectDebitGateway implements PaymentGatewayInterface
{

    public function __construct()
    {

    }

    public function charge(array $paymentData)
    {
        return true;
    }

    public function paymentSuccess(Request $request)
    {
        return true;
    }

}