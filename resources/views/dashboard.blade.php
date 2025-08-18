@extends('layouts.app')
@section('title')
    User Dashboard
@endsection

@section('description')
    User Dashboard
@endsection
@section('page_title')
    My Dashboard
@endsection
@section('content')
    <div id="dashboard-view">
        <h1 class="text-2xl sm:text-3xl font-bold text-blue-800 mb-6">Welcome to your Dashboard, {{ Auth::user()->name }}!</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-blue-700">Virtual Address Plan</h3>
                    <i class="fas fa-map-marker-alt text-3xl text-orange-400"></i>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-2xl font-bold text-blue-800">
                        @if(auth()->user()->subscribed('default') && auth()->user()->subscription('default')->plan->name) 
                            <a href="{{ route('virtual-address-orders.index') }}">{{ auth()->user()->subscription('default')->plan->name}} </a> 
                        @else
                            No Plan 
                        @endif

                    </p>
                    @if(auth()->user()->subscriptions()->exists())
                        @if(auth()->user()->subscribed('default'))
                            <span class="text-xs font-semibold text-white bg-green-500 px-3 py-1 rounded-full">Active</span>
                        @elseif(auth()->user()->subscription('default')->canceled() )
                            <span class="text-xs font-semibold text-white bg-red-500 px-3 py-1 rounded-full">Canceled</span>
                        @else
                            <span class="text-xs font-semibold text-white bg-red-500 px-3 py-1 rounded-full">Expired</span>
                        @endif
                    @else
                        <span class="text-xs font-semibold text-white bg-gray-500 px-3 py-1 rounded-full">No Subscription</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    <a href="{{ route('virtual-address-orders.index') }}" class="text-sm text-orange-600 hover:underline mt-1 block"> 
                        View Details
                    </a>
                </p>
                
                @if(auth()->user()->subscriptions()->exists())
                    @if(!auth()->user()->subscription('default')->active())
                        <div class="mt-4">
                            <a href="{{ route('subscription.renew') }}" 
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow-md transform hover:scale-105 inline-flex items-center text-sm">
                                <i class="fas fa-sync-alt mr-2"></i> Renew Subscription
                            </a>
                        </div>
                    @endif
                @else
                    <div class="mt-4">
                        <a href="{{ route('virtual-address.index') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow-md transform hover:scale-105 inline-flex items-center text-sm">
                            <i class="fas fa-sync-alt mr-2"></i> Subscribe To Virtual Address
                        </a>
                    </div>
                @endif
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-blue-700">Meeting Room Orders</h3>
                    <i class="fas fa-handshake text-3xl text-green-500"></i>
                </div>
                <p class="text-4xl font-bold text-blue-800">{{ $totalMeetingRoomOrderCount }}</p>
                <a href="{{ route('meeting-room-orders.index') }}"  class="text-sm text-orange-600 hover:underline mt-1 block">View Orders</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-blue-700">Conference Room Orders</h3>
                    <i class="fas fa-users text-3xl text-red-500"></i>
                </div>
                <p class="text-4xl font-bold text-blue-800">{{ $totalConferenceRoomOrderCount  }}</p>
                <a href="{{ route('conference-room-orders.index') }}"  class="text-sm text-orange-600 hover:underline mt-1 block">View Orders</a>
            </div>
        </div>

        <div class="mt-10 bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('profile.edit') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-300 shadow-md transform hover:scale-105 inline-flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i> Update Password
                </a>
                <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-300 shadow-md transform hover:scale-105 inline-flex items-center">
                    <i class="fas fa-user-cog mr-2"></i> Update Profile
                </a>
                
                @if(auth()->user()->subscriptions()->exists() && auth()->user()->subscription('default')->canceled())
                    <a href="{{ route('subscription.renew') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-300 shadow-md transform hover:scale-105 inline-flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Renew Subscription
                    </a>
                 @elseif(!auth()->user()->subscribed('default'))
                    <a href="{{ route('virtual-address.index') }}" class="bg-orangen-600 hover:bg-orange-700 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-300 shadow-md transform hover:scale-105 inline-flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i> Subscribe To Virtual Address Plan
                    </a>
                @endif

            </div>
        </div>
    </div>
@endsection