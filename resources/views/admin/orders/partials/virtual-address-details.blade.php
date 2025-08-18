{{-- This partial is included in the modal on the order details page --}}
<div class="space-y-6">
    <div class="border rounded-lg p-4 bg-gray-50">
        <div class="flex justify-between items-center mb-2">
            <span class="font-medium text-gray-800">{{ $orderDetail->name }} Plan</span>
            <span class="font-semibold text-gray-800">{{ currencyFormatter($orderDetail->price) }}/Month</span>
        </div>
        <p class="text-sm text-gray-600 mb-1">Subscription Period: Yearly (Billed Monthly)</p>
        <p class="text-sm text-gray-600 mb-1">Next Billing Date: {{ optional($orderDetail->subscription)->ends_at ? \Carbon\Carbon::parse($orderDetail->subscription->ends_at)->format('D, M j, Y') : 'N/A' }}</p>
        <p class="font-medium text-gray-700 mt-3">Included Features:</p>
        @php
            $features = json_decode($orderDetail->features, true) ?? [];
        @endphp
        <ul class="list-disc list-inside text-sm text-gray-600 mt-1 space-y-1">
            @forelse($features as $feature)
                <li>{{ $feature['name'] }}</li>
            @empty
                <li>No features listed.</li>
            @endforelse
        </ul>
    </div>
</div>
