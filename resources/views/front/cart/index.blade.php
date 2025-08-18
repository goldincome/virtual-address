@extends('layouts.front') 

@section('title')
    Shopping Cart - NURUD
@endsection

@section('description')
    Shopping Cart- NURUD
@endsection

@section('content')
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-bold text-blue-800 mb-8 text-center">Your Shopping Cart</h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Error</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if(session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p class="font-bold">Warning</p>
                <p>{{ session('warning') }}</p>
            </div>
        @endif

        @if(Cart::count() == 0)
            <div class="text-center bg-white p-10 rounded-lg shadow-md">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl text-gray-600 mb-6">Your cart is currently empty.</p>
                <a href="{{ url('/') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 shadow-md text-lg">
                    Continue Shopping
                </a>
            </div>
        @else
            {{-- Initialize variables for calculating total original price and discount --}}
            @php
                $totalOriginalSubtotal = 0;
                $totalDiscount = 0;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12">
                <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h2 class="text-xl font-semibold text-gray-700">Items in Cart ({{ Cart::count() }})</h2>
                        @if(Cart::count() > 0)
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?');">
                            @csrf
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium">Clear Cart</button>
                        </form>
                        @endif
                    </div>

                    @foreach($cartItems as $item)
                    @php
                        // Calculate original subtotal and discount for this item
                        $originalPrice = $item->options->product_price ?? $item->price;
                        $itemQty = ($item->options->type !== $productType::VIRTUAL_ADDRESS->value) ? count($item->options->booking_time_raw) : $item->qty;
                        $originalItemSubtotal = $originalPrice * $itemQty;
                        $itemDiscount = ($item->options->discount_amount ?? 0) * $itemQty;

                        // Add to grand totals
                        $totalOriginalSubtotal += $originalItemSubtotal;
                        $totalDiscount += $itemDiscount;
                    @endphp
                    <div class="flex flex-col sm:flex-row items-start justify-between py-4 border-b">
                        <div class="flex items-start flex-grow mb-4 sm:mb-0">
                            <img src="{{ $item->options->image ?: 'https://placehold.co/100x80/cccccc/ffffff?text=Item' }}" alt="{{ $item->name }}" class="w-24 h-20 object-cover rounded mr-4">
                            <div class="flex-grow">
                                <h3 class="font-semibold text-blue-800">{{ $item->name }}</h3>
                                
                                @if($item->options->type === $productType::VIRTUAL_ADDRESS->value)
                                    @if($subscriptionTypes::MONTHLY->value === $item->options->subscription_type)
                                        <p class="text-sm text-gray-600">{{ $subscriptionTypes::MONTHLY->label() }} Subscription (Billed Monthly)</p>
                                    @endif
                                    @if($subscriptionTypes::YEARLY->value === $item->options->subscription_type)
                                        <p class="text-sm text-gray-600">{{ $subscriptionTypes::YEARLY->label() }} Subscription Paid Annually (Billed Monthly)</p>
                                    @endif
                                @elseif($item->options->type === $productType::MEETING_ROOM->value || $item->options->type === $productType::CONFERENCE_ROOM->value)
                                    <p class="text-sm text-gray-600 mt-1">Date: {{ \Carbon\Carbon::parse($item->options->booking_date)->format('D, M j, Y') }}</p>
                                    @foreach($item->options->booking_time_display as $timeDisplay)
                                        <p class="text-sm text-gray-500"><span class="text-blue-800">Time:</span> {{ $timeDisplay }}</p>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="flex-shrink-0 sm:ml-6 w-full sm:w-auto">
                           <div class="flex sm:flex-col sm:items-end items-center justify-between">
                                <div class="text-sm text-gray-700 space-y-1 text-left sm:text-right">
                                    <div>
                                        <span class="text-gray-500">Price: </span>
                                        <span class="font-medium text-gray-800">{{ currencyFormatter($originalPrice) }}
                                            @if($item->options->type === $productType::VIRTUAL_ADDRESS->value)
                                                 {{ $item->options->subscription_type }}
                                            @else
                                                /hr
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Qty: </span>
                                        <span class="font-medium text-gray-800">{{ $itemQty }}{{ ($item->options->type !== $productType::VIRTUAL_ADDRESS->value) ? 'hr(s)' : 'mon' }}</span>
                                    </div>
                                    {{-- MODIFIED: Display original subtotal, discount, and final total --}}
                                    <div class="font-bold">
                                        <span class="text-gray-600">Subtotal: </span>
                                        <span class="text-gray-600">{{ currencyFormatter($originalItemSubtotal) }}</span>
                                    </div>
                                    @if($itemDiscount > 0)
                                    <div class="font-bold text-green-600">
                                        <span>Discount: </span>
                                        <span>-{{ currencyFormatter($itemDiscount) }}</span>
                                    </div>
                                    @endif
                                    <div class="font-bold border-t mt-1 pt-1">
                                        <span class="text-blue-800">Item Total: </span>
                                        <span class="text-blue-800">{{ currencyFormatter($item->subtotal) }}</span>
                                    </div>
                                </div>
                                <div class="mt-0 sm:mt-2">
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="rowId" value="{{ $item->rowId }}">
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition duration-150 p-1" aria-label="Remove item" title="Remove item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                           </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-6 text-right">
                        <a href="{{ url('/') }}" class="bg-blue-700 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                            <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-md sticky top-24">
                        <h2 class="text-xl font-semibold text-gray-700 mb-6 border-b pb-3">Order Summary</h2>
                        <div class="space-y-3 mb-6">
                            {{-- MODIFIED: Show original subtotal and total discount --}}
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>{{ currencyFormatter($totalOriginalSubtotal) }}</span>
                            </div>
                            @if($totalDiscount > 0)
                            <div class="flex justify-between text-green-600 font-medium">
                                <span>Special Discount</span>
                                <span>-{{ currencyFormatter($totalDiscount) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-gray-600">
                                <span>Taxes ({{ config('cart.tax') }}%)</span>
                                <span>{{ currencyFormatter(Cart::tax(2, '.', ',')) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg text-blue-900 pt-3 border-t">
                                <span>Total</span>
                                <span>{{ currencyFormatter(Cart::total(2, '.', ',')) }}</span>
                            </div>
                        </div>

                        <form action="{{ route('checkout.index') }}" method="GET">
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                                Proceed to Checkout <i class="fas fa-lock ml-2"></i>
                            </button>
                        </form>

                        <div class="mt-6 space-y-3 text-center text-sm text-gray-600">
                            <p class="flex items-center justify-center"><i class="fas fa-shield-alt text-green-500 mr-2"></i> Secure Checkout Process</p>
                            <p class="flex items-center justify-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Satisfaction Guaranteed</p>
                            <p class="flex items-center justify-center"><i class="fas fa-headset text-green-500 mr-2"></i> Support Available</p>
                        </div>

                        @php
                            $hasRoomBooking = false;
                            foreach ($cartItems as $item) {
                                if (isset($item->options['type']) && ($item->options['type'] === 'meeting_room' || $item->options['type'] === 'conference_room')) {
                                    $hasRoomBooking = true;
                                    break;
                                }
                            }
                        @endphp
                        @if($hasRoomBooking)
                        <div class="mt-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 rounded text-xs" role="alert">
                            <p><i class="fas fa-exclamation-triangle mr-1"></i> Please complete your order soon to secure your spot.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </main>
@endsection

@section('css')
<style>
    input[type='number']::-webkit-inner-spin-button,
    input[type='number']::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type='number'] {
        -moz-appearance: textfield; /* Firefox */
    }
</style>
@endsection


