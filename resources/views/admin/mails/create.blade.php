@extends('layouts.admin')

@section('title', 'Create New Mail Record')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-envelope-open-text mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.mails.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Mail List
            </a>
        </nav>
    </header>

    <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-blue-800">Create New Mail Record for {{ $selectedUser->name }}</h2>
                <p class="text-xs text-gray-500 mt-1">Mail Type: <span class="font-semibold text-blue-600">{{ $userMailPlanSetting->mail_type?->label() }}</span> (Unit Price: £{{ number_format($userMailPlanSetting->price, 2) }})</p>
            </div>

            <form method="POST" action="{{ route('admin.mails.store') }}" class="p-8 space-y-6">
                @csrf

                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                <input type="hidden" name="mail_type" value="{{ $userMailPlanSetting->mail_type->value }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mail_status" class="block text-sm font-medium text-gray-700 mb-1">Mail Status <span class="text-red-500">*</span></label>
                        <select id="mail_status" name="mail_status" required class="w-full px-4 py-2 border @error('mail_status') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            <option value="pending" {{ old('mail_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            @if($userMailPlanSetting && $userMailPlanSetting->mail_type)
                                @if($userMailPlanSetting->mail_type->value === \App\Enums\MailTypeEnum::Scanned->value)
                                    <option value="scanned" {{ old('mail_status') == 'scanned' ? 'selected' : '' }}>Scanned</option>
                                @elseif($userMailPlanSetting->mail_type->value === \App\Enums\MailTypeEnum::Forwarded->value)
                                    <option value="forwarded" {{ old('mail_status') == 'forwarded' ? 'selected' : '' }}>Forwarded</option>
                                @endif
                            @endif
                        </select>
                        @error('mail_status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Total Number of Letters <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="w-full px-4 py-2 border @error('quantity') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="1">
                        @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Brief description of the mail content...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center text-sm text-blue-800">
                    <div>
                        <span class="font-medium">Unit Price:</span> £<span id="unit-price">{{ number_format($userMailPlanSetting->price, 2) }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Total Amount:</span> £<span id="total-amount" class="font-bold text-lg">{{ number_format($userMailPlanSetting->price, 2) }}</span>
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i> Create Mail Record & Charge Usage
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quantityInput = document.getElementById('quantity');
        const unitPrice = parseFloat('{{ $userMailPlanSetting->price }}');
        const totalAmountSpan = document.getElementById('total-amount');

        function updateTotal() {
            const qty = parseInt(quantityInput.value) || 1;
            const total = (unitPrice * qty).toFixed(2);
            totalAmountSpan.textContent = total;
        }

        quantityInput.addEventListener('input', updateTotal);
        updateTotal();
    });
</script>
@endsection
