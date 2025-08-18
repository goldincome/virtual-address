@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Edit Room Discount</h1>
            <a href="{{ route('admin.plan-room-discounts.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Discounts
            </a>
        </nav>
    </header>

    <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-blue-800">Editing Discount for {{ $planRoomDiscount->product->name }}</h2>
            </div>

            <form method="POST" action="{{ route('admin.plan-room-discounts.update', $planRoomDiscount) }}" class="p-8">
                @method('PUT')
                @include('admin.plan-room-discounts._form')

                <div class="mt-8">
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300">
                        Update Discount
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
