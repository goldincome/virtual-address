@extends('layouts.admin')

@section('title', 'Create Service Usage')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-xl font-semibold mb-6">Log Service Usage</h1>

    <form method="POST" action="{{ route('admin.mail-usages.store') }}" class="space-y-6 bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block font-medium mb-1">User</label>
            <select name="user_id" class="form-select w-full border-gray-300 rounded">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Service Name</label>
            <input type="text" name="service_name" class="form-input w-full border-gray-300 rounded" required>
        </div>

        <div>
            <label class="block font-medium mb-1">Price (USD)</label>
            <input type="number" step="0.01" name="price" class="form-input w-full border-gray-300 rounded" required>
        </div>

        <div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
        </div>
    </form>
</div>
@endsection
