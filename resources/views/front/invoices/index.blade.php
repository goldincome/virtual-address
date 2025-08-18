@extends('layouts.app')
@section('title')
    My Invoice History
@endsection

@section('page_title')
    Invoice History
@endsection

@section('page_intro')
    View and manage your Invoice History.
@endsection

@section('css')
    <style>
        /* Modal styles */
        .modal-overlay {
            transition: opacity 0.3s ease-in-out;
        }

        .modal-content {
            transition: transform 0.3s ease-in-out;
        }

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
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <h2 class="text-2xl font-semibold text-blue-800">Invoice History</h2>
            
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full md:min-w-[900px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Qty</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amt</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('invoices.show',$order->order_no) }}"
                                            class="text-blue-600 hover:text-orange-600 font-medium focus:outline-none">
                                            #{{ $order->order_no }}
                                        </a></br>
                                        <span class="text-sm">{{ $order->created_at->format('D, M j, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->orderDetails()->sum('quantity') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                         {{ currencyFormatter($order->total) }} 
                                        
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @foreach ($paymentMethods::cases()  as $paymentMethod)
                                            @if($paymentMethod->value == $order->payment_method)
                                                {{ $paymentMethod->label() }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-completed">
                                            @foreach($orderStatuses::cases() as $orderStatus)
                                                @if($orderStatus->value == $order->status)
                                                    {{ $orderStatus->customerLabel() }}
                                                @endif
                                            @endforeach
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('invoices.show', $order->order_no) }}" class="text-blue-600 hover:text-orange-600">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
        </div>

        <div class="mt-8 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {{ $orders->links() }}
            </nav>
        </div>

    </div>
@endsection
