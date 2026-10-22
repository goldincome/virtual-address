@extends('layouts.front')

@section('title')
    Company Top-Up Checkout - Charlton Virtual Office
@endsection

@section('description')
    Company Top-Up Checkout - Charlton Virtual Office
@endsection

@section('content')
    <main class="container mx-auto px-6 py-16 md:py-10">
        @include('front.common.error-and-message')
        <h1 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Company Top-Up Checkout</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="md:col-span-2 bg-white p-8 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-2xl font-semibold text-blue-700 mb-6 border-b pb-3">Order Summary</h2>

                <div class="space-y-4">
                    @foreach ($cartItems as $item)
                        <div class="flex items-start justify-between border-b py-4">
                            <div class="flex items-start flex-grow">
                                <img src="{{ $item->options->image ?: 'https://placehold.co/100x80/1e4ed8/ffffff?text=Company' }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded mr-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-blue-800">{{ $item->name }}</h4>
                                    <p class="text-sm text-gray-600">Additional Company</p>
                                    <p class="text-xs text-orange-600 mt-1">Billed via your subscription and pro-rated for the current period.</p>
                                </div>
                            </div>
                            <div class="text-right text-sm">
                                <div class="text-gray-600">{{ $item->qty }} x {{ currencyFormatter($item->price) }} /company</div>
                                <div class="font-semibold text-gray-800 mt-1">Per {{ $interval === 'year' ? 'year' : 'month' }}</div>
                                <div class="font-bold text-blue-800 mt-1 border-t pt-1">Total: {{ currencyFormatter($item->subtotal) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 space-y-2">
                    <div class="flex justify-between items-center text-gray-700 text-lg">
                        <span>Subtotal</span>
                        <span>{{ currencyFormatter(Cart::subtotal(2, '.', '')) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-700 text-lg">
                        <span>Tax ({{ config('cart.tax') }}%)</span>
                        <span>{{ currencyFormatter(Cart::tax(2, '.', '')) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-blue-800 font-bold text-xl pt-3 border-t">
                        <span>Total Due Today</span>
                        <span>{{ currencyFormatter(Cart::total(2, '.', '')) }}</span>
                    </div>
                </div>

                <div class="mt-6 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-md text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    The amount is charged to the payment method on your existing subscription. The new company slots take effect immediately and the full new quantity is billed at your next renewal.
                </div>

                <h2 class="text-2xl font-semibold text-blue-700 mt-10 mb-6 border-b pb-3">Billing Information</h2>
                @if ($billing)
                    <div class="space-y-2 text-gray-700">
                        @if ($billing->company_name)
                            <p><span class="font-medium text-gray-500">Company:</span> {{ $billing->company_name }}</p>
                        @endif
                        <p><span class="font-medium text-gray-500">Billing Address:</span> {{ $billing->billing_address }}</p>
                        <p><span class="font-medium text-gray-500">Post Code:</span> {{ $billing->postal_code }}</p>
                        @if ($billing->vat_no)
                            <p><span class="font-medium text-gray-500">VAT Number:</span> {{ $billing->vat_no }}</p>
                        @endif
                        <p class="text-xs text-gray-500">This is charged through your Stripe subscription account.</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Your billing details are taken from your Stripe subscription account.</p>
                @endif

                <div class="mt-8 text-center text-sm text-gray-500 flex items-center justify-center space-x-4">
                    <span class="secure-badge bg-green-100 text-green-700">
                        <i class="fas fa-lock mr-1"></i> SSL Secured Connection
                    </span>
                    <span class="secure-badge bg-blue-100 text-blue-700">
                        <i class="fas fa-shield-alt mr-1"></i> Verified Payment Gateway
                    </span>
                </div>

                <form action="{{ route('companies.company.topup.pay') }}" method="POST" class="mt-10">
                    @csrf
                    <button type="submit" id="submit-button" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                        Pay Now <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-4">By clicking "Pay Now", you agree to our <a href="#" class="underline hover:text-blue-700">Terms of Service</a> and <a href="#" class="underline hover:text-blue-700">Privacy Policy</a>.</p>
                </form>

                <form action="{{ route('companies.company.topup.cancel') }}" method="POST" class="mt-4 text-center">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 hover:underline">
                        <i class="fas fa-times mr-1"></i> Cancel &amp; Clear
                    </button>
                </form>
            </div>

            <div class="md:col-span-1 space-y-8">
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-center">
                    <img src="https://placehold.co/80x80/34d399/ffffff?text=? " alt="Guarantee Badge" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">Our Satisfaction Guarantee</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">We stand by our services. If you're not completely satisfied within the first 30 days, contact us for a hassle-free refund. Your business success is our priority.</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 text-center">
                    <i class="fas fa-headset text-3xl text-blue-600 mb-3"></i>
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">Need Assistance?</h3>
                    <p class="text-gray-600 text-sm mb-4">Have questions about adding companies? Our team is here to help!</p>
                    <a href="{{ route('contact-us.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700 hover:underline">Contact Support <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('css')
    <style>
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
