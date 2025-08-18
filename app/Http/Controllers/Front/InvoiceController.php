<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Enums\OrderStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
     public function index()
    { 
        $orders = auth()->user()->orders()->latest()->paginate();
        $orders->load('orderDetails');
        $orderStatuses = OrderStatusEnum::class;
        $paymentMethods = PaymentMethodEnum::class;
        return view('front.invoices.index', compact('orders', 'orderStatuses', 'paymentMethods'));
    }

    public function show(int $invoice_no)
    {
        try{
            $invoice = auth()->user()->orders()->where('order_no', $invoice_no)->first();
            $invoice->load('orderDetails');
            $jsonPlan = null;
            if($invoice->hasSubscription()){
                $jsonPlan = json_decode(auth()->user()->orderDetails()->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)->latest()->first()->plan);
            }
            $orderStatuses = OrderStatusEnum::class;
            $subscriptionType = SubscriptionTypeEnum::class;
        }catch(\Exception $e){
            return back()->with('error', 'No invoice record found');
        }
        
        return view('front.invoices.show', compact('invoice', 'orderStatuses', 'subscriptionType', 'jsonPlan'));
    }

}
