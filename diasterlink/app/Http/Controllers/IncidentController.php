<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incidents = Incident::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('incidents.index', compact('incidents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('incidents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max per image
                'status' => 'in:reported,verified,investigating,resolved',
            ]);

            // Create the incident
            $incident = Incident::create([
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'description' => $validated['description'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'severity' => 'medium', // Default severity level
                'status' => $validated['status'] ?? 'reported',
                'images' => [], // Will be updated if images are uploaded
                'metadata' => json_encode([
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'reported_at' => now()->toISOString()
                ])
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('incidents/' . $incident->id, 'public');
                    $imagePaths[] = $path;
                }
                
                $incident->update(['images' => json_encode($imagePaths)]);
                
                // TODO: Send images to ML API for authenticity verification
                // $this->verifyImagesWithML($incident, $imagePaths);
            }

            Log::info('Incident Created', [
                'incident_id' => $incident->id,
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'severity' => $validated['severity']
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Incident reported successfully.',
                    'incident_id' => $incident->id,
                    'redirect_url' => route('incidents.show', $incident)
                ]);
            }

            return redirect()->route('incidents.show', $incident)
                ->with('success', 'Incident reported successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data provided.',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Incident Creation Failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to report incident. Please try again.'
                ], 500);
            }

            return back()->with('error', 'Failed to report incident. Please try again.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        // Ensure user can view their own incidents or is admin/responder
        if ($incident->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'responder'])) {
            abort(403, 'Unauthorized access to incident.');
        }

        return view('incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        // Only allow user to edit their own incidents
        if ($incident->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this incident.');
        }

        // Don't allow editing of resolved incidents
        if ($incident->status === 'resolved') {
            return redirect()->route('incidents.show', $incident)
                ->with('error', 'Resolved incidents cannot be edited.');
        }

        return view('incidents.edit', compact('incident'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        // Only allow user to update their own incidents
        if ($incident->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to update this incident.');
        }

        try {
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            $incident->update($validated);

            Log::info('Incident Updated', [
                'incident_id' => $incident->id,
                'user_id' => Auth::id(),
                'changes' => $incident->getChanges()
            ]);

            return redirect()->route('incidents.show', $incident)
                ->with('success', 'Incident updated successfully.');

        } catch (\Exception $e) {
            Log::error('Incident Update Failed', [
                'incident_id' => $incident->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to update incident.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        // Only allow user to delete their own incidents
        if ($incident->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to delete this incident.');
        }

        try {
            // Delete associated images
            if ($incident->images) {
                $images = json_decode($incident->images, true);
                foreach ($images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $incident->delete();

            Log::info('Incident Deleted', [
                'incident_id' => $incident->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('incidents.index')
                ->with('success', 'Incident deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Incident Deletion Failed', [
                'incident_id' => $incident->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete incident.');
        }
    }

    /**
     * Verify images with ML API (placeholder for future implementation)
     */
    private function verifyImagesWithML(Incident $incident, array $imagePaths)
    {
        // TODO: Implement ML image verification
        // This would send images to the ML API for authenticity check
        Log::info('ML Verification Queued', [
            'incident_id' => $incident->id,
            'image_count' => count($imagePaths)
        ]);
    }
}
