@extends('layouts.admin')

@section('title', 'Mail Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-envelope-open-text mr-2"></i> NURUD Admin
            </a>
            <a href="{{ route('admin.dashboard.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-0 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-blue-800">Received Mail</h2>
                    <a href="{{ route('admin.mails.search') }}"
                       class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition duration-300 text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i> New Mail Record
                    </a>
                    
                </div>
            </div>

            @if(session('success'))
                <div class="m-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($mails->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No mail records found.
                    <a href="{{ route('admin.mails.create') }}" class="text-orange-500 hover:text-orange-700 font-semibold ml-2">Create one now!</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Recipient (User)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Sender</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Mail Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Payment Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Received At</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($mails as $mail)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $mail->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $mail->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $mail->sender_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @switch($mail->mail_status)
                                            @case(\App\Enums\MailStatusEnum::Pending) bg-yellow-100 text-yellow-800 @break
                                            @case(\App\Enums\MailStatusEnum::Paid) bg-blue-100 text-blue-800 @break
                                            @case(\App\Enums\MailStatusEnum::Scanned) bg-green-100 text-green-800 @break
                                            @case(\App\Enums\MailStatusEnum::Forwarded) bg-purple-100 text-purple-800 @break
                                        @endswitch">
                                        {{ $mail->mail_status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @switch($mail->payment_status)
                                            @case(\App\Enums\PaymentStatusEnum::Pending) bg-yellow-100 text-yellow-800 @break
                                            @case(\App\Enums\PaymentStatusEnum::Paid) bg-green-100 text-green-800 @break
                                            @case(\App\Enums\PaymentStatusEnum::Failed) bg-red-100 text-red-800 @break
                                            @default bg-gray-100 text-gray-800 @break
                                        @endswitch">
                                        {{ $mail->payment_status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $mail->recieved_at ? $mail->recieved_at->format('M d, Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.mails.show', $mail) }}"
                                       class="text-green-600 hover:text-green-900 mr-3 transition duration-150 ease-in-out">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                   
                                    <form action="{{ route('admin.mails.destroy', $mail) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this mail record? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($mails->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $mails->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
@endsection
