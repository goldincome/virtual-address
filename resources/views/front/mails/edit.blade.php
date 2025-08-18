@extends('layouts.app')

@section('title', 'Edit Mail Service Request')
@section('page_title', 'Edit Mail Service Request')
@section('page_intro', 'Update your mail scan or forwarding request.')

@section('content')
<main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-blue-800">Edit Mail Request</h2>
        </div>
        <form method="POST" action="{{ route('mails.update', $mail) }}" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            @include('front.mails._form', ['mail' => $mail])
        </form>
    </div>
</main>
@endsection
