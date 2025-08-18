@extends('layouts.app')

@section('title')
    Invoice
@endsection

@section('page_title')
    Invoice Details
@endsection

@section('page_intro')
    Invoice No: #{{ $invoice->order_no }}
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md print:shadow-none print:p-0">
        <div class="email-wrapper">
            <div class="header">
                <h1>Invoice</h1>
            </div>
            <div class="content">
               
                <div class="bg-blue-50 p-6 rounded-md text-left my-8 border border-blue-200">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Invoice Details</h3>
                    <div class="space-y-2 text-gray-700 mb-6">
                        <p><strong>Invoice No:</strong> #<span class="font-bold text-lg text-blue-800">{{ $invoice->order_no }}</span></p>
                        <p><strong>Date:</strong> {{ $invoice->created_at->format('F j, Y, g:i a') }}</p>
                        <p><strong>Client Name:</strong> {{ $invoice->user->name }}</p>
                        <p><strong>Client Email:</strong> {{ $invoice->user->email }}</p>
                        <p><strong>Payment Status:</strong> 

                                @if($invoice->status === $orderStatuses::PENDING->value )
                                    <span class="text-red-600 font-semibold">{{ $orderStatuses::PENDING->customerLabel() }}</span>
                                @elseif( $invoice->status === $orderStatuses::CANCELED->value)
                                    <span class="text-red-600 font-semibold">{{ $orderStatuses::CANCELED->customerLabel() }}</span>
                                @elseif( $orderStatuses::DELIVERED->value === $invoice->status)
                                    <span class="text-green-600 font-semibold">{{ $orderStatuses::DELIVERED->customerLabel() }}</span>
                                @elseif($orderStatuses::PAID->value === $invoice->status)
                                    <span class="text-green-600 font-semibold">{{ $orderStatuses::PAID->customerLabel() }}</span>
                                @endif  
                        </p>
                    </div>

                    {{-- Invoice Items Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-700 border-collapse">
                            <thead class="bg-blue-100 text-blue-800 font-semibold">
                                <tr>
                                    <th class="p-3 border-b">Item</th>
                                    <th class="p-3 border-b text-center">Qty</th>
                                    <th class="p-3 border-b text-right">Price</th>
                                    <th class="p-3 border-b text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->orderDetails as $item)
                                    <tr class="border-b last:border-b-0">
                                        <td class="p-3">
                                            @if($item->isVirtualAddress())
                                                <a href="{{ route('virtual-address-orders.show', $item->ref_no) }}">{{ $item->name }}</a>
                                            @elseif($item->isMeetingRoom()) 
                                                <a href="{{ route('meeting-room-orders.show', $item->ref_no) }}">{{ $item->name }}</a>
                                            @elseif($item->isConferenceRoom())
                                                <a href="{{ route('conference-room-orders.show', $item->ref_no) }}">{{ $item->name }}</a>
                                            @endif
                                                    
                                            @if($item->booked_date)
                                                <div class="text-xs text-gray-500">Date: {{ \Carbon\Carbon::parse($item->booked_date)->format('D, M j, Y') }}</div>
                                            @endif
                                            @if($item->all_booked_time)
                                                @foreach(json_decode($item->all_booked_time) as $timeDisplay)
                                                    <div class="text-xs text-gray-500">
                                                        Time: {{ \Carbon\Carbon::parse($timeDisplay->startDate)->format('g:i A') }} - {{ \Carbon\Carbon::parse($timeDisplay->endDate)->format('g:i A') }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="p-3 text-center">{{ $item->quantity }}{{ $item->isVirtualAddress()  ?  $jsonPlan->subscription_type === $subscriptionType::YEARLY->value ? ' yr' : ' mon' : 'hr(s)' }}</td>
                                        <td class="p-3 text-right">{{ $item->discounts > 0 ? currencyFormatter(json_decode($item->discounts)->product_price) : currencyFormatter($item->price) }} </td>
                                        <td class="p-3 text-right">{{ $item->discounts > 0 ? currencyFormatter(json_decode($item->discounts)->product_price *  $item->quantity) : currencyFormatter($item->sub_total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals Section --}}
                    <div class="mt-6 space-y-2 text-gray-700 text-right">
                        <p><strong>Subtotal:</strong> {{ currencyFormatter($invoice->discount > 0 ? $invoice->sub_total + $invoice->discount : $invoice->sub_total) }}</p>
                        
                        {{-- Discount Information --}}
                        @if($invoice->discount > 0)
                            <p class="text-green-600">
                                <strong>Discount:</strong> 
                                -{{ currencyFormatter($invoice->discount) }}
                            </p>
                        @endif
                        @if($invoice->tax > 0)
                            <p><strong>Tax ({{ $invoice->tax }}%):</strong> {{ currencyFormatter($invoice->tax_amount) }}</p>
                        @endif
                        <p class="text-lg font-semibold text-blue-800"><strong>Total:</strong> {{ currencyFormatter($invoice->total) }}</p>
                    </div>
                </div>

                {{-- This section will only show if the invoice has event booking details --}}
                 @if($invoice->hasSubscription())
                <div class="bg-gray-50 p-6 rounded-md text-left my-8 border border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Subscription Details</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Plan Type:</strong> {{ auth()->user()->subscription('default')->plan->name }} ({{  $jsonPlan->subscription_type === $subscriptionType::YEARLY->value ? 'Billed Yearly' : 'Billed Monthly'  }})</p>
                        <p><strong>Activation Date:</strong> {{ auth()->user()->subscription('default')->created_at->format('D, M j, Y') }}</p>
                        <p><strong>Next Renewal Date:</strong> {{ auth()->user()->subscription('default')->created_at->addYear(1)->format('D, M j, Y')}}</p>
                    </div>
                </div>
                @endif
                
                <div class="text-center mt-10 print:hidden">
                    <button type="button" onclick="window.print()" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow-md inline-block">
                        <i class="fas fa-print mr-2"></i> Print Invoice
                    </button>
                </div>
            </div>
           
        </div>
    </div>
@endsection

@section('css')
    <style>
        @media print {
            .email-wrapper {
                border: none;
                box-shadow: none;
            }
            .header, .footer {
                display: none;
            }
            .print\:hidden {
                display: none;
            }
            .print\:shadow-none {
                box-shadow: none;
            }
            .print\:p-0 {
                padding: 0;
            }
            .print\:border-b-0 {
                border-bottom-width: 0;
            }
            .bg-blue-50, .border-blue-200 {
                background-color: transparent !important;
                border-color: #e5e7eb !important;
            }
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;

        }
        .email-wrapper {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #1d4ed8;
            color: #ffffff;
            padding: 10px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .content {
            padding: 32px;
        }
        
    </style>
@endsection
