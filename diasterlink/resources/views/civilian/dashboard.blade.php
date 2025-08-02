@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg p-4 mb-8 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-black mb-1">Welcome, {{ Auth::user()->name ?? 'Maria' }}!</h2>
                    <p class="text-xs text-gray-600">"Stay informed, stay safe. Together we build a resilient community."</p>
                </div>
                <div class="text-right">
                    <!-- Add any additional header content here -->
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
            <!-- SOS Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 h-44">
                <div class="mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-red-600 mb-1">Send SOS</h3>
                            <p class="text-xs text-gray-600">Request immediate emergency assistance</p>
                        </div>
                        <div class="flex items-center justify-center w-7 h-7 ml-4">
                            <svg class="w-5 h-5 text-red-600" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" preserveAspectRatio="xMidYMid meet" fill="currentColor">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M62 52c0 5.5-4.5 10-10 10H12C6.5 62 2 57.5 2 52V12C2 6.5 6.5 2 12 2h40c5.5 0 10 4.5 10 10v40z" fill="currentColor"></path>
                                    <g fill="#ffffff">
                                        <path d="M23 34.6c-.4-.8-.9-1.5-1.5-2.1c-.6-.6-1.4-1.1-2.2-1.5c-.9-.4-1.7-.6-2.7-.6c-1.9 0-3.9-1.9-3.9-3.7c0-2 1.7-3.6 3.7-3.6s3.9 1.6 3.9 3.6h3.2c0-.9-.2-1.8-.5-2.6s-.8-1.5-1.5-2.2c-.6-.6-1.4-1.1-2.2-1.5c-.8-.4-2-.5-2.9-.5s-1.8.2-2.7.5c-.8.3-1.6.8-2.2 1.4c-.6.6-1.1 1.3-1.5 2.2c-.4.8-.5 1.7-.5 2.6c0 1.7.8 3.4 2.2 4.7c1.4 1.3 3.2 2.1 4.9 2.1c1.9 0 3.7 1.7 3.7 3.7s-1.7 3.6-3.7 3.6s-3.9-1.7-3.9-3.7H9.6c0 .9.2 1.8.5 2.6s.8 1.5 1.5 2.2c.6.6 1.4 1.1 2.2 1.4c.8.4 2 .6 2.9.6c.9 0 1.8-.2 2.7-.5c.8-.3 1.6-.8 2.2-1.5c.6-.6 1.1-1.3 1.5-2.2c.4-.8.5-1.7.5-2.6c0-.7-.2-1.5-.6-2.4"></path>
                                        <path d="M39 24.1c-.4-.8-.9-1.5-1.5-2.2s-1.4-1.1-2.2-1.5c-.9-.4-1.8-.5-2.7-.5s-1.9.2-2.7.5c-.8.3-1.6.8-2.2 1.5c-.6.6-1.1 1.3-1.5 2.2c-.4.8-.6 1.7-.6 2.6v10.4c0 .9.2 1.8.6 2.6c.4.8.9 1.5 1.5 2.2c.6.6 1.4 1.1 2.2 1.5c.9.4 1.8.5 2.7.5c.9 0 1.9-.2 2.7-.5c.8-.3 1.6-.8 2.2-1.5c.6-.6 1.1-1.3 1.5-2.2c.4-.8.6-1.7.6-2.6V26.8c0-.9-.2-1.8-.6-2.7m-6.4 16.8c-2.1 0-3.8-1.6-3.8-3.7V26.8c0-2 1.7-3.7 3.8-3.7s3.8 1.6 3.8 3.7v10.4c-.1 2-1.8 3.7-3.8 3.7"></path>
                                        <path d="M55 34.6c-.4-.8-.9-1.5-1.5-2.1c-.6-.6-1.4-1.1-2.2-1.5c-.9-.4-1.8-.6-2.7-.6c-1.9 0-3.9-1.9-3.9-3.7c0-2 1.7-3.6 3.7-3.6s3.9 1.6 3.9 3.6h3.2c0-.9-.2-1.8-.5-2.6s-.8-1.5-1.5-2.2c-.6-.6-1.4-1.1-2.2-1.5c-.9-.4-2-.5-2.9-.5s-1.8.2-2.7.5c-.8.3-1.6.8-2.2 1.5c-.6.6-1.1 1.3-1.5 2.2c-.4.8-.5 1.7-.5 2.6c0 1.7.8 3.4 2.2 4.7c1.4 1.3 3.2 2.1 4.9 2.1c1.9 0 3.7 1.7 3.7 3.7s-1.7 3.6-3.7 3.6s-3.9-1.7-3.9-3.7h-3.2c0 .9.2 1.8.5 2.6s.8 1.5 1.5 2.2c.6.6 1.4 1.1 2.2 1.4c.9.4 2 .6 2.9.6s1.8-.2 2.7-.5c.8-.3 1.6-.8 2.2-1.4c.6-.6 1.1-1.3 1.5-2.2c.4-.8.5-1.7.5-2.6c.1-.9-.1-1.7-.5-2.6"></path>
                                    </g>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <a 
                    href="{{ route('civilian.sos') }}" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-normal py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 inline-block text-center"
                >
                    SOS
                </a>
            </div>

            <!-- Report Incident Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 h-44">
                <div class="mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-blue-600 mb-1">Capture Incident</h3>
                            <p class="text-xs text-gray-600">Report a disaster situation</p>
                        </div>
                        <div class="flex items-center justify-center w-7 h-7 ml-4">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 2L7.17 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4H16.83L15 2H9ZM12 7C14.76 7 17 9.24 17 12S14.76 17 12 17S7 14.76 7 12S9.24 7 12 7Z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <button 
                    onclick="reportIncident()" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-normal py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Report Now
                </button>
            </div>
        </div>

        <!-- Recent Activity Section (Optional) -->
        <div class="mt-8 max-w-6xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="text-sm text-gray-600">
                    <p>No recent activity to display.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function sendSOS() {
        if (confirm('Are you sure you want to send an SOS alert? This will notify emergency responders immediately.')) {
            // Get user location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const data = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        type: 'emergency',
                        message: 'Emergency SOS Alert'
                    };
                    
                    // Send SOS request
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
                        if (data.success) {
                            alert('SOS alert sent successfully! Emergency responders have been notified.');
                        } else {
                            alert('Failed to send SOS alert. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to send SOS alert. Please check your connection and try again.');
                    });
                }, function(error) {
                    alert('Location access denied. SOS alert sent without location.');
                    // Send SOS without location
                    sendSOSWithoutLocation();
                });
            } else {
                alert('Geolocation not supported. SOS alert sent without location.');
                sendSOSWithoutLocation();
            }
        }
    }

    function sendSOSWithoutLocation() {
        const data = {
            type: 'emergency',
            message: 'Emergency SOS Alert - Location not available'
        };
        
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
            if (data.success) {
                alert('SOS alert sent successfully! Emergency responders have been notified.');
            } else {
                alert('Failed to send SOS alert. Please try again.');
            }
        });
    }

    function reportIncident() {
        // Navigate to incident reporting page
        window.location.href = '{{ route("civilian.incident") }}';
    }

    // Auto-refresh for real-time updates (optional)
    setInterval(function() {
        // Check for new notifications or updates
        fetch('{{ route("notifications.check") }}')
            .then(response => response.json())
            .then(data => {
                if (data.hasNew) {
                    // Update notification indicator
                    updateNotificationBadge(data.count);
                }
            })
            .catch(error => console.log('Notification check failed'));
    }, 30000); // Check every 30 seconds

    function updateNotificationBadge(count) {
        // Add notification badge logic here
        console.log(`${count} new notifications`);
    }
</script>
@endsection
