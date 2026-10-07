<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #374151;
            margin: 0;
            padding: 0;
        }
        .page {
            padding: 30px;
        }
        .header {
            background: #1d4ed8;
            color: #ffffff;
            padding: 20px 24px;
            border-radius: 6px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #dbeafe;
        }
        h2 {
            font-size: 16px;
            color: #1e3a8a;
            margin: 24px 0 12px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .summary-table td:last-child {
            text-align: right;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .order-table th {
            background: #dbeafe;
            color: #1e3a8a;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #93c5fd;
            font-size: 11px;
        }
        .order-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .order-table td.right {
            text-align: right;
        }
        .order-table td.center {
            text-align: center;
        }
        .totals {
            margin-top: 16px;
            text-align: right;
        }
        .totals p {
            margin: 4px 0;
        }
        .totals .total {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .totals .discount {
            color: #059669;
        }
        .info-block {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 14px 16px;
            margin-top: 20px;
        }
        .info-block h3 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #1e3a8a;
        }
        .info-block p {
            margin: 3px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 14px;
            border-top: 2px solid #1e3a8a;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Charlton Virtual Office</h1>
            <p>Unit 6, Block 3, Dockyard Industrial Estate, Charlton, London. SE18 5PQ</p>
            <p>www.charltonvirtualoffice.com</p>
        </div>

        <h2>Invoice / Payment Receipt</h2>

        <table class="summary-table">
            <tr>
                <td>
                    <strong>Invoice No:</strong> #{{ $order->order_no }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->paid_at ?? $order->created_at)->format('F j, Y, g:i a') }}<br>
                    <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}
                </td>
                <td>
                    <strong>Client Name:</strong> {{ $order->user->name }}<br>
                    <strong>Client Email:</strong> {{ $order->user->email }}
                </td>
            </tr>
        </table>

        <table class="order-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="center">Qty</th>
                    <th class="right">Price</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $orderDetail)
                    <tr>
                        <td>
                            <strong>{{ $orderDetail->name }}</strong>
                            @if ($orderDetail->isMeetingRoom() || $orderDetail->isConferenceRoom())
                                <br>
                                <small>
                                    Date: {{ \Carbon\Carbon::parse($orderDetail->booked_date)->format('D, M j, Y') }}<br>
                                    @foreach (json_decode($orderDetail->all_booked_time) as $timeDisplay)
                                        {{ \Carbon\Carbon::parse($timeDisplay->startDate)->format('g:i A') }} - {{ \Carbon\Carbon::parse($timeDisplay->endDate)->format('g:i A') }}<br>
                                    @endforeach
                                </small>
                            @endif
                        </td>
                        <td class="center">
                            {{ $orderDetail->quantity }}{{ $orderDetail->qtyUnitLabel() }}
                        </td>
                        <td class="right">
                            {{ $orderDetail->discounts > 0 ? currencyFormatter(json_decode($orderDetail->discounts)->product_price) : currencyFormatter($orderDetail->price) }}
                        </td>
                        <td class="right">
                            {{ $orderDetail->discounts > 0 ? currencyFormatter(json_decode($orderDetail->discounts)->product_price * $orderDetail->quantity) : currencyFormatter($orderDetail->sub_total) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p><strong>Subtotal:</strong> {{ currencyFormatter($order->discount > 0 ? $order->sub_total + $order->discount : $order->sub_total) }}</p>
            @if (isset($order->discount) && $order->discount > 0)
                <p class="discount"><strong>Discount:</strong> -{{ currencyFormatter($order->discount) }}</p>
            @endif
            @if ($order->tax > 0)
                <p><strong>Tax:</strong> {{ currencyFormatter($order->tax) }}</p>
            @endif
            <p class="total"><strong>Total:</strong> {{ currencyFormatter($order->total) }}</p>
        </div>

        @if ($order->hasSubscription() && $jsonPlan)
            <div class="info-block">
                <h3>Subscription Details</h3>
                <p><strong>Plan:</strong> {{ $jsonPlan->name ?? $order->user->subscription('default')->plan->name }}</p>
                <p><strong>Billing:</strong> {{ $jsonPlan->subscription_type === $subscriptionType::YEARLY->value ? 'Yearly' : 'Monthly' }}</p>
                <p><strong>Activation Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('D, M j, Y') }}</p>
                <p>
                    <strong>Next Renewal Date:</strong>
                    {{ $jsonPlan->subscription_type === $subscriptionType::YEARLY->value
                        ? \Carbon\Carbon::parse($order->created_at)->addYear(1)->format('D, M j, Y')
                        : \Carbon\Carbon::parse($order->created_at)->addMonth(1)->format('D, M j, Y') }}
                </p>
            </div>
        @endif

        <div class="footer">
            &copy; {{ date('Y') }} Charlton Virtual Office. All Rights Reserved. | Unit 6, Block 3, Dockyard Industrial Estate, Charlton, London. SE18 5PQ
        </div>
    </div>
</body>
</html>