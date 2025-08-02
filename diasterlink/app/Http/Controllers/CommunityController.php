<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    /**
     * Display the community feed
     */
    public function index()
    {
        $posts = CommunityPost::with(['user', 'reactions.user', 'comments.user'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('community.index', compact('posts'));
    }

    /**
     * Search posts and users
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->route('community.index');
        }

        $posts = CommunityPost::with(['user', 'reactions.user', 'comments.user'])
            ->where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('content', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $users = User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();

        return view('community.search', compact('posts', 'users', 'query'));
    }
}
