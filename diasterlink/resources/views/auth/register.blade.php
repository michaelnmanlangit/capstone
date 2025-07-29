<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-sm text-gray-600">Join DISASTERLINK community</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- First Name -->
        <div>
            <x-text-input 
                id="first_name" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="text" 
                name="first_name" 
                :value="old('first_name')" 
                placeholder="First name"
                required />
            <x-input-error :messages="$errors->get('first_name')" class="mt-1 text-xs" />
        </div>

        <!-- Last Name -->
        <div>
            <x-text-input 
                id="last_name" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="text" 
                name="last_name" 
                :value="old('last_name')" 
                placeholder="Last name"
                required />
            <x-input-error :messages="$errors->get('last_name')" class="mt-1 text-xs" />
        </div>

        <!-- Email Address -->
        <div>
            <x-text-input 
                id="email" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="email" 
                name="email" 
                :value="old('email')" 
                placeholder="Email"
                required />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <x-text-input 
                id="password" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="password"
                name="password"
                placeholder="Password"
                required />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-text-input 
                id="password_confirmation" 
                class="block w-full px-3 py-3 text-sm rounded-md border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                type="password"
                name="password_confirmation"
                placeholder="Confirm password"
                required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        <!-- Terms and Privacy Policy -->
        <div class="flex items-start pt-4">
            <input id="terms" name="terms" type="checkbox" class="h-4 w-4 mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" required>
            <label for="terms" class="ml-2 block text-sm text-gray-600 leading-5">
                I agree to the <a href="#" class="text-blue-600 hover:text-blue-500 transition duration-200">Terms</a> and <a href="#" class="text-blue-600 hover:text-blue-500 transition duration-200">Privacy Policy</a>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit" 
    style="background-color: #2563eb; padding-top: 12px; padding-bottom: 12px; padding-left: 12px; padding-right: 12px;" 
    class="w-full text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
    {{ __('Create Account') }}
</button>
        </div>

        <div class="text-center pt-4">
            <p class="text-sm text-gray-600">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition duration-200">
                    {{ __('Sign in') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
