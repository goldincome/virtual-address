@extends('layouts.admin')

@section('title', 'Customers Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-building mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.dashboard.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <h2 class="text-2xl font-bold text-blue-800">Customers Management</h2>

                    <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center gap-2">
                        <select name="subscription_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">All Subscription Statuses</option>
                            <option value="active" {{ request('subscription_status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="cancelled" {{ request('subscription_status') == 'cancelled' ? 'selected' : '' }}>Cancelled / Grace</option>
                            <option value="expired" {{ request('subscription_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="none" {{ request('subscription_status') == 'none' ? 'selected' : '' }}>None</option>
                        </select>

                        <select name="plan_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="">All Plans</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>

                        <div class="relative flex-1 sm:flex-initial">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                   placeholder="Search name, email, #..."
                                   class="w-full sm:w-60 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                        </div>

                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-150 text-sm font-medium">
                            <i class="fas fa-search"></i>
                        </button>

                        <a href="{{ route('admin.customers.export', request()->all()) }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-150 text-sm font-medium flex items-center gap-1">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>

                        @if(request()->anyFilled(['search', 'subscription_status', 'plan_id']))
                            <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-2">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="m-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="m-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($customers->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No customers found matching your criteria.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Subscription Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Subscription Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Companies / PSCs</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Account Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                @php
                                    $activeSub = $customer->subscriptions->first(fn($s) => $s->active() || $s->onGracePeriod());
                                    $subPlan = $activeSub ? optional($activeSub->plan)->name : ($customer->subscriptions->first() ? optional($customer->subscriptions->first()->plan)->name : '—');
                                    $subStatus = 'None';
                                    if ($activeSub) {
                                        if ($activeSub->active()) $subStatus = 'Active';
                                        elseif ($activeSub->onGracePeriod()) $subStatus = 'Grace Period';
                                    } elseif ($customer->subscriptions->count() > 0) {
                                        $subStatus = 'Cancelled / Expired';
                                    }
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">#{{ $customer->customer_number ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                        {{ $subPlan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($subStatus == 'Active') bg-green-100 text-green-800
                                            @elseif($subStatus == 'Grace Period') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $subStatus }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $customer->companies->count() }} Company(s) / {{ $customer->pscSubscriptionItems->count() }} PSC(s)
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($customer->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-900 mr-2" title="View Customer">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="text-blue-600 hover:text-blue-900 mr-2" title="Edit Customer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($customer->status === 'active')
                                            <form action="{{ route('admin.customers.suspend', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to suspend this customer?');">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 mr-2" title="Suspend Customer">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.customers.activate', $customer) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2" title="Activate Customer">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
@endsection
