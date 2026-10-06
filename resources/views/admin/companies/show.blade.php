@extends('layouts.admin')

@section('title', 'Company Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-tools mr-2"></i> Charlton Virtual Office Admin
            </a>
            <a href="{{ route('admin.companies.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Companies
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-blue-800">Company Details</h2>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $company->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($company->status) }}
                        </span>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div>
                            <dt class="text-gray-500 font-medium">Owner</dt>
                            <dd class="text-gray-800">{{ $company->user->name ?? 'N/A' }} ({{ $company->user->email ?? '-' }})</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-medium">Company Name</dt>
                            <dd class="text-gray-800">{{ $company->company_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-medium">Address</dt>
                            <dd class="text-gray-800">{{ $company->address ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-medium">Phone</dt>
                            <dd class="text-gray-800">{{ $company->phone ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-medium">Email</dt>
                            <dd class="text-gray-800">{{ $company->email ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-medium">Description</dt>
                            <dd class="text-gray-800">{{ $company->description ?: '-' }}</dd>
                        </div>
                        @if($company->status === 'active')
                            <form action="{{ route('admin.companies.suspend', $company) }}" method="POST" onsubmit="return confirm('Suspend this company? This cancels Stripe billing for all its PSCs.');">
                                @csrf
                                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-semibold transition duration-300">
                                    <i class="fas fa-pause mr-2"></i> Suspend Company &amp; Cancel Billing
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.companies.activate', $company) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-300">
                                    <i class="fas fa-play mr-2"></i> Activate Company
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-blue-800">Person with Significant Control ({{ $company->companyPscs->count() }})</h3>
                    </div>

                    @if($company->companyPscs->isEmpty())
                        <div class="p-6 text-center text-gray-500">No Persons with Significant Control added for this company.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($company->companyPscs as $psc)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $psc->first_name }} {{ $psc->last_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $psc->pscType->name ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $psc->email ?: '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $psc->phone ?: '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $psc->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($psc->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($psc->status === 'active')
                                                    <form action="{{ route('admin.psc.suspend', $psc) }}" method="POST" class="inline-block" onsubmit="return confirm('Suspend this PSC? This cancels its Stripe billing.');">
                                                        @csrf
                                                        <button type="submit" class="text-amber-600 hover:text-amber-900 transition duration-150 ease-in-out">
                                                            <i class="fas fa-pause mr-1"></i>Suspend
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.psc.activate', $psc) }}" method="POST" class="inline-block">
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
                </div>

                @if($pscTypes->isNotEmpty())
                    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-blue-800">Add Person with Significant Control</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.companies.psc', $company) }}" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @csrf
                            <div>
                                <label for="psc_type_id" class="block text-sm font-medium text-gray-700 mb-1">PSC Type <span class="text-red-500">*</span></label>
                                <select id="psc_type_id" name="psc_type_id" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    @foreach($pscTypes as $pscType)
                                        <option value="{{ $pscType->id }}">{{ $pscType->name }} (£{{ $pscType->price_monthly }}/mo · £{{ $pscType->price_yearly }}/yr)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone No</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 text-sm">
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                        class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold transition duration-300">
                                    <i class="fas fa-user-plus mr-2"></i> Add PSC (and bill owner)
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection