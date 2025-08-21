<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/fQc228F/favicon.png">
    <title>Order Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
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
            padding: 24px;
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
        .footer {
            background-color: #1e3a8a;
            color: #dbeafe;
            padding: 24px;
            text-align: center;
            font-size: 12px;
        }
        .footer a {
            color: #93c5fd;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <h1>Nurud Virtual Offices</h1>
            </div>
            <div class="content">
                <div class="text-center mb-8">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h2 class="text-2xl font-bold text-green-600 mt-4">Order Created!</h2>
                    <p class="text-gray-600 mt-2">Hello {{ $order->user->name }}, thank you for your purchase.</p>
                    <p class="text-gray-600 mt-2">Below are your order details, you can also click on "View Order Details" to access and view status of your order on our platform.</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-md text-left my-8 border border-blue-200">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Order Invoice/Summary</h3>
                    <div class="space-y-2 text-gray-700 mb-6">
                        <p><strong>Order No:</strong> #{{ $order->order_no }}</p>
                        <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                        <p><strong>Client Name:</strong> {{ $order->user->name }}</p>
                        <p><strong>Client Email:</strong> {{ $order->user->email }}</p>
                    </div>

                    {{-- Order Items Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-700 border-collapse">
                            <thead class="bg-blue-100 text-blue-800 font-semibold">
                                <tr>
                                    <th class="p-3 border-b">Product</th>
                                    <th class="p-3 border-b text-center">Qty</th>
                                    <th class="p-3 border-b text-right">Price</th>
                                    <th class="p-3 border-b text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $orderDetail)
                                    <tr class="border-b">
                                        <td class="p-3">
                                            <strong>{{ $orderDetail->name }}</strong>
                                            @if($orderDetail->isMeetingRoom() || $orderDetail->isConferenceRoom())
                                                <div class="space-y-2 text-gray-700">
                                                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($orderDetail->booked_date)->format('D, M j, Y') }}<br/>
                                                    <strong>Time Slot:</strong><br/>
                                                         @foreach(json_decode($orderDetail->all_booked_time) as $timeDisplay)
                                                            {{ \Carbon\Carbon::parse($timeDisplay->startDate)->format('g:i A') }} - {{ \Carbon\Carbon::parse($timeDisplay->endDate)->format('g:i A') }}<br/>
                                                        @endforeach
                                                    </p>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center">
                                            {{ $orderDetail->quantity }}{{ $orderDetail->isVirtualAddress()  ?  $jsonPlan->subscription_type === $subscriptionType::YEARLY->value ? ' yr' : ' mon' : 'hr(s)' }}
                                        </td>
                                        <td class="p-3 text-right">
                                            {{ $orderDetail->discounts > 0 ? currencyFormatter(json_decode($orderDetail->discounts)->product_price) : currencyFormatter($orderDetail->price) }} 
                                        </td>
                                        <td class="p-3 text-right">
                                            {{ $orderDetail->discounts > 0 ? currencyFormatter(json_decode($orderDetail->discounts)->product_price *  $orderDetail->quantity) : currencyFormatter($orderDetail->sub_total) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals Section --}}
                    <div class="mt-6 space-y-2 text-gray-700 text-right">
                        <p><strong>Subtotal: </strong>{{ currencyFormatter($order->discount > 0 ? $order->sub_total + $order->discount : $order->sub_total) }}</p>
                        
                        {{-- Discount Information --}}
                        @if(isset($order->discount) && $order->discount > 0)
                            <p class="text-green-600">
                                <strong>Discount 
                                @if(isset($order->discount_percentage))
                                    ({{ $order->discount_percentage }}%)
                                @endif
                                :</strong> 
                                -{{ currencyFormatter($order->discount) }}
                            </p>
                        @endif
                        @if($order->tax > 0)
                            <p><strong>Tax:</strong> {{ currencyFormatter($order->tax) }}</p>
                        @endif
                        <p class="text-lg font-semibold text-blue-800"><strong>Total:</strong> {{ currencyFormatter($order->total) }}</p>
                    </div>
                </div>

                {{-- Subscription Details Section --}}
                {{-- This section will only show if the order has subscription details --}}
                @if($order->hasSubscription())
                <div class="bg-gray-50 p-6 rounded-md text-left my-8 border border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Subscription Details</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Plan Type:</strong> {{ $order->user->subscription('default')->plan->name }} ({{  $jsonPlan->subscription_type === $subscriptionType::YEARLY->value ? 'Billed Yearly' : 'Billed Monthly'  }})</p>
                        <p><strong>Activation Date:</strong> {{ $order->user->subscription('default')->created_at->format('D, M j, Y') }}</p>
                        <p><strong>Next Renewal Date:</strong> {{ $order->user->subscription('default')->created_at->addYear(1)->format('D, M j, Y')}}</p>
                    </div>
                </div>
                @endif

                {{-- Event Booking Details Section --}}
                {{-- This section will only show if the order has event booking details --}}
                @if(isset($order->eventBooking))
                <div class="bg-gray-50 p-6 rounded-md text-left my-8 border border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Event Booking Details</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Event/Room:</strong> {{ $order->eventBooking->room_name }}</p>
                        <p><strong>Date:</strong> {{ $order->eventBooking->event_date->format('F j, Y') }}</p>
                        <p><strong>Time Slot:</strong> {{ $order->eventBooking->start_time->format('g:i A') }} - {{ $order->eventBooking->end_time->format('g:i A') }} ({{ $order->eventBooking->hours_booked }} hours)</p>
                    </div>
                </div>
                @endif

                <div class="text-center mt-10">
                    <a href="{{ route('invoices.show', $order->order_no) }}" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow-md inline-block">
                        View Order Details
                    </a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} NURUD Virtual Offices. All Rights Reserved.</p>
                <p class="mt-2">
                    <a href="{{ url('/contact-us') }}">Contact Support</a> | 
                    <a href="{{ url('/') }}">Visit our Website</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
