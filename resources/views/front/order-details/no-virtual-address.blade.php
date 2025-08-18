@extends('layouts.app')
@section('title')
    Order Details
@endsection

@section('page_title')
    Virtual Address Plan Details
@endsection

@section('page_intro')
   Plan Name: Subscribe to a Virtual Address Plan
@endsection

@section('content')
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <h2 class="text-2xl font-semibold text-blue-800">Subscribe To A Virtual Address Plan</h2>
           
        </div>

        <main class="flex-grow">
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Virtual Address Plan</h3>
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-800"><p>You have no virtual address plan</p></span>
                            <br/><br/>

                        </div>
                        <p class="text-sm text-gray-600 mb-1">
                            @if(auth()->user()->subscribed('default'))
                
                            @elseif(auth()->user()->subscriptions()->exists() && auth()->user()->subscription('default')->canceled())
                                <a href="{{ route('virtual-address.show', $plan->slug) }}"
                                    class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-sm">
                                    <i class="fas fa-plus mr-1"></i> Renew Your Subscription
                                </a>
                            @else
                                <a href="{{ route('virtual-address.index') }}"
                                    class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-sm">
                                    <i class="fas fa-plus mr-1"></i> Click To Subscribe To A Plan
                                </a>
                            @endif
                        </p>
                        
                    </div>
                </div>
                
            </div>
        </main>

    </div>
@endsection
