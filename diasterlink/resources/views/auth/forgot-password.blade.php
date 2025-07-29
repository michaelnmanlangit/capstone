<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-sm text-gray-600">Enter your email address and we'll send you a password reset link.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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

        <div class="pt-4">
            <button type="submit" 
                style="background-color: #2563eb; padding-top: 12px; padding-bottom: 12px; padding-left: 12px; padding-right: 12px;" 
                class="w-full text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                {{ __('Send Reset Link') }}
            </button>
        </div>

        <div class="text-center pt-4">
            <p class="text-sm text-gray-600">
                {{ __('Remember your password?') }}
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition duration-200">
                    {{ __('Back to Login') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
