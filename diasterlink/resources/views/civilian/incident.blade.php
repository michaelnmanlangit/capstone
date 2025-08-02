@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900 mb-1">Report an Incident</h1>
                    <p class="text-sm text-gray-600">Provide details about the disaster or emergency situation you want to report.</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" viewBox="0 -2 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="currentColor">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <title>camera</title>
                            <desc>Created with Sketch Beta.</desc>
                            <defs></defs>
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                                <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-258.000000, -467.000000)" fill="currentColor">
                                    <path d="M286,471 L283,471 L282,469 C281.411,467.837 281.104,467 280,467 L268,467 C266.896,467 266.53,467.954 266,469 L265,471 L262,471 C259.791,471 258,472.791 258,475 L258,491 C258,493.209 259.791,495 262,495 L286,495 C288.209,495 290,493.209 290,491 L290,475 C290,472.791 288.209,471 286,471 Z M274,491 C269.582,491 266,487.418 266,483 C266,478.582 269.582,475 274,475 C278.418,475 282,478.582 282,483 C282,487.418 278.418,491 274,491 Z M274,477 C270.687,477 268,479.687 268,483 C268,486.313 270.687,489 274,489 C277.313,489 280,486.313 280,483 C280,479.687 277.313,477 274,477 L274,477 Z" id="camera" sketch:type="MSShapeGroup"></path>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">

            <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" id="incidentForm" class="space-y-6">
                @csrf
                
                <!-- Incident Type -->
                <div class="space-y-3">
                    <label for="type" class="block text-sm font-medium text-gray-700">Incident Type *</label>
                    <select name="type" id="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option value="">Select incident type</option>
                        <option value="fire" {{ old('type') == 'fire' ? 'selected' : '' }}>Fire</option>
                        <option value="flood" {{ old('type') == 'flood' ? 'selected' : '' }}>Flood</option>
                        <option value="typhoon" {{ old('type') == 'typhoon' ? 'selected' : '' }}>Typhoon</option>
                        <option value="earthquake" {{ old('type') == 'earthquake' ? 'selected' : '' }}>Earthquake</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="space-y-3">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4" 
                        required 
                        placeholder="Provide detailed information about the incident..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Location -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-700">Location *</label>
                        <button type="button" id="locationBtn" onclick="getCurrentLocation()" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors duration-200">
                            <svg id="locationIcon" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <span id="locationText">Use Current Location</span>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-gray-50"
                                readonly
                                required
                            >
                            @error('latitude')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-gray-50"
                                readonly
                                required
                            >
                            @error('longitude')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Capture Images <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-gray-500 ml-1">(Required: 3 images)</span>
                    </label>
                    
                    <!-- Camera Capture Area -->
                    <div class="border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 transition-colors duration-200 overflow-hidden">
                        <!-- Hidden input for form submission -->
                        <input 
                            type="file" 
                            name="images[]" 
                            id="images" 
                            multiple 
                            accept="image/*"
                            class="hidden"
                        >
                        
                        <!-- Capture Button Area -->
                        <div id="capture-area" onclick="captureImage()" class="cursor-pointer p-6 text-center bg-gray-50 hover:bg-blue-50 transition-colors duration-200">
                            <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center mb-4 shadow-md border border-gray-200">
                                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 2L7.17 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4H16.83L15 2H9ZM12 7C14.76 7 17 9.24 17 12S14.76 17 12 17S7 14.76 7 12S9.24 7 12 7Z"/>
                                </svg>
                            </div>
                            <h4 class="text-base font-semibold text-gray-800 mb-2">📷 Capture with Camera</h4>
                            <p class="text-sm text-gray-600 mb-1">Take 3 photos directly with your device camera</p>
                            <p class="text-xs text-blue-600 font-medium">(0/3) images captured</p>
                        </div>
                        
                        <!-- Captured Images Display -->
                        <div id="captured-images" class="bg-white p-4 flex gap-3 overflow-x-auto md:grid md:grid-cols-3 md:overflow-x-visible border-t border-gray-200 hidden"></div>
                    </div>
                    @error('images.*')
                        <p class="text-red-500 text-xs mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-start pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 text-sm font-medium"
                        id="submitBtn"
                        onclick="return validateForm()"
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
        const locationBtn = document.getElementById('locationBtn');
        const locationIcon = document.getElementById('locationIcon');
        const locationText = document.getElementById('locationText');
        
        // Add loading state
        locationBtn.className = "inline-flex items-center text-xs text-gray-600 bg-gray-100 px-3 py-1 rounded-lg transition-colors duration-200";
        locationIcon.innerHTML = `
            <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;
        locationText.textContent = "Getting Location...";
        locationBtn.disabled = true;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                
                // Change to success state - green with check icon
                locationBtn.className = "inline-flex items-center text-xs text-green-600 bg-green-50 px-3 py-1 rounded-lg transition-colors duration-200";
                locationIcon.innerHTML = `
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                `;
                locationText.textContent = "Location Captured";
                locationBtn.disabled = false;
                
                // Show success notification
                showNotification('Location captured successfully!', 'success');
            }, function(error) {
                // Reset to original state on error
                locationBtn.className = "inline-flex items-center text-xs text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors duration-200";
                locationIcon.innerHTML = `
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                `;
                locationText.textContent = "Use Current Location";
                locationBtn.disabled = false;
                
                let errorMessage = 'Unable to get location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Location access denied.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += 'Unknown error occurred.';
                        break;
                }
                showNotification(errorMessage, 'error');
            });
        } else {
            // Reset to original state if geolocation not supported
            locationBtn.className = "inline-flex items-center text-xs text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors duration-200";
            locationIcon.innerHTML = `
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
            `;
            locationText.textContent = "Use Current Location";
            locationBtn.disabled = false;
            showNotification('Geolocation is not supported by this browser.', 'error');
        }
    }

    // Force camera capture only (no file selection)
    async function captureImage() {
        // Check current image count
        const capturedImagesDiv = document.getElementById('captured-images');
        const currentImageCount = capturedImagesDiv.children.length;
        
        if (currentImageCount >= 3) {
            showNotification('Maximum of 3 images allowed. Please remove an image before capturing a new one.', 'error');
            return;
        }

        try {
            // Check if device supports camera
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showNotification('Camera access is not supported on this device.', 'error');
                return;
            }

            // Create video element for camera preview
            const video = document.createElement('video');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // Get camera stream
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment' // Use back camera if available
                } 
            });

            video.srcObject = stream;
            video.play();

            // Create camera modal - responsive design
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-xl w-full max-w-md lg:max-w-lg mx-auto overflow-hidden shadow-2xl">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3">
                        <h3 class="text-lg font-semibold text-white text-center">Camera Capture</h3>
                        <p class="text-blue-100 text-center mt-1 text-sm">
                            Capturing image ${currentImageCount + 1} of 3
                        </p>
                    </div>
                    
                    <!-- Camera Preview -->
                    <div class="relative bg-black">
                        <video id="camera-preview" class="w-full h-56 md:h-64 lg:h-72 object-cover bg-black"></video>
                        
                        <!-- Capture frame overlay -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-3/4 h-3/4 max-w-xs max-h-xs border-2 border-white rounded-lg opacity-70 shadow-lg">
                                <div class="absolute -top-2 -left-2 w-4 h-4 border-l-2 border-t-2 border-white"></div>
                                <div class="absolute -top-2 -right-2 w-4 h-4 border-r-2 border-t-2 border-white"></div>
                                <div class="absolute -bottom-2 -left-2 w-4 h-4 border-l-2 border-b-2 border-white"></div>
                                <div class="absolute -bottom-2 -right-2 w-4 h-4 border-r-2 border-b-2 border-white"></div>
                            </div>
                        </div>
                        
                        <!-- Flash effect -->
                        <div id="flash-effect" class="absolute inset-0 bg-white opacity-0 transition-opacity duration-150"></div>
                        
                        <!-- Progress indicator -->
                        <div class="absolute top-3 right-3 bg-black bg-opacity-60 text-white px-2 py-1 rounded-lg">
                            <div class="flex items-center space-x-1 text-xs">
                                <span>${currentImageCount + 1}/3</span>
                                <div class="flex space-x-1">
                                    ${Array.from({length: 3}, (_, i) => 
                                        `<div class="w-1.5 h-1.5 rounded-full ${i < currentImageCount ? 'bg-green-400' : i === currentImageCount ? 'bg-blue-400' : 'bg-gray-400'}"></div>`
                                    ).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Controls -->
                    <div class="p-4 bg-gray-50">
                        <div class="flex justify-center space-x-3">
                            <!-- Capture Button -->
                            <button onclick="takePicture()" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 2L7.17 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4H16.83L15 2H9ZM12 7C14.76 7 17 9.24 17 12S14.76 17 12 17S7 14.76 7 12S9.24 7 12 7Z"/>
                                </svg>
                                <span>Capture</span>
                            </button>
                            
                            <!-- Cancel Button -->
                            <button onclick="closeCameraModal()" class="bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                                Cancel
                            </button>
                        </div>
                        
                        <!-- Desktop keyboard shortcuts -->
                        <div class="hidden md:block mt-3 pt-3 border-t border-gray-200">
                            <p class="text-center text-xs text-gray-600">
                                <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs font-mono">Space</kbd> to capture • 
                                <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs font-mono">Esc</kbd> to cancel
                            </p>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            
            // Set up video in modal
            const modalVideo = modal.querySelector('#camera-preview');
            modalVideo.srcObject = stream;
            modalVideo.play();

            // Add keyboard event listeners for desktop
            const handleKeyPress = (e) => {
                if (e.code === 'Space') {
                    e.preventDefault();
                    takePicture();
                } else if (e.code === 'Escape') {
                    e.preventDefault();
                    closeCameraModal();
                }
            };
            
            document.addEventListener('keydown', handleKeyPress);
            
            // Store event listener for cleanup
            window.currentKeyHandler = handleKeyPress;

            // Store stream globally for cleanup
            window.currentStream = stream;
            window.currentModal = modal;
            window.currentVideo = modalVideo;
            window.currentCanvas = canvas;
            window.currentCtx = ctx;
            window.capturedBlobs = window.capturedBlobs || []; // Store captured images

        } catch (error) {
            console.error('Error accessing camera:', error);
            showNotification('Failed to access camera. Please ensure camera permissions are granted.', 'error');
        }
    }

    // Take picture function
    function takePicture() {
        const video = window.currentVideo;
        const canvas = window.currentCanvas;
        const ctx = window.currentCtx;

        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw video frame to canvas
        ctx.drawImage(video, 0, 0);

        // Flash effect
        const flashEffect = document.getElementById('flash-effect');
        flashEffect.style.opacity = '0.8';
        setTimeout(() => {
            flashEffect.style.opacity = '0';
        }, 150);

        // Convert to blob and store it
        canvas.toBlob((blob) => {
            // Initialize captured blobs array if not exists
            if (!window.capturedBlobs) {
                window.capturedBlobs = [];
            }
            
            // Add blob to array
            window.capturedBlobs.push(blob);
            
            const captureCount = window.capturedBlobs.length;
            
            if (captureCount < 3) {
                // Update modal for next capture
                updateModalForNextCapture(captureCount);
                showNotification(`Image ${captureCount} captured! Capture ${3 - captureCount} more.`, 'success');
            } else {
                // All 3 images captured, process them
                processAllCapturedImages();
                closeCameraModal();
                showNotification('All 3 images captured successfully!', 'success');
            }
            
        }, 'image/jpeg', 0.8);
    }

    // Update modal for next capture
    function updateModalForNextCapture(capturedCount) {
        const modal = window.currentModal;
        const header = modal.querySelector('.bg-gradient-to-r p');
        const progressText = modal.querySelector('.absolute.top-3.right-3 span');
        const progressDots = modal.querySelectorAll('.w-1\\.5.h-1\\.5');
        
        // Update header text
        if (header) {
            header.textContent = `Capturing image ${capturedCount + 1} of 3`;
        }
        
        // Update progress text
        if (progressText) {
            progressText.textContent = `${capturedCount + 1}/3`;
        }
        
        // Update progress dots
        progressDots.forEach((dot, index) => {
            dot.className = dot.className.replace(/bg-(gray|green|blue)-400/, '');
            if (index < capturedCount) {
                dot.className += ' bg-green-400'; // Completed
            } else if (index === capturedCount) {
                dot.className += ' bg-blue-400'; // Current
            } else {
                dot.className += ' bg-gray-400'; // Pending
            }
        });
    }

    // Process all captured images and add to form
    function processAllCapturedImages() {
        const capturedImagesDiv = document.getElementById('captured-images');
        const input = document.getElementById('images');
        
        // Clear previous images
        capturedImagesDiv.innerHTML = '';
        
        // Create DataTransfer to handle multiple files
        const dt = new DataTransfer();
        
        // Process each captured blob
        window.capturedBlobs.forEach((blob, index) => {
            // Create file object
            const file = new File([blob], `captured-image-${index + 1}-${Date.now()}.jpg`, { type: 'image/jpeg' });
            dt.items.add(file);
            
            // Create image preview for display
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageContainer = document.createElement('div');
                imageContainer.className = 'relative group flex-shrink-0 w-28 md:w-auto md:flex-shrink';
                
                imageContainer.innerHTML = `
                    <div class="relative">
                        <img src="${e.target.result}" class="w-28 h-20 md:w-full md:h-24 object-cover rounded-lg border-2 border-gray-200 group-hover:border-blue-400 transition-colors shadow-sm">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-opacity"></div>
                        <button type="button" onclick="removeImage(this)" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 md:w-7 md:h-7 flex items-center justify-center text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                            ×
                        </button>
                    </div>
                    <p class="text-xs text-gray-600 mt-2 text-center font-medium">Image ${index + 1}</p>
                `;
                
                capturedImagesDiv.appendChild(imageContainer);
                
                // Show the captured images container after adding all images
                if (index === window.capturedBlobs.length - 1) {
                    capturedImagesDiv.classList.remove('hidden');
                    // Update UI after all images are processed
                    updateCaptureAreaUI();
                }
            };
            reader.readAsDataURL(blob);
        });
        
        // Update file input
        input.files = dt.files;
        
        // Clear captured blobs
        window.capturedBlobs = [];
    }

    // Close camera modal
    function closeCameraModal() {
        if (window.currentStream) {
            window.currentStream.getTracks().forEach(track => track.stop());
        }
        if (window.currentModal) {
            document.body.removeChild(window.currentModal);
        }
        
        // Remove keyboard event listener
        if (window.currentKeyHandler) {
            document.removeEventListener('keydown', window.currentKeyHandler);
        }
        
        // Clean up global variables
        window.currentStream = null;
        window.currentModal = null;
        window.currentVideo = null;
        window.currentCanvas = null;
        window.currentCtx = null;
        window.currentKeyHandler = null;
        
        // Reset captured blobs if user cancels
        if (window.capturedBlobs && window.capturedBlobs.length > 0 && window.capturedBlobs.length < 3) {
            window.capturedBlobs = [];
            showNotification('Capture cancelled. Please start over to capture all 3 images.', 'warning');
        }
    }

    function handleImageCapture(input) {
        const files = input.files;
        const capturedImagesDiv = document.getElementById('captured-images');
        
        // Validate that images are from camera capture (not file upload)
        if (files.length > 0) {
            // Clear previous images
            capturedImagesDiv.innerHTML = '';
            
            // Limit to 3 images maximum
            const maxImages = Math.min(files.length, 3);
            
            for (let i = 0; i < maxImages; i++) {
                const file = files[i];
                
                // Additional validation to ensure it's an image
                if (!file.type.startsWith('image/')) {
                    showNotification('Please capture images only.', 'error');
                    continue;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'relative group';
                    
                    imageContainer.innerHTML = `
                        <div class="relative">
                            <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg border border-gray-300 group-hover:border-blue-400 transition-colors">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-opacity"></div>
                            <button type="button" onclick="removeImage(this)" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                ×
                            </button>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 text-center truncate">Image ${i + 1}</p>
                    `;
                    
                    capturedImagesDiv.appendChild(imageContainer);
                    
                    // Update capture area UI
                    updateCaptureAreaUI();
                };
                
                reader.readAsDataURL(file);
            }
            
            showNotification(`${maxImages} image(s) captured successfully!`, 'success');
            
            if (files.length > 3) {
                showNotification('Only first 3 images were captured. Maximum limit is 3 images.', 'warning');
            }
        }
    }

    // Update capture area UI based on image count
    function updateCaptureAreaUI() {
        const capturedImagesDiv = document.getElementById('captured-images');
        const captureArea = document.getElementById('capture-area');
        const currentImageCount = capturedImagesDiv.children.length;
        
        console.log('Updating UI, current image count:', currentImageCount); // Debug log
        
        // Update counter text
        const counterText = captureArea.querySelector('.text-xs.text-blue-600, .text-xs.text-green-600');
        if (counterText) {
            counterText.textContent = `(${currentImageCount}/3) images captured`;
        }
        
        if (currentImageCount >= 3) {
            // Disable capture area when 3 images are captured
            captureArea.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
            captureArea.classList.remove('cursor-pointer', 'bg-gray-50', 'hover:bg-blue-50');
            captureArea.onclick = null;
            
            // Update text to show limit reached
            const titleText = captureArea.querySelector('h4');
            const descText = captureArea.querySelector('.text-sm.text-gray-600');
            if (titleText) titleText.textContent = '✅ All Images Captured';
            if (descText) descText.textContent = 'Maximum 3 images captured successfully';
            if (counterText) {
                counterText.textContent = '(3/3) Complete';
                counterText.classList.remove('text-blue-600');
                counterText.classList.add('text-green-600');
            }
        } else {
            // Enable capture area when less than 3 images
            captureArea.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
            captureArea.classList.add('cursor-pointer', 'bg-gray-50');
            captureArea.onclick = captureImage;
            
            // Reset text
            const titleText = captureArea.querySelector('h4');
            const descText = captureArea.querySelector('.text-sm.text-gray-600');
            if (titleText) titleText.textContent = '📷 Capture with Camera';
            if (descText) descText.textContent = 'Take 3 photos directly with your device camera';
            if (counterText) {
                counterText.classList.remove('text-green-600');
                counterText.classList.add('text-blue-600');
            }
        }
        
        // Show/hide captured images area
        if (currentImageCount > 0) {
            console.log('Showing captured images container'); // Debug log
            capturedImagesDiv.classList.remove('hidden');
        } else {
            console.log('Hiding captured images container'); // Debug log
            capturedImagesDiv.classList.add('hidden');
        }
    }

    // Remove captured image
    function removeImage(button) {
        const imageContainer = button.closest('.relative.group');
        const capturedImagesDiv = document.getElementById('captured-images');
        const currentImageCount = capturedImagesDiv.children.length;
        
        // Don't allow removal if it would go below 3 images and we already have 3
        if (currentImageCount === 3) {
            // Allow removal, user can capture new image
            imageContainer.remove();
            
            // Reset the file input to allow new captures
            const input = document.getElementById('images');
            input.value = '';
            
            // Update UI
            updateCaptureAreaUI();
            renumberImages();
            
            showNotification('Image removed. You can now capture a new image.', 'success');
        } else {
            // For cases with less than 3 images, don't allow removal
            showNotification('You must have exactly 3 images. Please capture more images instead of removing.', 'error');
        }
    }

    // Renumber images after removal
    function renumberImages() {
        const capturedImagesDiv = document.getElementById('captured-images');
        const images = capturedImagesDiv.children;
        
        for (let i = 0; i < images.length; i++) {
            const imageText = images[i].querySelector('p');
            if (imageText) {
                imageText.textContent = `Image ${i + 1}`;
            }
        }
    }

    // Validate form before submission
    function validateForm() {
        const capturedImagesDiv = document.getElementById('captured-images');
        const imageCount = capturedImagesDiv.children.length;
        
        if (imageCount !== 3) {
            showNotification(`You must capture exactly 3 images. Current: ${imageCount}/3`, 'error');
            return false;
        }
        
        return true;
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 text-sm`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Slide in
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        
        // Slide out and remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }

    // Enhanced form validation
    document.getElementById('incidentForm').addEventListener('submit', function(e) {
        const type = document.getElementById('type').value;
        const description = document.getElementById('description').value.trim();
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;

        if (!type || !description || !latitude || !longitude) {
            e.preventDefault();
            showNotification('Please fill in all required fields.', 'error');
            return;
        }
    });

    // Auto-resize textarea
    document.getElementById('description').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 200) + 'px';
    });

    // Initialize capture area UI on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCaptureAreaUI();
        // Auto-get location on page load
        getCurrentLocation();
    });
</script>
@endsection
