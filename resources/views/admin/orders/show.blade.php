@extends('layouts.admin')

@section('title', 'Order Details')

@section('css')
<style>
    /* Modal styles */
    .modal-overlay {
        transition: opacity 0.3s ease-in-out;
    }
    .modal-content {
        transition: transform 0.3s ease-in-out;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-building mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.orders.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to All Orders
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-blue-800">Order #{{ $order->order_no }}</h2>
                        <p class="text-sm text-gray-500">
                            Customer: {{ $order->user->name }} | Placed on: {{ $order->created_at->format('M j, Y') }}
                        </p>
                    </div>
                    <div class="flex gap-2 mt-4 sm:mt-0">
                        <form action="{{ route('admin.orders.approve', $order->order_no) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                        </form>
                         <form action="{{ route('admin.orders.cancel', $order->order_no) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Items List -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Order Items</h3>
                <div class="space-y-4">
                    @foreach($order->orderDetails as $item)
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-blue-700">{{ $item->name }}</p>
                            <p class="text-sm text-gray-500">
                                Item Ref: {{ $item->ref_no }} | Type: {{ Str::headline($item->type) }}
                            </p>
                        </div>
                        <!-- This button will trigger the modal -->
                        <button onclick="showItemDetails('{{ $item->ref_no }}')" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            View Details
                        </button>
                    </div>

                    <!-- Hidden Modal for each order item -->
                    <div id="modal-{{ $item->ref_no }}" class="fixed inset-0 z-50 items-center justify-center p-4 hidden bg-black bg-opacity-70 modal-overlay">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col transform scale-95 modal-content">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h3 class="text-xl font-semibold text-blue-800">Details for #{{ $item->ref_no }}</h3>
                                <button onclick="hideItemDetails('{{ $item->ref_no }}')" class="text-gray-400 hover:text-gray-800 text-2xl font-bold">&times;</button>
                            </div>
                            <div class="p-6 overflow-y-auto">
                                @if($item->type === \App\Enums\ProductTypeEnum::VIRTUAL_ADDRESS->value)
                                    {{-- Include the virtual address details view --}}
                                    @include('admin.orders.partials.virtual-address-details', ['orderDetail' => $item, 'order' => $order])
                                @else {{-- If($item->type === 'meeting-room') --}}
                                    {{-- Include the meeting room details view --}}
                                    @include('admin.orders.partials.meeting-room-details', ['orderDetail' => $item, 'order' => $order])
                                @endif
                            </div>
                            <div class="flex justify-end p-4 bg-gray-50 border-t">
                                <button onclick="hideItemDetails('{{ $item->ref_no }}')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Close</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="p-6 bg-gray-50 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Payment Summary</h3>
                <div class="max-w-sm ml-auto space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-800">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium text-gray-800">${{ number_format($order->tax, 2) }}</span>
                    </div>
                     <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium text-gray-800">{{ ucfirst($order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t text-blue-800">
                        <span>Total Paid:</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('js')
<script>
    function showItemDetails(refNo) {
        const modal = document.getElementById(`modal-${refNo}`);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.modal-content').classList.remove('scale-95');
            }, 10);
        }
    }

    function hideItemDetails(refNo) {
        const modal = document.getElementById(`modal-${refNo}`);
        if (modal) {
             modal.querySelector('.modal-content').classList.add('scale-95');
             modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    }

    // Close modal with escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    const refNo = modal.id.replace('modal-', '');
                    hideItemDetails(refNo);
                }
            });
        }
    });
</script>
@endsection
