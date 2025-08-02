<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostComment extends Model
{
    protected $fillable = [
        'user_id',
        'community_post_id',
        'content'
    ];

    /**
     * Get the user that made the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that this comment belongs to
     */
    public function communityPost(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class);
    }
}
