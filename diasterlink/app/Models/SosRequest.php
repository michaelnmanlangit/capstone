<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SosRequest extends Model
{
    protected $fillable = [
        'user_id',
        'emergency_type',
        'message',
        'severity',
        'status',
        'latitude',
        'longitude',
        'location_address',
        'nearest_landmark',
        'contact_number',
        'alternate_contact',
        'people_affected',
        'acknowledged_at',
        'acknowledged_by',
        'responded_at',
        'responded_by',
        'resolved_at',
        'response_notes',
        'notified_contacts',
        'last_notification_sent',
        'notification_count',
        'image_path',
        'audio_path',
        'device_info',
        'is_test',
    ];

    protected $casts = [
        'notified_contacts' => 'array',
        'device_info' => 'array',
        'people_affected' => 'integer',
        'notification_count' => 'integer',
        'is_test' => 'boolean',
        'acknowledged_at' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'last_notification_sent' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function notifications(): HasMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByEmergencyType($query, $type)
    {
        return $query->where('emergency_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeNearLocation($query, $latitude, $longitude, $radius = 5)
    {
        // Find SOS requests within radius (in kilometers) - smaller radius for emergencies
        return $query->whereRaw(
            "ST_Distance_Sphere(
                POINT(longitude, latitude),
                POINT(?, ?)
            ) <= ? * 1000",
            [$longitude, $latitude, $radius]
        );
    }

    // Accessors & Mutators
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'red',
            'responding' => 'yellow',
            'resolved' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    public function getUrgencyScoreAttribute()
    {
        $baseScore = match($this->severity) {
            'critical' => 100,
            'high' => 80,
            'medium' => 60,
            default => 40,
        };

        // Increase urgency based on time elapsed
        $minutesElapsed = $this->created_at->diffInMinutes(now());
        $timeUrgency = min($minutesElapsed * 2, 20); // Max 20 points for time

        return $baseScore + $timeUrgency;
    }

    public function getResponseTimeAttribute()
    {
        if ($this->responded_at) {
            return $this->created_at->diffInMinutes($this->responded_at);
        }
        return null;
    }
}
