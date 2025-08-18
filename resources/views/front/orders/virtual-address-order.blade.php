@extends('layouts.app')
@section('title')
    My Virtual Address Orders
@endsection

@section('page_title')
    My Virtual Address Orders
@endsection

@section('page_intro')
    Track and manage your virtual office address subscriptions and history.
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
            <h2 class="text-2xl font-semibold text-blue-800">Virtual Address Order History</h2>
            <a href="virtual-office-address.html"
                class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-sm">
                <i class="fas fa-plus mr-1"></i> New Virtual Address
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full md:min-w-[900px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Name
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Assuming that in your controller, you eager load 'order.details' and 'order.subscription' --}}
                    @foreach ($userOrderDetails as $orderDetail)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <!-- MODIFIED: This button triggers the modal with all data for the parent order -->
                                <button type="button"
                                    class="text-blue-600 hover:text-orange-600 font-medium focus:outline-none"
                                    data-ref-no="{{ $orderDetail->ref_no }}" 
                                    onclick="showOrderDetails(this)">
                                    #{{ $orderDetail->order->order_no }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $orderDetail->order->created_at->format('D, M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orderDetail->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ currencyFormatter($orderDetail->price) }}/month</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full status-active">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <!-- MODIFIED: Changed link to also trigger modal by finding the button in its row -->
                                <a href="{{ route('virtual-address-orders.order-detail', $orderDetail->ref_no) }}"
                                    class="text-blue-600 hover:text-orange-600">
                                    View Details
                                </a>
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

    @foreach ($userOrderDetails as $orderDetail)
        <!-- REVISED: Order Details Modal -->
        <div id="orderDetailsModal{{ $orderDetail->ref_no }}"
            class="my-modal fixed inset-0 z-50 items-center justify-center p-4 hidden bg-black bg-opacity-70 modal-overlay" 
            onclick="if(event.target === this) closeModal(this)">
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col transform scale-95 modal-content">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-4 border-b bg-gray-50">
                    <h3 id="modalOrderRef" class="text-xl font-semibold text-blue-800">
                        Order No: #{{ $orderDetail->order->order_no }}, 
                        Date: {{ $orderDetail->order->created_at->format('D, M j, Y') }}

                    </h3>
                    <button type="button" class="close-modal-btn text-gray-400 hover:text-gray-800 text-2xl font-bold focus:outline-none">
                        &times;
                    </button>
                </div>
                <!-- Modal Body (Scrollable) -->
                <div class="p-6 overflow-y-auto">
                    <!-- NEW: Container for the table of order items -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-blue-700 mb-3">Order Items</h4>
                        <div id="modalOrderItems" class="border rounded-lg overflow-hidden">
                            <table class="min-w-full md:min-w-[700px] divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            RefNo
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product Name
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SubTotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    {{-- Assuming that in your controller, you eager load 'order.details' and 'order.subscription' --}}
                                    @foreach ($orderDetail->order->orderDetails as $orderDetail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <!-- MODIFIED: This button triggers the modal with all data for the parent order -->
                                                <button type="button"
                                                    class="text-blue-600 hover:text-orange-600 font-medium focus:outline-none">
                                                    #{{ $orderDetail->ref_no }}
                                                </button>
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $orderDetail->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $orderDetail->quantity }} X {{ currencyFormatter($orderDetail->price) }}/ {{ $orderDetail->product_type === \App\Enums\ProductTypeEnum::VIRTUAL_ADDRESS->value ? 'Month' : 'Hour' }}
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                {{ currencyFormatter($orderDetail->sub_total)}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-blue-700 mb-3">Billing Information</h4>
                        <div class="text-sm text-gray-700 space-y-1 bg-gray-50 p-4 rounded-lg border">
                            <p><strong>{{ auth()->user()->name }}</strong></p>
                            <p>{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="mb-2">
                        <h4 class="text-lg font-semibold text-blue-700 mb-3">Payment Summary</h4>
                        <div class="border rounded-lg p-4 bg-gray-50 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="modalSubtotal" class="text-gray-800 font-medium">{{ currencyFormatter($orderDetail->order->sub_total) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax/VAT:</span>
                                <span id="modalTax" class="text-gray-800 font-medium">{{ currencyFormatter($orderDetail->order->tax) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-semibold pt-2 border-t mt-2">
                                <span class="text-blue-800">Total Paid:</span>
                                <span id="modalTotal" class="text-blue-800">{{ currencyFormatter($orderDetail->order->total) }}</span>
                            </div>
                            <p id="modalPaidOn" class="text-xs text-gray-500 pt-2"></p>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 p-4 bg-gray-50 border-t">
                    <button type="button"
                        class="close-modal-btn w-full sm:w-auto text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-5 rounded-lg transition duration-300">
                        Close
                    </button>
                    <button type="button"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-5 rounded-lg transition duration-300">
                        <i class="fas fa-print mr-2"></i> Print Invoice
                    </button>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('js')
<script>
    function showOrderDetails(button) {
        const refNo = button.dataset.refNo;
        const modal = document.getElementById(`orderDetailsModal${refNo}`);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modals when clicking outside content
    document.querySelectorAll('[id^="orderDetailsModal"]').forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal(this);
            }
        });
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            document.querySelectorAll('[id^="orderDetailsModal"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    closeModal(modal);
                }
            });
        }
    });
    //  Add close functionality to the modal close button
        const closeButtons = document.querySelectorAll('.close-modal-btn');
        closeButtons.forEach(button => {
            button.addEventListener('click', function () {
                const modal = this.closest('.my-modal');
                if (modal) {
                    closeModal(modal)
                }
            });
        });
</script>
@endsection
