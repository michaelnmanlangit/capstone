<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-sm text-gray-600">Please verify your email address to complete your account setup.</p>
    </div>

    <div class="mb-6 text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
            {{ __('We\'ve sent a verification link to your email address. Please check your inbox and click the link to verify your account.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <p class="text-sm text-green-800 text-center">
                {{ __('A new verification link has been sent to your email address.') }}
            </p>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" 
                style="background-color: #2563eb; padding-top: 12px; padding-bottom: 12px; padding-left: 12px; padding-right: 12px;" 
                class="w-full text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <div class="text-center pt-2">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-blue-600 transition duration-200">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
