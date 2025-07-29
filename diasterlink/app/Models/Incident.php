<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'incident_type',
        'severity',
        'status',
        'latitude',
        'longitude',
        'location_address',
        'barangay',
        'images',
        'video_path',
        'is_verified',
        'ml_confidence_score',
        'ml_verification_details',
        'verified_at',
        'verified_by',
        'responded_at',
        'responded_by',
        'response_notes',
        'priority_score',
        'is_public',
        'metadata',
    ];

    protected $casts = [
        'images' => 'array',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'ml_confidence_score' => 'decimal:4',
        'verified_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function notifications(): HasMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // ML Image Verification Methods
    
    /**
     * Check if the incident images are verified as authentic by ML
     */
    public function isImageAuthentic(): bool
    {
        return $this->is_verified && $this->ml_confidence_score >= 0.7;
    }
    
    /**
     * Get ML verification status text
     */
    public function getVerificationStatusAttribute(): string
    {
        if (!$this->ml_confidence_score) {
            return 'Not Processed';
        }
        
        if ($this->ml_confidence_score >= 0.8) {
            return 'Highly Authentic';
        } elseif ($this->ml_confidence_score >= 0.6) {
            return 'Likely Authentic';
        } elseif ($this->ml_confidence_score >= 0.4) {
            return 'Uncertain';
        } else {
            return 'Likely Fake';
        }
    }
    
    /**
     * Get ML confidence percentage
     */
    public function getConfidencePercentageAttribute(): int
    {
        return $this->ml_confidence_score ? round($this->ml_confidence_score * 100) : 0;
    }
    
    /**
     * Scope for ML verified authentic images
     */
    public function scopeAuthenticImages($query)
    {
        return $query->where('is_verified', true)
                    ->where('ml_confidence_score', '>=', 0.6);
    }
    
    /**
     * Scope for suspected fake images
     */
    public function scopeSuspectedFake($query)
    {
        return $query->where('ml_confidence_score', '<', 0.4);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeInLocation($query, $latitude, $longitude, $radius = 10)
    {
        // Find incidents within radius (in kilometers)
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
            'pending' => 'yellow',
            'verified' => 'blue',
            'investigating' => 'purple',
            'resolved' => 'green',
            default => 'gray',
        };
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }
}
