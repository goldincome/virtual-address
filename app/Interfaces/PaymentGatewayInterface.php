<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{

    public function charge(array $paymentData);
    public function paymentSuccess(Request $request);
}