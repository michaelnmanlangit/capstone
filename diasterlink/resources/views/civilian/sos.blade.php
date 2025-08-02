@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900 mb-1">Emergency SOS</h1>
                    <p class="text-sm text-gray-600">Send immediate emergency alert to responders and emergency contacts</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-red-50 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path d="M62 52c0 5.5-4.5 10-10 10H12C6.5 62 2 57.5 2 52V12C2 6.5 6.5 2 12 2h40c5.5 0 10 4.5 10 10v40z" fill="currentColor"></path>
                            <g fill="#ffffff">
                                <path d="M23 34.6c-.4-.8-.9-1.5-1.5-2.1c-.6-.6-1.4-1.1-2.2-1.5c-.9-.4-1.7-.6-2.7-.6c-1.9 0-3.9-1.9-3.9-3.7c0-2 1.7-3.6 3.7-3.6s3.9 1.6 3.9 3.6h3.2c0-.9-.2-1.8-.5-2.6s-.8-1.5-1.5-2.2c-.6-.6-1.4-1.1-2.2-1.5c-.8-.4-2-.5-2.9-.5s-1.8.2-2.7.5c-.8.3-1.6.8-2.2 1.5c-.6.6-1.1 1.3-1.5 2.2c-.4.8-.5 1.7-.5 2.6v1.1c0 .9.2 1.8.5 2.6c.4.8.9 1.5 1.5 2.2c.6.6 1.4 1.1 2.2 1.5c.9.4 1.8.5 2.7.5c.9 0 1.9-.2 2.7-.5c.8-.3 1.6-.8 2.2-1.5c.6-.6 1.1-1.3 1.5-2.2c.4-.8.6-1.7.6-2.6v-1.1c0-.9-.2-1.8-.6-2.6z"/>
                                <path d="M39 24.1c-.4-.8-.9-1.5-1.5-2.2s-1.4-1.1-2.2-1.5c-.9-.4-1.8-.5-2.7-.5s-1.9.2-2.7.5c-.8.3-1.6.8-2.2 1.5c-.6.6-1.1 1.3-1.5 2.2c-.4.8-.6 1.7-.6 2.6v10.4c0 .9.2 1.8.6 2.6c.4.8.9 1.5 1.5 2.2c.6.6 1.4 1.1 2.2 1.5c.9.4 1.8.5 2.7.5c.9 0 1.9-.2 2.7-.5c.8-.3 1.6-.8 2.2-1.5c.6-.6 1.1-1.3 1.5-2.2c.4-.8.6-1.7.6-2.6V26.7c0-.9-.2-1.8-.6-2.6z"/>
                                <path d="M55 34.6c-.4-.8-.9-1.5-1.5-2.1c-.6-.6-1.4-1.1-2.2-1.5c-.9-.4-1.8-.6-2.7-.6c-1.9 0-3.9-1.9-3.9-3.7c0-2 1.7-3.6 3.7-3.6s3.9 1.6 3.9 3.6h3.2c0-.9-.2-1.8-.5-2.6s-.8-1.5-1.5-2.2c-.6-.6-1.4-1.1-2.2-1.5c-.9-.4-2-.5-2.9-.5s-1.8.2-2.7.5c-.8.3-1.6.8-2.2 1.5c-.6.6-1.1 1.3-1.5 2.2c-.4.8-.6 1.7-.6 2.6v1.1c0 .9.2 1.8.6 2.6c.4.8.9 1.5 1.5 2.2c.6.6 1.4 1.1 2.2 1.5c.9.4 1.8.5 2.7.5c.9 0 1.9-.2 2.7-.5c.8-.3 1.6-.8 2.2-1.5c.6-.6 1.1-1.3 1.5-2.2c.4-.8.6-1.7.6-2.6v-1.1c0-.9-.2-1.8-.6-2.6z"/>
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

        <!-- Main SOS Interface -->
        <div class="max-w-2xl mx-auto">
            <!-- Detailed SOS Form -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Emergency Report</h3>
                
                <form id="detailedSOSForm" class="space-y-4">
                    @csrf
                    
                    <!-- Emergency Type -->
                    <div>
                        <label for="emergency_type" class="block text-sm font-medium text-gray-700 mb-1">Emergency Type *</label>
                        <select name="emergency_type" id="emergency_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm bg-white">
                            <option value="">Select emergency type</option>
                            <option value="medical">Medical Emergency</option>
                            <option value="fire">Fire</option>
                            <option value="police">Crime/Security</option>
                            <option value="natural_disaster">Natural Disaster</option>
                            <option value="accident">Accident</option>
                            <option value="other">Other Emergency</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea 
                            name="message" 
                            id="message" 
                            rows="4" 
                            required 
                            placeholder="Describe the emergency situation in detail..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm resize-none"
                        ></textarea>
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
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm bg-gray-50"
                                    readonly
                                    required
                                >
                            </div>
                            <div>
                                <input 
                                    type="number" 
                                    name="longitude" 
                                    id="longitude" 
                                    step="any" 
                                    placeholder="Longitude" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm bg-gray-50"
                                    readonly
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Affected People -->
                    <div>
                        <label for="people_affected" class="block text-sm font-medium text-gray-700 mb-1">Number of People Affected</label>
                        <input 
                            type="number" 
                            name="people_affected" 
                            id="people_affected" 
                            min="1" 
                            max="100"
                            placeholder="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-start pt-6 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 flex items-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>Send Emergency Alert</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let isSOSActive = false;

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

    // Detailed SOS Form Handler
    document.getElementById('detailedSOSForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isSOSActive) return;
        
        const formData = new FormData(this);
        const data = {
            type: formData.get('emergency_type'),
            message: formData.get('message'),
            latitude: formData.get('latitude') || null,
            longitude: formData.get('longitude') || null,
            people_affected: formData.get('people_affected') || null
        };

        // Validate required fields
        if (!data.type || !data.message) {
            showNotification('Please fill in all required fields.', 'error');
            return;
        }

        isSOSActive = true;
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Sending Emergency Alert...</span>
        `;
        submitBtn.disabled = true;

        fetch('{{ route("sos.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            isSOSActive = false;
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (data.success) {
                showNotification('🚨 Emergency alert sent successfully! Responders have been notified.', 'success');
                this.reset(); // Clear form
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
            } else {
                showNotification('❌ Failed to send emergency alert. Please try again.', 'error');
            }
        })
        .catch(error => {
            isSOSActive = false;
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            showNotification('❌ Network error. Please check your connection and try again.', 'error');
            console.error('Error:', error);
        });
    });

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600';
        
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 text-sm`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Slide in
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    // Auto-get location on page load
    document.addEventListener('DOMContentLoaded', function() {
        getCurrentLocation();
    });

    // Prevent accidental page refresh/close during SOS
    window.addEventListener('beforeunload', function(e) {
        if (isSOSActive) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });
</script>
@endsection
