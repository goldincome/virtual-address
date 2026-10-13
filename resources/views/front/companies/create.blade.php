@extends('layouts.app')

@section('title')
    Add Company - Charlton Virtual Office
@endsection

@section('page_title')
    Add Your Company
@endsection

@section('page_intro')
    Register your company details for statutory compliance and PSC filings.
@endsection

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-lg shadow-md border border-gray-200">
        <form action="{{ route('companies.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('company_name') border-red-500 @enderror"
                            placeholder="Your Company Ltd.">
                        @error('company_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                        <textarea name="address" id="address" rows="2"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('address') border-red-500 @enderror"
                            placeholder="Registered office address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('phone') border-red-500 @enderror"
                            placeholder="+44 ...">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('email') border-red-500 @enderror"
                            placeholder="info@company.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Company Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('description') border-red-500 @enderror"
                            placeholder="Short description of your business">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <a href="{{ route('companies.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-md">
                        Save Company <i class="fas fa-check-circle ml-2"></i>
                    </button>
                </div>
            </form>
    </div>
@endsection