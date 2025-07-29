<?php

namespace App\Http\Controllers;

use App\Models\SOSRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SOSController extends Controller
{
    /**
     * Store a new SOS request
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'message' => 'required|string|max:1000',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            $sosRequest = SOSRequest::create([
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'message' => $validated['message'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'pending',
                'urgency' => 'critical', // SOS is always critical
                'contact_number' => Auth::user()->phone ?? null,
            ]);

            // Log the SOS request for monitoring
            Log::emergency('SOS Request Created', [
                'sos_id' => $sosRequest->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'type' => $validated['type'],
                'location' => [
                    'latitude' => $validated['latitude'] ?? 'unknown',
                    'longitude' => $validated['longitude'] ?? 'unknown'
                ],
                'timestamp' => now()
            ]);

            // TODO: Implement real-time notifications to emergency responders
            // TODO: Integrate with emergency services API
            // TODO: Send SMS/Push notifications to emergency contacts

            return response()->json([
                'success' => true,
                'message' => 'SOS alert sent successfully. Emergency responders have been notified.',
                'sos_id' => $sosRequest->id,
                'timestamp' => $sosRequest->created_at->toISOString()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('SOS Request Failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send SOS alert. Please try again or contact emergency services directly.'
            ], 500);
        }
    }

    /**
     * Get user's SOS history
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $sosRequests = SOSRequest::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($sos) {
                    return [
                        'id' => $sos->id,
                        'type' => $sos->type,
                        'message' => $sos->message,
                        'status' => $sos->status,
                        'urgency' => $sos->urgency,
                        'created_at' => $sos->created_at->toISOString(),
                        'location' => [
                            'latitude' => $sos->latitude,
                            'longitude' => $sos->longitude
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sosRequests
            ]);

        } catch (\Exception $e) {
            Log::error('SOS History Fetch Failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve SOS history.'
            ], 500);
        }
    }

    /**
     * Cancel an SOS request (if still pending)
     */
    public function cancel(SOSRequest $sosRequest): JsonResponse
    {
        try {
            // Ensure user can only cancel their own SOS requests
            if ($sosRequest->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            // Only allow cancellation of pending requests
            if ($sosRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'SOS request cannot be cancelled as it has already been processed.'
                ], 400);
            }

            $sosRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            Log::info('SOS Request Cancelled', [
                'sos_id' => $sosRequest->id,
                'user_id' => Auth::id(),
                'cancelled_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SOS request has been cancelled.'
            ]);

        } catch (\Exception $e) {
            Log::error('SOS Cancellation Failed', [
                'sos_id' => $sosRequest->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel SOS request.'
            ], 500);
        }
    }
}
