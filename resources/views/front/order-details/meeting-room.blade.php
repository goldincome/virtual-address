@extends('layouts.app')
@section('title')
    Track and manage your virtual office address subscriptions and history.
    Meeting Room Booking Details #{{ $orderDetail->ref_no }} - NURUD
@endsection

@section('page_title')
    Meeting Room Booking Details #{{ $orderDetail->ref_no }} - NURUD
@endsection

@section('page_intro')
    Booking For #{{ $orderDetail->ref_no }}
@endsection
@section('css')
    <style>
        .status-active {
            @apply bg-green-100 text-green-800;
        }

        .status-pending {
            @apply bg-yellow-100 text-yellow-800;
        }

        .status-cancelled {
            @apply bg-red-100 text-red-800;
        }

        .status-completed {
            @apply bg-blue-100 text-blue-800;
        }
    </style>
@endsection
@section('content')
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">

        <main class="flex-grow">
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b">
                    <div>
                        <h2 class="text-2xl font-semibold text-blue-800">Booking #{{ $orderDetail->ref_no }}</h2>
                        <p class="text-sm text-gray-500">Booked on:
                            {{ $orderDetail->order->created_at->format('D, M j, Y') }}</p>
                    </div>
                    <span class="status-completed mt-2 sm:mt-0">Confirmed</span>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Booking Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
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
                                    $bookedTimes = json_decode($orderDetail->all_booked_time, true);
                                @endphp
                                @foreach ($bookedTimes as $bookedTime)
                                    {{ \Carbon\Carbon::parse($bookedTime['startDate'])->format('g:i A') }}-{{ \Carbon\Carbon::parse($bookedTime['endDate'])->format('g:i A') }}</br>
                                @endforeach
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Duration:</p>
                            <p class="text-gray-800 font-medium">{{ $orderDetail->quantity }} hours</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-500">Location:</p>
                            <p class="text-gray-800 font-medium">NURUD Woolwich Center</p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Included Amenities</h3>
                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                         @php
                            $features = json_decode($orderDetail->features, true);
                        @endphp
                        @foreach ($features as $feature)
                            <li>{{ $feature['name'] }}</li>
                        @endforeach
                    </ul>
                </div>


                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Payment Summary</h3>
                    <div class="border rounded-lg p-4 bg-gray-50 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Room Rate ({{ $orderDetail->quantity }} hours @ {{ currencyFormatter($orderDetail->price) }}/hr):</span>
                            <span class="text-gray-800">{{ currencyFormatter($orderDetail->sub_total) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">VAT :</span>
                            <span class="text-gray-800">{{ currencyFormatter($orderDetail->tax) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold pt-2 border-t mt-2">
                            <span class="text-blue-800">Total Paid:</span>
                            <span class="text-blue-800">{{ currencyFormatter($orderDetail->sub_total) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 pt-2">{{ $orderDetail->order->created_at->format('D, M j, Y') }} via {{ ucwords($orderDetail->order->payment_method) }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <a href="{{ route('meeting-room-orders.index') }}"
                        class="w-full sm:w-auto text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2.5 px-5 rounded-lg transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Bookings
                    </a>
                    <button type="button"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg transition duration-300">
                        <i class="fas fa-print mr-2"></i> Print Confirmation
                    </button>
                    {{--<button type="button"
                        class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white font-medium py-2.5 px-5 rounded-lg transition duration-300">
                        <i class="fas fa-times-circle mr-2"></i> Cancel Booking
                    </button> --}}
                </div>
                <p class="text-xs text-gray-500 mt-4">Cancellation policy applies. Please refer to terms and conditions.</p>

            </div>
        </main>
    </div>
@endsection
