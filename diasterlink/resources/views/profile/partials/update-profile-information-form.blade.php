@php
use Illuminate\Support\Facades\Storage;
@endphp

<section>
    <header class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Picture -->
        <div class="space-y-2">
            <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
            <div class="flex items-center space-x-4">
                @if($user->profile_picture)
                    <img src="{{ Storage::url($user->profile_picture) }}" alt="Profile" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="w-16 h-16 rounded-full bg-gray-100 border-2 border-gray-200 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF up to 2MB</p>
                </div>
            </div>
            @error('profile_picture')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input id="name" name="name" type="text" 
                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input id="email" name="email" type="email" 
                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline text-sm text-amber-600 hover:text-amber-900 font-medium">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Phone Number -->
        <div class="space-y-2">
            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input id="phone_number" name="phone_number" type="tel" 
                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel" placeholder="Enter your phone number">
            @error('phone_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div class="space-y-2">
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <textarea id="address" name="address" rows="3" 
                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                      autocomplete="street-address" placeholder="Enter your complete address">{{ old('address', $user->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-4">
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                   class="text-sm text-green-600 font-medium">{{ __('Saved successfully!') }}</p>
            @endif
        </div>
    </form>
</section>
