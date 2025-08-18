@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Create New Room Discount</h1>
            <a href="{{ route('admin.plan-room-discounts.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Discounts
            </a>
        </nav>
    </header>

    <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-blue-800">New Plan Discount for Room</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Use this form to assign a specific discount to a meeting or conference room for subscribers of a particular plan. This allows you to offer exclusive rates to your members.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.plan-room-discounts.store') }}" class="p-8">
                @include('admin.plan-room-discounts._form')

                <div class="mt-8">
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300">
                        Save Discount
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

