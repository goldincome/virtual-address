@extends('layouts.admin')

@section('title', 'Create New Person with Significant Control Price')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-tools mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.psc-types.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to PSC Prices
            </a>
        </nav>
    </header>

    <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-xl bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-blue-800">Create New Person with Significant Control Price</h2>
                <p class="text-sm text-gray-500 mt-1">A Stripe product and prices (monthly / yearly) are created automatically.</p>
            </div>

            @if(session('error'))
                <div class="m-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.psc-types.store') }}" class="p-8 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out"
                           placeholder="e.g., Individual PSC">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="2"
                           class="w-full px-4 py-2 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out"
                           placeholder="Optional short description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price_monthly" class="block text-sm font-medium text-gray-700 mb-1">Monthly Price (£) <span class="text-red-500">*</span></label>
                        <input type="number" id="price_monthly" name="price_monthly" value="{{ old('price_monthly', '0.00') }}" required step="0.01" min="0"
                               class="w-full px-4 py-2 border @error('price_monthly') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        @error('price_monthly')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price_yearly" class="block text-sm font-medium text-gray-700 mb-1">Yearly Price (£) <span class="text-red-500">*</span></label>
                        <input type="number" id="price_yearly" name="price_yearly" value="{{ old('price_yearly', '0.00') }}" required step="0.01" min="0"
                               class="w-full px-4 py-2 border @error('price_yearly') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        @error('price_yearly')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                            class="w-full px-4 py-2 border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-3">
                    <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i> Create PSC Price
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection