@extends('layouts.admin')

@section('title', 'Admin Profile & Security')

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

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 max-w-3xl space-y-8">
        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm" role="alert">
                <span class="block sm:inline">Password updated successfully.</span>
            </div>
        @endif

        <!-- Profile Information -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden p-6">
            <h3 class="text-xl font-bold text-blue-800 mb-4"><i class="fas fa-user-edit mr-2 text-orange-500"></i>Admin Profile Information</h3>
            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                @if($errors->updateProfile ?? false)
                    <div class="p-3 bg-red-100 text-red-700 rounded text-sm">
                        @foreach($errors->updateProfile->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div id="change-password" class="bg-white shadow-xl rounded-lg overflow-hidden p-6">
            <h3 class="text-xl font-bold text-blue-800 mb-4"><i class="fas fa-key mr-2 text-orange-500"></i>Change Password</h3>
            <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                @if($errors->updatePassword ?? false)
                    <div class="p-3 bg-red-100 text-red-700 rounded text-sm">
                        @foreach($errors->updatePassword->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
