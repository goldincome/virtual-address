@extends('layouts.front')

@section('title')
    My Companies - Charlton Virtual Office
@endsection

@section('content')
    <section class="hero-bg-virtual text-white relative"
        style="background-image: url('{{ asset('images/virtual-office-address.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-16 relative z-10 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">My Companies</h1>
            <p class="text-blue-100">Manage your registered companies and Persons with Significant Control (PSC).</p>
        </div>
    </section>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @include('front.common.error-and-message')

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
            <h2 class="text-2xl font-bold text-blue-800 mb-4 sm:mb-0">Your Companies</h2>
            <a href="{{ route('companies.create') }}"
                class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 px-5 rounded-lg transition duration-300 shadow-md">
                <i class="fas fa-plus-circle mr-2"></i> Add Company
            </a>
        </div>

        @include('front.companies.partials.psc-allowance')

        @if($companies->isEmpty())
            <div class="bg-white p-10 rounded-lg shadow-md text-center">
                <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl text-gray-600 mb-6">You have not added any companies yet.</p>
                <a href="{{ route('companies.create') }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 shadow-md inline-block">
                    Add Your First Company
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($companies as $company)
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition duration-300">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-blue-800">{{ $company->company_name }}</h3>
                                @if($company->status === 'active')
                                    <span class="text-xs font-semibold text-white bg-green-500 px-2.5 py-1 rounded-full">Active</span>
                                @else
                                    <span class="text-xs font-semibold text-white bg-red-500 px-2.5 py-1 rounded-full">Suspended</span>
                                @endif
                            </div>
                            @if($company->address)
                                <p class="text-sm text-gray-600 mb-1"><i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>{{ $company->address }}</p>
                            @endif
                            @if($company->email)
                                <p class="text-sm text-gray-600 mb-1"><i class="fas fa-envelope text-orange-500 mr-2"></i>{{ $company->email }}</p>
                            @endif
                            @if($company->phone)
                                <p class="text-sm text-gray-600 mb-3"><i class="fas fa-phone text-orange-500 mr-2"></i>{{ $company->phone }}</p>
                            @endif
                            <div class="flex items-center justify-between border-t pt-3 mt-3">
                                <span class="text-sm text-gray-500">
                                    {{ $company->companyPscs->count() }} PSC(s)
                                </span>
                                <a href="{{ route('companies.show', $company) }}"
                                    class="text-orange-600 hover:text-orange-800 text-sm font-semibold">
                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($companies->hasPages())
                <div class="mt-8">{{ $companies->links() }}</div>
            @endif
        @endif
    </main>
@endsection