<?php
namespace App\Services\PaymentGateways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Interfaces\PaymentGatewayInterface;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalGateway implements PaymentGatewayInterface
{

     protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
    }

    public function charge(array $paymentData)
    {
        $cartTotal = (float) str_replace(',', '', $paymentData['amount']);
        $payData = [
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.success'),
                "cancel_url" => route('payment.cancelled'),
            ],
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $paymentData['currency'],
                    "value" => number_format($cartTotal, 2, '.', ''),
                ],
                "custom_id" => auth()->id(),
                "description" => "Order for ". $paymentData['description']
            ]],
        ];

        $order = $this->provider->createOrder($payData);

        if (isset($order['id']) && $order['status'] === 'CREATED') {
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return Redirect::away($link['href'])->send();
                }
            }
        }
        //throw new \Exception('Something went wrong with PayPal.');
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->get('token');

        $result = $this->provider->capturePaymentOrder($orderId);

        if ($result['status'] === 'COMPLETED') {
 
            return $result;
            
        }

        return false;
    }

}