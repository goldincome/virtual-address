@extends('layouts.app')

@section('title', 'New Mail Service Request')
@section('page_title', 'New Mail Service Request')
@section('page_intro', 'Request a mail scan or forwarding service.')

@section('content')
<main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-blue-800">New Mail Service Request</h2>
        </div>
        <form method="POST" action="{{ route('mails.store') }}" class="p-8 space-y-6">
            @csrf
            @include('front.mails._form', ['mail' => new \App\Models\Mail()])
        </form>
    </div>
</main>
@endsection

