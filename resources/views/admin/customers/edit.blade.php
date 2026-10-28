@extends('layouts.admin')

@section('title', 'Edit Customer - ' . $customer->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-2xl">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-blue-700 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold">Edit Customer: {{ $customer->name }}</h2>
                <a href="{{ route('admin.customers.show', $customer) }}" class="text-sm hover:text-orange-300">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>

            <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('status', $customer->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        Update Customer
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
