@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Emergency SOS Button -->
        <div class="mb-8">
            <div class="bg-red-600 text-white rounded-lg p-6 text-center">
                <h2 class="text-2xl font-bold mb-4">Emergency SOS</h2>
                <a href="{{ route('civilian.send-sos') }}" class="inline-block bg-white text-red-600 font-bold py-4 px-8 rounded-full text-lg hover:bg-red-50 transition">
                    🚨 SEND SOS ALERT
                </a>
                <p class="mt-4 text-red-100">Click only in case of real emergency</p>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Civilian Dashboard</h2>
                    <p class="text-gray-600">Welcome, {{ auth()->user()->full_name }}! Stay safe and connected.</p>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-blue-800">Report Incident</h3>
                                <p class="text-blue-600">Report non-emergency incidents</p>
                            </div>
                        </div>
                        <a href="{{ route('civilian.report-incident') }}" class="inline-block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            Report Now
                        </a>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-green-800">Safety Tips</h3>
                                <p class="text-green-600">Learn disaster preparedness</p>
                            </div>
                        </div>
                        <a href="#" class="inline-block w-full text-center bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition">
                            View Tips
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-gray-50 p-6 rounded-lg mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-white rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-4"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Your incident report has been acknowledged</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-white rounded-lg">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-4"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Weather alert issued for your area</p>
                                <p class="text-xs text-gray-500">1 day ago</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contacts -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Emergency Contacts</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded-lg text-center">
                            <div class="text-2xl mb-2">🚔</div>
                            <h4 class="font-semibold">Police</h4>
                            <p class="text-sm text-gray-600">911</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg text-center">
                            <div class="text-2xl mb-2">🚑</div>
                            <h4 class="font-semibold">Medical</h4>
                            <p class="text-sm text-gray-600">911</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg text-center">
                            <div class="text-2xl mb-2">🚒</div>
                            <h4 class="font-semibold">Fire</h4>
                            <p class="text-sm text-gray-600">911</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
