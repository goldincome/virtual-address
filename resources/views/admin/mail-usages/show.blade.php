@extends('layouts.admin')

@section('title', 'View Service Usage')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-xl font-semibold mb-6">Service Usage Details</h1>

    <div class="bg-white p-6 rounded shadow space-y-4">
        <div>
            <strong>User:</strong> {{ $usage->user->name }} ({{ $usage->user->email }})
        </div>
        <div>
            <strong>Service:</strong> {{ $usage->service_name }}
        </div>
        <div>
            <strong>Price:</strong> ${{ number_format($usage->price, 2) }}
        </div>
        <div>
            <strong>Billed:</strong>
            @if ($usage->billed)
                <span class="text-green-600 font-medium">Yes</span>
            @else
                <span class="text-red-600 font-medium">No</span>
            @endif
        </div>
        <div>
            <strong>Used At:</strong> {{ $usage->created_at->format('M j, Y H:i') }}
        </div>
        <div class="pt-4">
            <a href="{{ route('admin.usages.edit', $usage) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit</a>
            <a href="{{ route('admin.usages.index') }}" class="ml-2 inline-block text-sm text-gray-600 hover:underline">← Back</a>
        </div>
    </div>
</div>
@endsection
