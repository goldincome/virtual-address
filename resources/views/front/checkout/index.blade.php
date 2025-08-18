@extends('layouts.front')

@section('title')
    Checkout - NURUD
@endsection

@section('description')
    Checkout - NURUD
@endsection

@section('content')
    <main class="container mx-auto px-6 py-16 md:py-10">
        @include('front.common.error-and-message')
        <h1 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Complete Your Order</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">

            <div class="md:col-span-2 bg-white p-8 rounded-lg shadow-md border border-gray-200">
                <form action="{{ route('process.payment') }}" method="POST" id="payment-form">
                    @csrf
                    <h2 class="text-2xl font-semibold text-blue-700 mb-6 border-b pb-3">1. Order Summary</h2>

                    {{-- Initialize variables for calculating total original price and discount --}}
                    @php
                        $totalOriginalSubtotal = 0;
                        $totalDiscount = 0;
                    @endphp

                    @foreach ($cartItems as $item)
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
                        <div class="flex items-start justify-between border-b py-4">
                            <div class="flex items-start flex-grow">
                                <img src="{{ $item->options->image }}" alt="{{ $item->name }}" class="w-20 h-20 object-cover rounded mr-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-blue-800">{{ $item->name }}</h4>
                                    @if ($item->options->type === $productType::VIRTUAL_ADDRESS->value)
                                        <div class="text-sm text-gray-600">Monthly Subscription: {{ $item->qty }}</div>
                                    @elseif($item->options->type === $productType::MEETING_ROOM->value || $item->options->type === $productType::CONFERENCE_ROOM->value)
                                        <div class="text-sm text-gray-600">
                                            Date: {{ \Carbon\Carbon::parse($item->options->booking_date)->format('D, M j, Y') }}
                                            @foreach ($item->options->booking_time_display as $timeDisplay)
                                                <p class="text-sm text-gray-500"><span class="text-blue-800">Time:</span> {{ $timeDisplay }} ({{ $itemQty }} hour{{ $itemQty > 1 ? 's' : '' }})</p>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="w-40 text-right text-sm">
                                <div class="text-gray-600">
                                    {{ $itemQty }} x {{ currencyFormatter($originalPrice) }}
                                </div>
                                <div class="font-semibold text-gray-800 mt-1">
                                    Subtotal: {{ currencyFormatter($originalItemSubtotal) }}
                                </div>
                                @if($itemDiscount > 0)
                                <div class="font-semibold text-green-600">
                                    Discount: -{{ currencyFormatter($itemDiscount) }}
                                </div>
                                @endif
                                <div class="font-bold text-blue-800 mt-1 border-t pt-1">
                                    Total: {{ currencyFormatter($item->subtotal) }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- MODIFIED: Order Summary Totals --}}
                    <div class="mt-6 space-y-2">
                        <div class="flex justify-between items-center text-gray-700 text-lg">
                            <span>Subtotal</span>
                            <span>{{ currencyFormatter($totalOriginalSubtotal) }}</span>
                        </div>
                        @if($totalDiscount > 0)
                        <div class="flex justify-between items-center text-green-600 font-medium text-lg">
                            <span>Special Discount</span>
                            <span>-{{ currencyFormatter($totalDiscount) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center text-gray-700 text-lg">
                            <span>Tax ({{ config('cart.tax') }}%)</span>
                            <span>{{ currencyFormatter(Cart::tax(2, '.', ',')) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-blue-800 font-bold text-xl pt-3 border-t">
                            <span>Total Due Today</span>
                            <span>{{ currencyFormatter(Cart::total(2, '.', ',')) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-3 bg-orange-100 border border-orange-300 text-orange-700 rounded-md text-sm flex items-center">
                        <i class="fas fa-clock mr-2 animate-pulse"></i>
                        <span>Limited time offer! Complete your purchase within <strong>15 minutes</strong> to secure this pricing.</span>
                    </div>

                    {{-- Billing Information Section --}}
                    <h2 class="text-2xl font-semibold text-blue-700 mt-10 mb-6 border-b pb-3">2. Billing Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                   class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('company_name') border-red-500 @else border-gray-300 @enderror" 
                                   placeholder="Your Company Inc.">
                            @error('company_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Billing Address</label>
                            <textarea name="billing_address" id="billing_address" rows="3"
                                      class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('billing_address') border-red-500 @else border-gray-300 @enderror" 
                                      placeholder="123 Business Lane, London, EC1A 1BB">{{ old('billing_address') }}</textarea>
                             @error('billing_address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Post Code </label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                   class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('postal_code') border-red-500 @else border-gray-300 @enderror" 
                                   placeholder="23E-345">
                            @error('postal_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="vat_no" class="block text-sm font-medium text-gray-700 mb-1">VAT Number (Optional)</label>
                            <input type="text" name="vat_no" id="vat_no" value="{{ old('vat_no') }}"
                                   class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('vat_no') border-red-500 @else border-gray-300 @enderror" 
                                   placeholder="GB123456789">
                            @error('vat_no')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <h2 class="text-2xl font-semibold text-blue-700 mt-10 mb-6 border-b pb-3">3. Select Payment Method</h2>
                    <div class="space-y-4">
                        @foreach ($paymentMethods::cases() as $payMethod)
                            @include('front.payment-gateways.' . $payMethod->value)
                        @endforeach
                    </div>

                    {{-- Stripe Card Element --}}
                    <div id="stripe-card-element" class="mt-4 hidden">
                        <div id="card-element" class="p-4 border rounded-lg bg-gray-50"></div>
                        <div id="card-errors" role="alert" class="text-red-600 text-sm mt-2"></div>
                    </div>

                    <div class="mt-8 text-center text-sm text-gray-500 flex items-center justify-center space-x-4">
                        <span class="secure-badge bg-green-100 text-green-700">
                            <i class="fas fa-lock mr-1"></i> SSL Secured Connection
                        </span>
                        <span class="secure-badge bg-blue-100 text-blue-700">
                            <i class="fas fa-shield-alt mr-1"></i> Verified Payment Gateway
                        </span>
                    </div>

                    <button type="submit" id="submit-button" class="mt-10 w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                        Complete Purchase <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-4">By clicking "Complete Purchase", you agree to our <a href="#" class="underline hover:text-blue-700">Terms of Service</a> and <a href="#" class="underline hover:text-blue-700">Privacy Policy</a>.</p>
                </form>
            </div>

            <div class="md:col-span-1 space-y-8">
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-center">
                    <img src="https://placehold.co/80x80/34d399/ffffff?text=?" alt="Guarantee Badge" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">Our Satisfaction Guarantee</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">We stand by our services. If you're not completely satisfied within the first 30 days, contact us for a hassle-free refund. Your business success is our priority.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-4 text-center">Why Choose NURUD?</h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i><span>Prestigious London Business Address.</span></li>
                        <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i><span>Secure Mail Handling & Forwarding.</span></li>
                        <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i><span>Flexible Plans to Suit Your Needs.</span></li>
                        <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i><span>Dedicated Customer Support.</span></li>
                    </ul>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 text-center">
                    <i class="fas fa-headset text-3xl text-blue-600 mb-3"></i>
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">Need Assistance?</h3>
                    <p class="text-gray-600 text-sm mb-4">Have questions about the checkout process or our services? Our team is here to help!</p>
                    <a href="contact-us.html" class="text-sm font-medium text-orange-600 hover:text-orange-700 hover:underline">Contact Support <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('css')
    <style>
        .payment-option {
            border: 1px solid #e5e7eb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .payment-option:hover,
        .payment-option.selected {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
        }
        footer a:not(.social-icon) {
            @apply hover:text-orange-300 transition duration-300 hover:underline;
        }
        .secure-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
@endsection

@section('js')
    <script>
    const paymentMethods = document.querySelectorAll('.payment-method');
    const stripeCardDetails = document.getElementById('stripe-card-element');
    const directDebitDetails = document.getElementById('direct-debit-details');

    function selectPaymentMethod(element) {
        const methodValue = element.dataset.method;

        paymentMethods.forEach(el => {
            el.classList.remove('selected', 'border-blue-500', 'ring', 'ring-blue-300');
            const input = el.querySelector('input[type="radio"]');
            if (input) input.checked = false;
        });

        element.classList.add('selected', 'border-blue-500', 'ring', 'ring-blue-300');
        const selectedInput = element.querySelector('input[type="radio"]');
        if (selectedInput) selectedInput.checked = true;

        if (stripeCardDetails) stripeCardDetails.classList.add('hidden');
        if (directDebitDetails) directDebitDetails.classList.add('hidden');

        if (methodValue === 'card') {
            if (stripeCardDetails) stripeCardDetails.classList.remove('hidden');
        } else if (methodValue === 'direct_debit') {
            if (directDebitDetails) directDebitDetails.classList.remove('hidden');
        }
    }

    paymentMethods.forEach(method => {
        method.addEventListener('click', () => {
            selectPaymentMethod(method);
        });
    });

</script>
@endsection
