<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReaction extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'reaction_type',
    ];

    // Relationships
    public function post(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Available reaction types (Facebook-like)
    public static function getReactionTypes(): array
    {
        return [
            'like' => '👍',
            'love' => '❤️',
            'care' => '🤗',
            'haha' => '😂',
            'wow' => '😮',
            'sad' => '😢',
            'angry' => '😠',
        ];
    }

    public function getReactionEmojiAttribute(): string
    {
        return self::getReactionTypes()[$this->reaction_type] ?? '👍';
    }
}
