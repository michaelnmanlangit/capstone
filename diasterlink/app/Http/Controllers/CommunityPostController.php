<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\PostReaction;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class CommunityPostController extends Controller
{
    /**
     * Store a new community post
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'nullable|mimetypes:video/mp4,video/avi,video/quicktime|max:20480'
        ]);

        $post = new CommunityPost();
        $post->user_id = Auth::id();
        $post->content = $request->content;
        $post->status = 'published';

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('community/images', 'public');
            $post->image_path = $imagePath;
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoPath = $video->store('community/videos', 'public');
            $post->video_path = $videoPath;
        }

        $post->save();

        return redirect()->route('community.index')->with('success', 'Post created successfully!');
    }

    /**
     * Handle post reactions (like/love/etc)
     */
    public function react(Request $request, CommunityPost $post): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:like,love,haha,wow,sad,angry'
        ]);

        $userId = Auth::id();
        $reactionType = $request->type;

        // Check if user already reacted to this post
        $existingReaction = PostReaction::where('user_id', $userId)
            ->where('community_post_id', $post->id)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->type === $reactionType) {
                // Same reaction - remove it
                $existingReaction->delete();
                $action = 'removed';
            } else {
                // Different reaction - update it
                $existingReaction->type = $reactionType;
                $existingReaction->save();
                $action = 'updated';
            }
        } else {
            // New reaction
            PostReaction::create([
                'user_id' => $userId,
                'community_post_id' => $post->id,
                'type' => $reactionType
            ]);
            $action = 'added';
        }

        // Get updated reaction counts
        $reactions = PostReaction::where('community_post_id', $post->id)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return response()->json([
            'success' => true,
            'action' => $action,
            'reactions' => $reactions,
            'total' => array_sum($reactions)
        ]);
    }

    /**
     * Add a comment to a post
     */
    public function comment(Request $request, CommunityPost $post): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:500'
        ]);

        $comment = PostComment::create([
            'user_id' => Auth::id(),
            'community_post_id' => $post->id,
            'content' => $request->content
        ]);

        // Load the user relationship
        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&color=7F9CF5&background=EBF4FF'
                ],
                'created_at' => $comment->created_at->diffForHumans()
            ]
        ]);
    }
}
