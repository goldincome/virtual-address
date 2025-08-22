@extends('layouts.app')

@section('title', 'My Mailbox')
@section('page_title', 'My Mailbox')
@section('page_intro', 'View and manage all mail and packages received at your virtual address.')

@section('content')
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-xl">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <h2 class="text-2xl font-semibold text-blue-800">Mail & Service Requests</h2>
            <a href="{{ route('mails.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                <i class="fas fa-plus-circle mr-2"></i> New Request
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type /
                            From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($mails as $mail)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($mail->recieved_at ?? $mail->created_at)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $mail->mail_type?->label() ?? ($mail->sender_name ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $mail->description }}">
                                {{ Str::limit($mail->description, 40) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $mail->mail_status->colorClass() }}">{{ $mail->mail_status->label() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                @if($mail->mail_status->value === $mailStatuses::Pending->value)
                                    <a href="#"
                                        class="text-green-600 hover:text-green-900">
                                        Pay Now
                                    </a>
                                @endif
                                @if($mail->mail_status->value === $mailStatuses::Scanned->value)
                                    <a href="{{ asset($mail->scan_upload_url) }}"
                                        class="text-green-600 hover:text-green-900">
                                        Download Mail
                                    </a>
                                @endif
                                <a href="{{ route('mails.show', $mail) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    View
                                </a>

                                <a href="{{ route('mails.edit', $mail) }}"
                                    class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('mails.destroy', $mail) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No mail records or requests
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-8">{{ $mails->links() }}</div>
    </div>
@endsection
