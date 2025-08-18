<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Profile Information
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Update your account's profile information and email address.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <input id="first_name" name="first_name" type="text" autocomplete="given-name" required
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="John" value="{{ old('first_name', $user->first_name) }}">
            @error('first_name')
                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
            @enderror
            
        </div>
        <div>
            <input id="last_name" name="last_name" type="text" autocomplete="given-name" required
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="John" value="{{ old('last_name', $user->first_name) }}">
            @error('last_name')
                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
            @enderror
            
        </div>
        <div>
            <input id="phone" name="phone" type="number" autocomplete="phone" required
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="phone" value="{{ old('last_name', $user->phone) }}">
            @error('phone')
                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
            @enderror
            
        </div>

        <div>
            <input id="email" name="email" type="email" autocomplete="given-name" required
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="John" value="{{ old('email', $user->email) }}">
            @error('email')
                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
            @enderror
            
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Your email address is unverified.

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Click here to re-send the verification email
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            A new verification link has been sent to your email address 
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
           <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Update Profile
            </button>
        </div>
    </form>
</section>
