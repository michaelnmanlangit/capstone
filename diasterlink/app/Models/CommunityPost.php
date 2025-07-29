<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityPost extends Model
{
    protected $fillable = [
        'community_id',
        'user_id',
        'content',
        'post_type',
        'images',
        'video_path',
        'link_url',
        'link_title',
        'link_description',
        'latitude',
        'longitude',
        'location_name',
        'is_pinned',
        'is_announcement',
        'allow_comments',
        'is_public',
        'status',
        'approved_by',
        'approved_at',
        'moderation_notes',
        'likes_count',
        'comments_count',
        'shares_count',
        'views_count',
        'last_activity',
    ];

    protected $casts = [
        'images' => 'array',
        'is_pinned' => 'boolean',
        'is_announcement' => 'boolean',
        'allow_comments' => 'boolean',
        'is_public' => 'boolean',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'views_count' => 'integer',
        'approved_at' => 'datetime',
        'last_activity' => 'datetime',
    ];

    // Relationships
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id')->whereNull('parent_comment_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    // Facebook-like reaction methods
    public function getLikesByType(string $type = 'like')
    {
        return $this->reactions()->where('reaction_type', $type)->count();
    }

    public function hasUserReacted(User $user, string $type = 'like'): bool
    {
        return $this->reactions()
            ->where('user_id', $user->id)
            ->where('reaction_type', $type)
            ->exists();
    }

    public function getUserReaction(User $user)
    {
        return $this->reactions()
            ->where('user_id', $user->id)
            ->first();
    }

    public function toggleReaction(User $user, string $type = 'like')
    {
        $existingReaction = $this->reactions()
            ->where('user_id', $user->id)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->reaction_type === $type) {
                // Remove reaction if same type
                $existingReaction->delete();
                $this->decrement('likes_count');
                return null;
            } else {
                // Update reaction type
                $existingReaction->update(['reaction_type' => $type]);
                return $existingReaction;
            }
        } else {
            // Create new reaction
            $reaction = $this->reactions()->create([
                'user_id' => $user->id,
                'reaction_type' => $type,
            ]);
            $this->increment('likes_count');
            return $reaction;
        }
    }

    public function addComment(User $user, string $content, ?int $parentId = null)
    {
        $comment = $this->allComments()->create([
            'user_id' => $user->id,
            'content' => $content,
            'parent_comment_id' => $parentId,
        ]);

        $this->increment('comments_count');
        $this->touch('last_activity');

        return $comment;
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    // Scopes for filtering posts
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeAnnouncements($query)
    {
        return $query->where('is_announcement', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('post_type', $type);
    }

    // Facebook-like feed sorting
    public function scopeFeedOrder($query)
    {
        return $query->orderByDesc('is_pinned')
                    ->orderByDesc('last_activity')
                    ->orderByDesc('created_at');
    }

    // Helper methods for Facebook-like functionality
    public function getReactionCounts(): array
    {
        return $this->reactions()
            ->selectRaw('reaction_type, COUNT(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();
    }

    public function updateLastActivity(): void
    {
        $this->update(['last_activity' => now()]);
        $this->community->update(['last_activity' => now()]);
    }

    // Engagement score for algorithmic feed
    public function getEngagementScoreAttribute(): int
    {
        $age = $this->created_at->diffInHours(now());
        $ageWeight = max(0, 48 - $age) / 48; // Decay over 48 hours

        return (int) (
            ($this->likes_count * 1) +
            ($this->comments_count * 3) +
            ($this->shares_count * 5) +
            ($this->views_count * 0.1)
        ) * $ageWeight;
    }
}
