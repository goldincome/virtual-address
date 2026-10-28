@extends('layouts.admin')

@section('title', 'Customer Detail - ' . $customer->name)

@section('content')
<div class="min-h-screen bg-gray-50 pb-12">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.customers.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-building mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.customers.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Customers
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header / Actions Card -->
        <div class="bg-white shadow-lg rounded-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-blue-900">{{ $customer->name }}</h2>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Customer Number: <span class="font-medium text-gray-700">#{{ $customer->customer_number ?? 'N/A' }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.customers.edit', $customer) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
                    <i class="fas fa-edit mr-1"></i> Edit Profile
                </a>
                @if($customer->status === 'active')
                    <form action="{{ route('admin.customers.suspend', $customer) }}" method="POST" onsubmit="return confirm('Suspend this customer?');">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
                            <i class="fas fa-ban mr-1"></i> Suspend
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.customers.activate', $customer) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
                            <i class="fas fa-check-circle mr-1"></i> Activate
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Contact Info -->
            <div class="bg-white shadow rounded-lg p-6 space-y-3">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2"><i class="fas fa-user mr-2 text-blue-600"></i>Contact Information</h3>
                <div>
                    <span class="text-xs text-gray-500 block">Email Address</span>
                    <span class="text-sm font-medium text-gray-900">{{ $customer->email }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Phone Number</span>
                    <span class="text-sm font-medium text-gray-900">{{ $customer->phone ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Registered Date</span>
                    <span class="text-sm font-medium text-gray-900">{{ $customer->created_at->format('M d, Y H:i') }}</span>
                </div>
            </div>

            <!-- Subscription Summary -->
            <div class="bg-white shadow rounded-lg p-6 space-y-3">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2"><i class="fas fa-star mr-2 text-orange-500"></i>Subscription Summary</h3>
                @php
                    $activeSub = $customer->subscriptions->first(fn($s) => $s->active() || $s->onGracePeriod());
                @endphp
                @if($activeSub)
                    <div>
                        <span class="text-xs text-gray-500 block">Active Plan</span>
                        <span class="text-sm font-semibold text-blue-900">{{ optional($activeSub->plan)->name ?? 'Virtual Address Plan' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Stripe Status</span>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($activeSub->stripe_status) }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Renews / Ends At</span>
                        <span class="text-sm font-medium text-gray-900">{{ $activeSub->ends_at ? $activeSub->ends_at->format('M d, Y') : 'Active (Auto-renewing)' }}</span>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No active subscription found.</p>
                @endif
            </div>

            <!-- Mail & Services -->
            <div class="bg-white shadow rounded-lg p-6 space-y-3">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2"><i class="fas fa-envelope mr-2 text-indigo-600"></i>Mail & Services</h3>
                <div>
                    <span class="text-xs text-gray-500 block">Mail Service Type</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $mailType }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Total Mails Processed</span>
                    <span class="text-sm font-medium text-gray-900">{{ $customer->mails->count() }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Mail Usages Recorded</span>
                    <span class="text-sm font-medium text-gray-900">{{ $customer->mailUsages->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Companies & PSCs -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-building mr-2 text-blue-600"></i>Registered Companies & PSCs</h3>
            </div>
            @if($customer->companies->isEmpty())
                <div class="p-6 text-center text-gray-500 text-sm">No registered companies found for this customer.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Company Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Registration No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">PSCs</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->companies as $company)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $company->company_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $company->company_number ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $company->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($company->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $company->companyPscs->count() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.companies.show', $company) }}" class="text-indigo-600 hover:text-indigo-900">View Company</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Order History -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-shopping-cart mr-2 text-orange-500"></i>Customer Orders</h3>
            </div>
            @if($customer->orders->isEmpty())
                <div class="p-6 text-center text-gray-500 text-sm">No orders found for this customer.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Order No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_no }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ currencyFormatter($order->total) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                            @if($order->status == 'completed') bg-green-100 text-green-800
                                            @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">View Order</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection
