@extends('layouts.admin')

@section('title', 'Archived Subscribers')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-building mr-2"></i> Charlton Virtual Office Admin
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.customers.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                    <i class="fas fa-users mr-1"></i> Active Subscribers
                </a>
                <a href="{{ route('admin.dashboard.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-blue-800">Archived Subscribers</h2>
                        <p class="text-xs text-gray-500 mt-1">Inactive accounts for over 3 months or manually archived accounts.</p>
                    </div>

                    <form method="GET" action="{{ route('admin.customers.archived') }}" class="flex items-center gap-2">
                        <div class="relative flex-1 sm:flex-initial">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                   placeholder="Search name, email, #..."
                                   class="w-full sm:w-60 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                        </div>

                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-150 text-sm font-medium">
                            <i class="fas fa-search"></i>
                        </button>

                        @if(!empty($search))
                            <a href="{{ route('admin.customers.archived') }}" class="text-gray-500 hover:text-gray-700 text-sm px-2">
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
                    No archived subscribers found.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Subscriber</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Registered Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">#{{ $customer->customer_number ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($customer->status) }} (Archived / Inactive)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $customer->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Subscriber">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <form action="{{ route('admin.customers.restore', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Restore this subscriber?');">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 font-medium" title="Restore Subscriber">
                                                <i class="fas fa-undo mr-1"></i> Restore
                                            </button>
                                        </form>
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
