@extends('layouts.admin')

@section('title', 'Create New Off Date')

@section('content')
<div class="min-h-screen bg-gray-50">
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-blue-800">Add New Off Date</h2>
                <a href="{{ route('admin.off-days.index') }}" class="text-sm text-gray-600 hover:text-blue-700 transition duration-300">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Off Date List
                </a>
            </div>

            <form action="{{ route('admin.off-days.store') }}" method="POST" class="p-8 space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From <span class="text-red-500">*</span></label>
                        <input type="date" name="date_from" id="date_from" value="{{ old('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                        @error('date_from')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To <span class="text-red-500">*</span></label>
                        <input type="date" name="date_to" id="date_to" value="{{ old('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                        @error('date_to')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }} class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    <label for="status" class="ml-2 block text-sm text-gray-900">Set as Active</label>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Save Holiday
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection