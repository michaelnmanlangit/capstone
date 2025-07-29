@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Report an Incident</h1>
                <p class="text-gray-600">Provide details about the disaster or emergency situation you want to report.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" id="incidentForm">
                @csrf
                
                <!-- Incident Type -->
                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Incident Type *</label>
                    <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select incident type</option>
                        <option value="fire">Fire</option>
                        <option value="flood">Flood</option>
                        <option value="earthquake">Earthquake</option>
                        <option value="accident">Traffic Accident</option>
                        <option value="medical">Medical Emergency</option>
                        <option value="weather">Severe Weather</option>
                        <option value="infrastructure">Infrastructure Failure</option>
                        <option value="other">Other</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="5" 
                        required 
                        placeholder="Provide detailed information about the incident..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <button type="button" onclick="getCurrentLocation()" class="text-sm text-blue-600 hover:text-blue-700">
                            📍 Use Current Location
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input 
                                type="number" 
                                name="latitude" 
                                id="latitude" 
                                step="any" 
                                placeholder="Latitude" 
                                value="{{ old('latitude') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            @error('latitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <input 
                                type="number" 
                                name="longitude" 
                                id="longitude" 
                                step="any" 
                                placeholder="Longitude" 
                                value="{{ old('longitude') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            @error('longitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Capture Images</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <input 
                            type="file" 
                            name="images[]" 
                            id="images" 
                            multiple 
                            accept="image/*"
                            capture="environment"
                            class="hidden"
                            onchange="handleImageCapture(this)"
                        >
                        <div id="capture-area" onclick="document.getElementById('images').click()" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M9 2L7.17 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4H16.83L15 2H9ZM12 7C14.76 7 17 9.24 17 12S14.76 17 12 17S7 14.76 7 12S9.24 7 12 7Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-medium text-blue-600">📷 Capture with Camera</span>
                            </p>
                            <p class="text-xs text-gray-500">Take photos directly with your device camera</p>
                        </div>
                        <div id="captured-images" class="mt-4 grid grid-cols-2 gap-2"></div>
                    </div>
                    @error('images.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('civilian.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        id="submitBtn"
                    >
                        Report Incident
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                alert('Location captured successfully!');
            }, function(error) {
                alert('Unable to get location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }

    function handleImageCapture(input) {
        const files = input.files;
        const capturedImagesDiv = document.getElementById('captured-images');
        
        // Clear previous images
        capturedImagesDiv.innerHTML = '';
        
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'relative';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-24 object-cover rounded-lg border border-gray-300';
                    
                    const fileName = document.createElement('p');
                    fileName.textContent = file.name;
                    fileName.className = 'text-xs text-gray-600 mt-1 text-center truncate';
                    
                    imageContainer.appendChild(img);
                    imageContainer.appendChild(fileName);
                    capturedImagesDiv.appendChild(imageContainer);
                };
                
                reader.readAsDataURL(file);
            }
        }
    }

    // Form validation
    document.getElementById('incidentForm').addEventListener('submit', function(e) {
        const type = document.getElementById('type').value;
        const description = document.getElementById('description').value.trim();

        if (!type || !description) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Reporting...';
    });
</script>
@endsection
