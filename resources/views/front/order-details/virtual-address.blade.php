@extends('layouts.app')
@section('title')
    Order Details
@endsection

@section('page_title')
    Virtual Address Plan Details
@endsection

@section('page_intro')
   Plan Name: {{ $plan->name }}
@endsection

@section('content')
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <h2 class="text-2xl font-semibold text-blue-800">Virtual Address Plan Details</h2>
            @if(auth()->user()->subscribed('default'))
                
            @elseif(auth()->user()->subscription('default')->canceled())
                <a href="{{ route('virtual-address.show', $plan->slug) }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-sm">
                    <i class="fas fa-plus mr-1"></i> Renew Your Subscription
                </a>
            @else
                <a href="{{ route('virtual-address.index') }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-sm">
                    <i class="fas fa-plus mr-1"></i> Subscribe To A Plan
                </a>
            @endif
        </div>

        <main class="flex-grow">
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b">
                    <div>
                        <h2 class="text-2xl font-semibold text-blue-800">Virtual Address Plan: {{ $plan->name }}</h2>
                        <p class="text-sm text-gray-500">Date: {{ auth()->user()->subscription('default')->created_at->format('D, M j, Y') }}</p>
                    </div>
                    @if(auth()->user()->subscribed('default'))
                        <span class="text-xs font-semibold text-white bg-green-500 px-3 py-1 rounded-full">Active</span>
                    @elseif(auth()->user()->subscription('default')->canceled() )
                        <span class="text-xs font-semibold text-white bg-red-500 px-3 py-1 rounded-full">Canceled</span>
                    @else
                        <span class="text-xs font-semibold text-white bg-red-500 px-3 py-1 rounded-full">Expired</span>
                    @endif
                    
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Service Details</h3>
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-800">{{ $plan->name }} Plan</span>
                            <span class="font-semibold text-gray-800">{{ currencyFormatter($plan->price) }}/Month</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">
                            @if($subscriptionInterval === $subscriptionType::YEARLY->value)
                                Subscription Period: Yearly(Billed Yearly)
                            @else
                                Subscription Period: Monthly(Billed Monthly)
                            @endif
                        </p>
                         <p class="text-sm text-gray-600 mb-1">Start Date:{{ auth()->user()->subscription('default')->created_at->format('D, M j, Y') }}</p>
                        <p class="text-sm text-gray-600 mb-1">Next Billing Date: 
                            @if($subscriptionInterval === $subscriptionType::YEARLY->value)
                                {{ $subscription ? auth()->user()->subscription('default')->created_at->addYear(1)->format('D, M j, Y') : ''}}
                            @else
                                {{ $subscription ? auth()->user()->subscription('default')->created_at->addMonth(1)->format('D, M j, Y') : ''}}
                            @endif
                        </p>
                        <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                            @foreach($plan->features as $feature)
                                <li>{{ $feature->featureSetting->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
             
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Mail Delivery Type</h3>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p><strong>{{ $mailType}}</strong></p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                   
                    <button type="button" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg transition duration-300">
                        <i class="fas fa-print mr-2"></i> View/Print Invoice
                    </button>
                     {{--<button type="button" class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white font-medium py-2.5 px-5 rounded-lg transition duration-300">
                        <i class="fas fa-times-circle mr-2"></i> Cancel Subscription
                    </button> --}}
                </div>

            </div>
        </main>

    </div>
@endsection
