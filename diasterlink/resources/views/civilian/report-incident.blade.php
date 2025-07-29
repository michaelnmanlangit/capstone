@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Report Incident</h2>
                    <p class="text-gray-600">Report a non-emergency incident to authorities</p>
                </div>

                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Incident Type</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option>Select incident type</option>
                            <option>Fire</option>
                            <option>Flood</option>
                            <option>Medical Emergency</option>
                            <option>Traffic Accident</option>
                            <option>Natural Disaster</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Enter location or address">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the incident in detail"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Severity Level</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="severity" value="low" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Low</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="severity" value="medium" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Medium</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="severity" value="high" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">High</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photos (Optional)</label>
                        <input type="file" multiple accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
