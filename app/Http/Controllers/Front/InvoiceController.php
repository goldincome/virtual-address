<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Enums\OrderStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Http\Controllers\Controller;
use App\Services\ServiceAgreementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function show(string $invoice_no)
    {
        try{
            $invoice = $this->resolveInvoice($invoice_no);
            $jsonPlan = $this->resolveJsonPlan($invoice);
            $orderStatuses = OrderStatusEnum::class;
            $subscriptionType = SubscriptionTypeEnum::class;
        }catch(\Exception $e){
            return back()->with('error', 'No invoice record found');
        }
        
        return view('front.invoices.show', compact('invoice', 'orderStatuses', 'subscriptionType', 'jsonPlan'));
    }

    public function download(string $invoice_no)
    {
        try {
            $invoice = $this->resolveInvoice($invoice_no);
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'No invoice record found');
        }

        $data = [
            'order' => $invoice,
            'jsonPlan' => $this->resolveJsonPlan($invoice),
            'subscriptionType' => SubscriptionTypeEnum::class,
        ];

        return Pdf::loadView('pdf.invoice', $data)
            ->download('invoice-' . $invoice->order_no . '.pdf');
    }

    public function agreement(string $invoice_no)
    {
        try {
            $invoice = $this->resolveInvoice($invoice_no);
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'No invoice record found');
        }

        $data = app(ServiceAgreementService::class)
            ->forOrder($invoice, $this->resolveJsonPlan($invoice));

        return Pdf::loadView('pdf.service-agreement', $data)
            ->download('service-agreement-' . $invoice->order_no . '.pdf');
    }

    /**
     * Resolve an invoice order belonging to the authenticated user.
     */
    protected function resolveInvoice(string $invoice_no): Order
    {
        $invoice = auth()->user()->orders()->where('order_no', $invoice_no)->first();

        if (! $invoice) {
            throw (new ModelNotFoundException())->setModel(Order::class);
        }

        return $invoice->load('orderDetails');
    }

    /**
     * Resolve the decoded virtual address plan for a subscribed invoice.
     */
    protected function resolveJsonPlan(Order $invoice)
    {
        if (! $invoice->hasSubscription()) {
            return null;
        }

        $virtualAddressDetail = $invoice->orderDetails()
            ->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)
            ->latest()
            ->first()
            ?? auth()->user()->orderDetails()
            ->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)
            ->latest()
            ->first();

        return $virtualAddressDetail ? json_decode($virtualAddressDetail->plan) : null;
    }

}
