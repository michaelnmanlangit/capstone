@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Incident Management</h2>
                    <p class="text-gray-600">Manage and respond to reported incidents</p>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Incident Management Coming Soon</h3>
                            <p class="mt-1 text-sm text-yellow-700">This feature is currently under development.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-red-50 p-6 rounded-lg border border-red-200">
                        <h3 class="text-lg font-semibold text-red-800 mb-2">High Priority</h3>
                        <p class="text-3xl font-bold text-red-900">2</p>
                        <p class="text-sm text-red-600">Requires immediate attention</p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Medium Priority</h3>
                        <p class="text-3xl font-bold text-yellow-900">5</p>
                        <p class="text-sm text-yellow-600">Under investigation</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Resolved</h3>
                        <p class="text-3xl font-bold text-green-900">12</p>
                        <p class="text-sm text-green-600">Completed today</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
