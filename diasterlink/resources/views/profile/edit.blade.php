@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if (session('status') === 'profile-updated')
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Profile updated successfully!
                </div>
            </div>
        @endif

        <!-- Profile Information Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Update Password Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete User Section -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
