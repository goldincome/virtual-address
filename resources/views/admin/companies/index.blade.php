@extends('layouts.admin')

@section('title', 'Companies & Persons with Significant Control')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-tools mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.dashboard.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <h2 class="text-2xl font-bold text-blue-800">Companies</h2>
                    <form method="GET" action="{{ route('admin.companies.index') }}" class="flex items-center gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               placeholder="Search company name, PSC first/last name..."
                               class="w-full sm:w-80 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out text-sm">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-150 text-sm font-medium">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('admin.companies.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="m-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="m-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($companies->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    @if(!empty($search))
                        No companies found matching "{{ $search }}".
                    @else
                        No companies found yet.
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Company Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Owner</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">PSCs</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Created</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($companies as $company)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $company->company_name }}</div>
                                    @if($company->email)
                                        <div class="text-xs text-gray-500">{{ $company->email }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $company->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $company->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $company->company_pscs_count }} PSC(s)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $company->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $company->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.companies.show', $company) }}"
                                       class="text-green-600 hover:text-green-900 mr-3 transition duration-150 ease-in-out">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                    @if($company->status === 'active')
                                        <form action="{{ route('admin.companies.suspend', $company) }}" method="POST" class="inline-block" onsubmit="return confirm('Suspend this company? This cancels Stripe billing for all its PSCs.');">
                                            @csrf
                                            <button type="submit" class="text-amber-600 hover:text-amber-900 transition duration-150 ease-in-out">
                                                <i class="fas fa-pause mr-1"></i>Suspend
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.companies.activate', $company) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 transition duration-150 ease-in-out">
                                                <i class="fas fa-play mr-1"></i>Activate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($companies->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $companies->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
@endsection