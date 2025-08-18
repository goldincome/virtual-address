@extends('layouts.admin') 

@section('title', 'Feature Settings Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-tools mr-2"></i> NURUD Admin
            </a>
            <a href="{{ route('admin.dashboard.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-0 py-8"> {{-- Adjusted padding for consistency --}}
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-blue-800">Feature Settings</h2>
                    <a href="{{ route('admin.feature-settings.create') }}"
                       class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition duration-300 text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i> New Feature Setting
                    </a>
                </div>
            </div>

            @if($featureSettings->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    No feature settings found.
                    <a href="{{ route('admin.feature-settings.create') }}" class="text-orange-500 hover:text-orange-700 font-semibold ml-2">Create one now!</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100"> 
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Icon (Class/SVG)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Last Updated</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($featureSettings as $setting)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $setting->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- Display icon preview if it's a Font Awesome class --}}
                                    @if(Str::startsWith($setting->icon, 'fa'))
                                        <i class="{{ $setting->icon }} text-blue-600 text-lg mr-2"></i>
                                    @endif
                                    <span class="text-sm text-gray-600">{{ Str::limit($setting->icon, 30) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $setting->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $setting->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $setting->updated_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.feature-settings.edit', $setting) }}"
                                       class="text-indigo-600 hover:text-indigo-900 mr-3 transition duration-150 ease-in-out">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.feature-settings.destroy', $setting) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this feature setting? This action cannot be undone.');">
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

            @if($featureSettings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $featureSettings->links() }} 
                </div>
            @endif
        </div>
    </main>
</div>
@endsection