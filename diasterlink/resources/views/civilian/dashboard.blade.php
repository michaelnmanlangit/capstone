@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg p-4 mb-8 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-black mb-1">Welcome back, {{ Auth::user()->name ?? 'Maria' }}!</h2>
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
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM12 6C14.8 6 17 8.2 17 11V16L19 18V19H5V18L7 16V11C7 8.2 9.2 6 12 6ZM10 20H14C14 21.1 13.1 22 12 22C10.9 22 10 21.1 10 20Z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <button 
                    onclick="sendSOS()" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-normal py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    SOS
                </button>
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
        window.location.href = '{{ route("incidents.create") }}';
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
