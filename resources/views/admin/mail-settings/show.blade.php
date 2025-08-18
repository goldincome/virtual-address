@extends('layouts.admin')

@section('title', 'View Mail Setting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-tools mr-2"></i> NURUD Admin
            </a>
            <a href="{{ route('admin.mail-settings.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Mail Settings
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-blue-800">Mail Setting Details</h2>
                <p class="text-gray-600 mt-1">Viewing details for mail setting associated with plan: <span class="font-semibold">{{ $mailSetting->plan->name }}</span></p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Plan</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mailSetting->plan->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Mail Type</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mailSetting->mail_type->label() }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Price</h3>
                            <p class="mt-1 text-lg text-orange-600 font-semibold">£{{ number_format($mailSetting->price, 2) }}</p>
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1 text-lg text-gray-900">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $mailSetting->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $mailSetting->status ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mailSetting->created_at->format('M d, Y \a\t H:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mailSetting->updated_at->format('M d, Y \a\t H:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.mail-settings.edit', $mailSetting) }}"
                       class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-edit mr-2"></i> Edit Setting
                    </a>
                    <form action="{{ route('admin.mail-settings.destroy', $mailSetting) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this mail setting? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-300">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
