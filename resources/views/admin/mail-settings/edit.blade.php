@extends('layouts.admin')

@section('title', 'Edit Mail Setting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-tools mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.mail-settings.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Mail Settings
            </a>
        </nav>
    </header>

    <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-xl bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-blue-800">Edit Mail Setting for: <span class="text-orange-500">{{ $mailSetting->plan->name }}</span></h2>
            </div>

            <form method="POST" action="{{ route('admin.mail-settings.update', $mailSetting) }}" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-1">Plan <span class="text-red-500">*</span></label>
                    <select id="plan_id" name="plan_id" required
                            class="w-full px-4 py-2 border @error('plan_id') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        <option value="">Select a Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $mailSetting->plan_id) == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mail_type" class="block text-sm font-medium text-gray-700 mb-1">Mail Type <span class="text-red-500">*</span></label>
                    <select id="mail_type" name="mail_type" required
                            class="w-full px-4 py-2 border @error('mail_type') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        <option value="">Select a Mail Type</option>
                        @foreach($mailTypes as $type)
                            <option value="{{ $type->value }}" {{ old('mail_type', $mailSetting->mail_type->value) == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('mail_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (£) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', $mailSetting->price) }}" required step="0.01"
                           class="w-full px-4 py-2 border @error('price') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out"
                           placeholder="e.g., 5.99">
                    @error('price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="interval" class="block text-sm font-medium text-gray-700 mb-1">Billing Interval <span class="text-red-500">*</span></label>
                    <select id="interval" name="interval" required
                            class="w-full px-4 py-2 border @error('interval') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        <option value="">Select an Interval</option>
                        <option value="month" {{ old('interval', $mailSetting->interval) == 'month' ? 'selected' : '' }}>Monthly</option>
                        <option value="year" {{ old('interval', $mailSetting->interval) == 'year' ? 'selected' : '' }}>Yearly</option>
                        <option value="week" {{ old('interval', $mailSetting->interval) == 'week' ? 'selected' : '' }}>Weekly</option>
                        <option value="day" {{ old('interval', $mailSetting->interval) == 'day' ? 'selected' : '' }}>Daily</option>
                    </select>
                    @error('interval')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror   
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        <option value="1" {{ old('status', $mailSetting->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $mailSetting->status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-3">
                    <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i> Update Mail Setting
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
