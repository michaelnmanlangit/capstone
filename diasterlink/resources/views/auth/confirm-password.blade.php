<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-sm text-gray-600">Please confirm your password to continue to this secure area.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <!-- Password -->
        <div>
            <x-text-input id="password" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="password"
                name="password"
                placeholder="Password"
                required 
                autofocus
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <div class="pt-4">
            <button type="submit" 
                style="background-color: #2563eb; padding-top: 12px; padding-bottom: 12px; padding-left: 12px; padding-right: 12px;" 
                class="w-full text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                {{ __('Confirm') }}
            </button>
        </div>

        <div class="text-center pt-4">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-blue-600 transition duration-200">
                {{ __('Back to Dashboard') }}
            </a>
        </div>
    </form>
</x-guest-layout>
