@extends('layouts.admin')

@section('title', 'View Mail Record')

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

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-blue-800">Mail Record #{{ $mail->id }}</h2>
                <p class="text-gray-600 mt-1">Details for mail received from <span class="font-semibold">{{ $mail->sender_name }}</span> for <span class="font-semibold">{{ $mail->user->name }}</span>.</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Column 1 -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Recipient (User)</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mail->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $mail->user->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Sender Name</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mail->sender_name }}</p>
                        </div>
                         <div>
                            <h3 class="text-sm font-medium text-gray-500">Price</h3>
                            <p class="mt-1 text-lg text-orange-600 font-semibold">£{{ number_format($mail->price, 2) }}</p>
                        </div>
                    </div>
                    <!-- Column 2 -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Mail Status</h3>
                            <p class="mt-1 text-lg text-gray-900">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    @switch($mail->mail_status)
                                        @case(\App\Enums\MailStatusEnum::Pending) bg-yellow-100 text-yellow-800 @break
                                        @case(\App\Enums\MailStatusEnum::Paid) bg-blue-100 text-blue-800 @break
                                        @case(\App\Enums\MailStatusEnum::Scanned) bg-green-100 text-green-800 @break
                                        @case(\App\Enums\MailStatusEnum::Forwarded) bg-purple-100 text-purple-800 @break
                                    @endswitch">
                                    {{ $mail->mail_status->label() }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Payment Status</h3>
                            <p class="mt-1 text-lg text-gray-900">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    @switch($mail->payment_status)
                                        @case(\App\Enums\PaymentStatusEnum::Pending) bg-yellow-100 text-yellow-800 @break
                                        @case(\App\Enums\PaymentStatusEnum::Paid) bg-green-100 text-green-800 @break
                                        @case(\App\Enums\PaymentStatusEnum::Failed) bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800 @break
                                    @endswitch">
                                    {{ $mail->payment_status->label() }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <!-- Column 3 -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Received At</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mail->recieved_at ? $mail->recieved_at->format('M d, Y, H:i A') : 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Scanned At</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mail->scanned_at ? $mail->scanned_at->format('M d, Y, H:i A') : 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Forwarded At</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $mail->forwarded_at ? $mail->forwarded_at->format('M d, Y, H:i A') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t">
                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                    <p class="mt-1 text-base text-gray-800 whitespace-pre-wrap">{{ $mail->description }}</p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-end gap-4">
                    
                    <form action="{{ route('admin.mails.destroy', $mail) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this mail record? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition duration-300">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
