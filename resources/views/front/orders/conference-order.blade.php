@extends('layouts.app')
@section('title')
    My Conference Room Bookings
@endsection

@section('page_title')
    My Conference Room Bookings
@endsection

@section('page_intro')
    Review your scheduled conference events and past bookings.
@endsection

@section('content')
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <h2 class="text-2xl font-semibold text-blue-800">Conference Room Booking History</h2>
            <a href="{{ route('conference-rooms.index') }}#room-types"
                class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-sm">
                <i class="fas fa-plus mr-1"></i> Book New Conference
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full md:min-w-[900px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked Date Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($userOrderDetails as $orderDetail)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <span class="text-sm">{{ $orderDetail->order->created_at->format('D, M j, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orderDetail->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($orderDetail->booked_date)->format('D, M j, Y') }}</br>
                                        @php
                                            $bookedTimes = json_decode($orderDetail->all_booked_time,true);
                                        @endphp
                                        @foreach($bookedTimes as $bookedTime)
                                            {{ \Carbon\Carbon::parse($bookedTime['startDate'])->format('g:i A') }}-{{ \Carbon\Carbon::parse($bookedTime['endDate'])->format('g:i A') }}</br>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ currencyFormatter($orderDetail->price) }}/hr</br> (for {{ $orderDetail->quantity}}hrs)</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-completed">
                                            Confirmed
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('conference-room-orders.show', $orderDetail->ref_no) }}" class="text-blue-600 hover:text-orange-600">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
        </div>

        <div class="mt-8 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {{ $userOrderDetails->links() }}
            </nav>
        </div>

    </div>
@endsection
