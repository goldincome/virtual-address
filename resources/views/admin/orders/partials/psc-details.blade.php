{{-- This partial is included in the modal on the order details page --}}
@php
    $pscInterval = app(\App\Services\PscService::class)->intervalForUser($orderDetail->user);
    $isYearly = $pscInterval === 'year';
@endphp
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
        <div>
            <p class="text-gray-500">PSC Type:</p>
            <p class="text-gray-800 font-medium">{{ $orderDetail->name }}</p>
        </div>
        <div>
            <p class="text-gray-500">Item Ref:</p>
            <p class="text-gray-800 font-medium">{{ $orderDetail->ref_no }}</p>
        </div>
        <div>
            <p class="text-gray-500">Total Quantity:</p>
            <p class="text-gray-800 font-medium">
                {{ $orderDetail->quantity }} {{ $isYearly ? Str::plural('Year', $orderDetail->quantity) : Str::plural('Month', $orderDetail->quantity) }}
            </p>
        </div>
        <div>
            <p class="text-gray-500">Rate:</p>
            <p class="text-gray-800 font-medium">
                {{ currencyFormatter($orderDetail->price) }}/{{ $isYearly ? 'year' : 'month' }} per person
            </p>
        </div>
        <div>
            <p class="text-gray-500">Billing Period:</p>
            <p class="text-gray-800 font-medium">{{ $isYearly ? 'Yearly' : 'Monthly' }}</p>
        </div>
    </div>
</div>