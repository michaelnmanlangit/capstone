<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Community extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'cover_image',
        'is_public',
        'allow_posts',
        'require_approval',
        'allowed_roles',
        'barangay',
        'city',
        'created_by',
        'moderators',
        'total_posts',
        'total_members',
        'last_activity',
        'is_active',
    ];

    protected $casts = [
        'allowed_roles' => 'array',
        'moderators' => 'array',
        'is_public' => 'boolean',
        'allow_posts' => 'boolean',
        'require_approval' => 'boolean',
        'is_active' => 'boolean',
        'total_posts' => 'integer',
        'total_members' => 'integer',
        'last_activity' => 'datetime',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class)->where('status', 'published');
    }

    public function pinnedPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class)->where('is_pinned', true);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByBarangay($query, $barangay)
    {
        return $query->where('barangay', $barangay);
    }

    // Helper methods
    public function canUserPost(User $user): bool
    {
        if (!$this->allow_posts || !$this->is_active) {
            return false;
        }

        // Check if user role is allowed
        if ($this->allowed_roles && !in_array($user->role, $this->allowed_roles)) {
            return false;
        }

        return true;
    }

    public function isModerator(User $user): bool
    {
        return $this->created_by === $user->id || 
               ($this->moderators && in_array($user->id, $this->moderators));
    }

    public function incrementPostCount(): void
    {
        $this->increment('total_posts');
        $this->update(['last_activity' => now()]);
    }

    public function decrementPostCount(): void
    {
        $this->decrement('total_posts');
    }
}
