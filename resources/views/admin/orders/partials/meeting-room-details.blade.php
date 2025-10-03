{{-- This partial is included in the modal on the order details page --}}
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
        <div>
            <p class="text-gray-500">Room Name:</p>
            <p class="text-gray-800 font-medium">{{ $orderDetail->name }}</p>
        </div>
        <div>
            <p class="text-gray-500">Booked Date:</p>
            <p class="text-gray-800 font-medium">
                {{ \Carbon\Carbon::parse($orderDetail->booked_date)->format('D, M j, Y') }}</p>
        </div>
        <div>
            <p class="text-gray-500">Booked Time:</p>
            <p class="text-gray-800 font-medium">
                @php
                    $bookedTimes = json_decode($orderDetail->all_booked_time, true) ?? [];
                @endphp
                @foreach ($bookedTimes as $bookedTime)
                    {{ \Carbon\Carbon::parse($bookedTime['startDate'])->format('g:i A') }}-{{ \Carbon\Carbon::parse($bookedTime['endDate'])->format('g:i A') }}<br>
                @endforeach
            </p>
        </div>
        <div>
            <p class="text-gray-500">Total Duration:</p>
            <p class="text-gray-800 font-medium">{{ $orderDetail->quantity }} hours</p>
        </div>
        <div>
            <p class="text-gray-500">Location:</p>
            <p class="text-gray-800 font-medium">Charlton Virtual Office Woolwich Center</p>
        </div>
    </div>

    <div>
        <h4 class="text-md font-semibold text-blue-700 mb-2">Included Amenities</h4>
        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
             @php
                $features = json_decode($orderDetail->features, true) ?? [];
            @endphp
            @forelse ($features as $feature)
                <li>{{ $feature['name'] }}</li>
            @empty
                <li>No amenities listed.</li>
            @endforelse
        </ul>
    </div>
</div>

