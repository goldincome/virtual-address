@extends('layouts.app')

@section('title', 'Mail Details')
@section('page_title', 'Mail Details')
@section('page_intro', 'Detailed information for your mail item.')

@section('content')
<main class="py-8 px-4">
    <div class="w-full max-w-3xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-blue-800">
                    @if($mail->mail_type)
                        {{ $mail->mail_type->label() }} Request
                    @else
                        Mail from: {{ $mail->sender_name ?? 'N/A' }}
                    @endif
                </h2>
                <a href="{{ route('mails.index') }}" class="text-sm text-gray-600 hover:text-orange-500">&larr; Back to Mailbox</a>
            </div>
        </div>

        <div class="p-8 space-y-6">
            @if($mail->sender_name)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b">
                <div>
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">Courier/Sender</h3>
                    <p class="text-gray-800 font-medium">{{ $mail->sender_name }}</p>
                </div>
            </div>
            @endif

            <div>
                <h3 class="text-lg font-semibold text-blue-700 mb-2">Description / Instructions</h3>
                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg border whitespace-pre-wrap">{{ $mail->description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Mail Status</h3>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $mail->mail_status->colorClass() }}">{{ $mail->mail_status->label() }}</span>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Payment Status</h3>
                     <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $mail->payment_status->colorClass() }}">{{ $mail->payment_status->label() }}</span>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Service Fee</h3>
                    <p class="text-lg font-semibold text-gray-800">£{{ number_format($mail->price, 2) }}</p>
                </div>
            </div>
            
            @if($mail->tracking_number || $mail->tracking_url)
            <div class="pb-6 border-b">
                <h3 class="text-lg font-semibold text-blue-700 mb-2">Tracking Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($mail->tracking_number)
                        <div><h4 class="text-sm font-medium text-gray-500 mb-1">Tracking Number</h4><p class="text-gray-800">{{ $mail->tracking_number }}</p></div>
                    @endif
                    @if($mail->tracking_url)
                        <div><h4 class="text-sm font-medium text-gray-500 mb-1">Tracking URL</h4><a href="{{ $mail->tracking_url }}" target="_blank" rel="noopener noreferrer" class="text-orange-500 hover:underline break-all">{{ $mail->tracking_url }}</a></div>
                    @endif
                </div>
            </div>
            @endif

            <div>
                <h3 class="text-lg font-semibold text-blue-700 mb-2">Important Dates</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div><p class="text-gray-500">Date Created:</p><p class="font-medium text-gray-800">{{ $mail->created_at->format('d M Y, H:i A') }}</p></div>
                    <div><p class="text-gray-500">Received At:</p><p class="font-medium text-gray-800">{{ $mail->recieved_at ? $mail->recieved_at->format('d M Y, H:i A') : 'N/A' }}</p></div>
                    <div><p class="text-gray-500">Action Completed:</p><p class="font-medium text-gray-800">{{ ($mail->scanned_at ?? $mail->forwarded_at) ? ($mail->scanned_at ?? $mail->forwarded_at)->format('d M Y, H:i A') : 'N/A' }}</p></div>
                </div>
            </div>

            @can('update', $mail)
            <div class="pt-6 flex justify-end">
                 <a href="{{ route('mails.edit', $mail) }}" class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105 text-center">
                    <i class="fas fa-pencil-alt mr-2"></i> Edit Request
                </a>
            </div>
            @endcan
        </div>
    </div>
</main>
@endsection

