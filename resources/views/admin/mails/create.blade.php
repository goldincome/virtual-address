@extends('layouts.admin')

@section('title', 'Create New Mail Record')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-envelope-open-text mr-2"></i> NURUD Admin
            </a>
            <a href="{{ route('admin.mails.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Mail List
            </a>
        </nav>
    </header>

    <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-blue-800">Create New {{ $mailTypes::tryFrom($userMailPlanSetting->mail_type->value)->label() }} Record</h2>
            </div>

            <form method="POST" action="{{ route('admin.mails.store') }}" class="p-8 space-y-6" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Recipient (User) </label>
                        
                        {{ $selectedUser->name }} ({{ $selectedUser->email }})
                        <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                        @error('user_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-1">Sender Name <span class="text-red-500">*</span></label>
                        <input type="text" id="sender_name" name="sender_name" value="{{ old('sender_name') }}" required class="w-full px-4 py-2 border @error('sender_name') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., Amazon, John Doe">
                        @error('sender_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Brief description of the mail content...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="mail_type" class="block text-sm font-medium text-gray-700 mb-1">Mail Type <span class="text-red-500">*</span></label>
                    <select id="mail_type" name="mail_type" required class="w-full px-4 py-2 border @error('mail_type') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
                        @foreach($mailTypes::cases() as $type)
                            @if($userMailPlanSetting && $userMailPlanSetting->mail_type->value === $type->value)
                                <option value="{{ $type->value }}" {{ $userMailPlanSetting->mail_type->value === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option> 
                            @endif
                        @endforeach
                    </select>
                    @error('mail_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div id="scanned-file-container" class="hidden">
                    <label for="scanned_file" class="block text-sm font-medium text-gray-700 mb-1">Upload Scanned File (PDF, JPG, PNG) <span class="text-red-500">*</span></label>
                    <input type="file" id="scanned_file" name="scanned_file" class="w-full px-4 py-2 border @error('scanned_file') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
                    @error('scanned_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>


                <div class="pt-3">
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i> Create Mail Record
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
        const mailTypeSelect = document.getElementById('mail_type');
        const scannedFileContainer = document.getElementById('scanned-file-container');
        const scannedFileInput = document.getElementById('scanned_file');

        function toggleScannedFileField() {
            if (mailTypeSelect.value === '{{ \App\Enums\MailTypeEnum::Scanned->value }}') {
                scannedFileContainer.classList.remove('hidden');
                scannedFileInput.required = true;
            } else {
                scannedFileContainer.classList.add('hidden');
                scannedFileInput.required = false;
            }
        }

        // Initial check on page load
        toggleScannedFileField();

        // Add event listener for changes
        mailTypeSelect.addEventListener('change', toggleScannedFileField);
    });
</script>
@endsection
