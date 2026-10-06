@extends('layouts.front')

@section('title')
    {{ $company->company_name }} - Charlton Virtual Office
@endsection

@section('content')
    <section class="hero-bg-virtual text-white relative"
        style="background-image: url('{{ asset('images/virtual-office-address.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-16 relative z-10 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $company->company_name }}</h1>
            <p class="text-blue-100">Company details and Persons with Significant Control (PSC).</p>
        </div>
    </section>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @include('front.common.error-and-message')

        <div class="mb-6">
            <a href="{{ route('companies.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Back to My Companies
            </a>
        </div>

        @include('front.companies.partials.psc-allowance')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Company details --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-blue-800">Company Details</h3>
                        @if($company->status === 'active')
                            <span class="text-xs font-semibold text-white bg-green-500 px-2.5 py-1 rounded-full">Active</span>
                        @else
                            <span class="text-xs font-semibold text-white bg-red-500 px-2.5 py-1 rounded-full">Suspended</span>
                        @endif
                    </div>
                    <dl class="space-y-3 text-sm">
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
                    </dl>
                </div>
            </div>

            {{-- PSC list + add form --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-blue-800">Person with Significant Control</h3>
                        <span class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full">{{ $company->companyPscs->count() }} PSC(s)</span>
                    </div>

                    @if($company->companyPscs->isEmpty())
                        <p class="text-gray-500 text-sm mb-4">No Persons with Significant Control added yet.</p>
                    @else
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Phone</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($company->companyPscs as $psc)
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $psc->first_name }} {{ $psc->last_name }}</td>
                                            <td class="px-4 py-3 text-gray-700">{{ $psc->pscType->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-700">{{ $psc->email ?: '-' }}</td>
                                            <td class="px-4 py-3 text-gray-700">{{ $psc->phone ?: '-' }}</td>
                                            <td class="px-4 py-3">
                                                @if($psc->status === 'active')
                                                    <span class="text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">Active</span>
                                                @else
                                                    <span class="text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1 rounded-full">Suspended</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($company->status === 'active' && $pscTypes->isNotEmpty())
                        <div class="border-t pt-5">
                            <h4 class="text-lg font-semibold text-blue-800 mb-3">Add a Person with Significant Control</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                You can add a PSC for each slot in your allowance. If your allowance is used up,
                                purchase more slots first using the <strong>Buy More</strong> button above.
                            </p>
                            <form action="{{ route('companies.psc.store', $company) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @csrf
                                <div>
                                    <label for="psc_type_id" class="block text-sm font-medium text-gray-700 mb-1">PSC Type <span class="text-red-500">*</span></label>
                                    <select name="psc_type_id" id="psc_type_id" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                        @foreach($pscTypes as $pscType)
                                            <option value="{{ $pscType->id }}">{{ $pscType->name }} (£{{ $pscType->price_monthly }}/mo · £{{ $pscType->price_yearly }}/yr)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone No</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit"
                                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-300 shadow-md">
                                        <i class="fas fa-user-plus mr-2"></i> Add PSC
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection