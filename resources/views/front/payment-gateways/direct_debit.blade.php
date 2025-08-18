@if($isSubscription)
    <div>
        {{-- This is the main clickable card for the Direct Debit option --}}
        <div class="payment-method payment-option p-4 rounded-lg cursor-pointer flex items-center justify-between"
            data-method="{{ $payMethod->value }}">
            {{-- The `checked` attribute is now dynamically set based on old input --}}
            <input type="radio" name="payment_method" value="{{ $payMethod->value }}" class="sr-only" @if(old('payment_method', 'card') == $payMethod->value) checked @endif />
            <div>
                <h3 class="font-semibold text-blue-800">Direct Debit</h3>
                <p class="text-sm text-gray-600">Pay securely from your bank account.</p>
            </div>
            <div>
            {{-- You can add a Direct Debit logo here --}}
            <img src="https://www.directdebit.co.uk/media/yojffdzu/dd_logo.png" alt="Direct Debit" class="h-6 hidden sm:block">
            </div>
        </div>

        {{-- This section is now conditionally shown based on old input or errors --}}
        @php
            $showDirectDebit = old('payment_method') === $payMethod->value || $errors->hasAny(['account_holder_name', 'vat_no', 'account_number', 'sort_code']);
        @endphp
        <div id="direct-debit-details" class="mt-4 p-6 border rounded-lg bg-gray-50 space-y-4 @if(!$showDirectDebit) hidden @endif">
            <h4 class="font-semibold text-gray-800 border-b pb-2">Enter your bank account details</h4>
            
            <div>
                <label for="acct_holder_name" class="block text-sm font-medium text-gray-700 mb-1">Account Holder Name</label>
                {{-- Added old() helper and @error directive for validation --}}
                <input type="text" name="acct_holder_name" id="acct_holder_name" value="{{ old('acct_holder_name') }}"
                    class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('acct_holder_name') border-red-500 @else border-gray-300 @enderror" 
                    placeholder="John M. Doe">
                @error('acct_holder_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="acct_number" class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                    {{-- Added old() helper and @error directive for validation --}}
                    <input type="text" name="acct_number" id="account_number" value="{{ old('acct_number') }}"
                        class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('acct_number') border-red-500 @else border-gray-300 @enderror" 
                        placeholder="12345678">
                    @error('acct_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="sort_code" class="block text-sm font-medium text-gray-700 mb-1">Sort Code</label>
                    {{-- Added old() helper and @error directive for validation --}}
                    <input type="text" name="sort_code" id="sort_code" value="{{ old('sort_code') }}"
                        class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('sort_code') border-red-500 @else border-gray-300 @enderror" 
                        placeholder="12-34-56">
                    @error('sort_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-2">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        {{-- Added old() helper for checkbox state --}}
                        <input id="approval_required" name="approval_required" type="checkbox" value="1" @if(old('approval_required')) checked @endif class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="approval_required" class="font-medium text-gray-700">More than one person is required for approval</label>
                        <p class="text-gray-500">Check this box if two signatures are required to authorise Direct Debits.</p>
                    </div>
                </div>
            </div>
            <div class="text-xs text-gray-500 pt-2 border-t mt-2">
                By proceeding, you are authorising NURUD to send instructions to your bank to debit your account in accordance with the Direct Debit Guarantee.
            </div>
        </div>
    </div>
@endif

