<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-sm text-gray-600">Welcome back! Please login to your account.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Registration Success Message -->
    @if(session('status') && str_contains(session('status'), 'Registration successful'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-text-input id="email" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="email" 
                name="email" 
                :value="old('email')" 
                placeholder="Email"
                required 
                autofocus 
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <x-text-input id="password" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="password"
                name="password"
                placeholder="Password"
                required 
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:text-blue-800 transition duration-200" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- reCAPTCHA -->
        <div >
            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-size="normal" data-theme="light"></div>
        </div>
        <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-1 text-xs text-center" />

        <div class="pt-5">
            <button type="submit" 
    style="background-color: #2563eb; padding-top: 12px; padding-bottom: 12px; padding-left: 12px; padding-right: 12px;" 
    class="w-full text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
    {{ __('Login') }}
</button>
        </div>

        <div class="text-center pt-4">
            <p class="text-sm text-gray-600">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 transition duration-200">
                    {{ __('Register here') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
