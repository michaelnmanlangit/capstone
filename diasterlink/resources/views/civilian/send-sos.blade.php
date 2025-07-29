@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6 text-center">
                    <div class="text-6xl mb-4">🚨</div>
                    <h2 class="text-3xl font-bold text-red-600">Emergency SOS Alert</h2>
                    <p class="text-gray-600 mt-2">Use this only for life-threatening emergencies</p>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Emergency Use Only</h3>
                            <p class="mt-1 text-sm text-red-700">This will immediately alert emergency responders and authorities. Use only for life-threatening situations.</p>
                        </div>
                    </div>
                </div>

                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Location</label>
                        <div class="flex space-x-2">
                            <input type="text" class="flex-1 px-4 py-3 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" placeholder="Your current location" readonly>
                            <button type="button" class="px-4 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                📍 Get Location
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Location will be automatically detected</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="emergency_type" value="medical" class="text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium">🚑 Medical Emergency</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="emergency_type" value="fire" class="text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium">🔥 Fire Emergency</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="emergency_type" value="crime" class="text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium">🚔 Crime in Progress</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="emergency_type" value="disaster" class="text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium">🌪️ Natural Disaster</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Information (Optional)</label>
                        <textarea rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" placeholder="Brief description of the emergency"></textarea>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">What happens next?</h3>
                                <p class="mt-1 text-sm text-yellow-700">
                                    Your SOS alert will be sent to local emergency services, nearby responders, and your emergency contacts immediately.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" class="w-full max-w-md px-8 py-4 bg-red-600 text-white text-lg font-bold rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300">
                            🚨 SEND EMERGENCY ALERT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
