@extends('layouts.app')

@push('styles')
<style>
mark {
    background-color: #fef08a;
    padding: 0.1em 0.2em;
    border-radius: 0.2em;
}
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form action="{{ route('community.search') }}" method="GET" class="flex gap-2">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search posts and people..." 
                        value="{{ request('q') }}"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Create Post Form -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <form action="{{ route('community.posts.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
                @csrf
                
                <!-- User Info -->
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                </div>

                <!-- Post Content -->
                <div class="mb-4">
                    <textarea 
                        name="content" 
                        placeholder="What's on your mind, {{ Auth::user()->name }}?" 
                        rows="3"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                    ></textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Media Upload Options -->
                <div class="flex items-center justify-between">
                    <div class="flex space-x-4">
                        <!-- Image Upload -->
                        <label class="cursor-pointer flex items-center text-gray-600 hover:text-blue-600 transition-colors">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm">Photo</span>
                            <input type="file" name="images[]" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                        </label>

                        <!-- Video Upload -->
                        <label class="cursor-pointer flex items-center text-gray-600 hover:text-blue-600 transition-colors">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                            <span class="text-sm">Video</span>
                            <input type="file" name="video" accept="video/*" class="hidden" onchange="previewVideo(this)">
                        </label>
                    </div>

                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Post
                    </button>
                </div>

                <!-- Media Preview -->
                <div id="mediaPreview" class="mt-4 hidden">
                    <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-3 gap-2"></div>
                    <div id="videoPreview"></div>
                </div>
            </form>
        </div>

        <!-- Posts Feed -->
        <div class="space-y-6">
            @forelse($posts as $post)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden" data-post-id="{{ $post->id }}">
                    <!-- Post Header -->
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $post->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }} • {{ ucfirst($post->user->role) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="p-4">
                        <p class="text-gray-900 mb-4">{{ $post->content }}</p>

                        <!-- Images -->
                        @if($post->images && count($post->images) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                                @foreach($post->images as $image)
                                    <img src="{{ Storage::url($image) }}" alt="Post image" class="rounded-lg w-full h-64 object-cover">
                                @endforeach
                            </div>
                        @endif

                        <!-- Video -->
                        @if($post->video_path)
                            <div class="mb-4">
                                <video controls class="w-full rounded-lg">
                                    <source src="{{ Storage::url($post->video_path) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @endif
                    </div>

                    <!-- Post Stats -->
                    <div class="px-4 py-2 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span class="reactions-count">{{ $post->likes_count }} {{ $post->likes_count == 1 ? 'reaction' : 'reactions' }}</span>
                            <span class="comments-count">{{ $post->comments_count }} {{ $post->comments_count == 1 ? 'comment' : 'comments' }}</span>
                        </div>
                    </div>

                    <!-- Post Actions -->
                    <div class="px-4 py-3 border-t border-gray-100">
                        <div class="flex items-center justify-around">
                            <!-- Reactions -->
                            <div class="relative group">
                                <button class="reaction-btn flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-post-id="{{ $post->id }}">
                                    @php
                                        $userReaction = $post->reactions->where('user_id', Auth::id())->first();
                                    @endphp
                                    <span class="reaction-emoji">
                                        @if($userReaction)
                                            {{ \App\Models\PostReaction::getReactionTypes()[$userReaction->reaction_type] }}
                                        @else
                                            👍
                                        @endif
                                    </span>
                                    <span class="reaction-text text-sm {{ $userReaction ? 'text-blue-600 font-medium' : 'text-gray-600' }}">
                                        {{ $userReaction ? ucfirst($userReaction->reaction_type) : 'Like' }}
                                    </span>
                                </button>
                                
                                <!-- Reaction Options -->
                                <div class="absolute bottom-full left-0 mb-2 bg-white rounded-full shadow-lg border p-2 flex space-x-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    @foreach(\App\Models\PostReaction::getReactionTypes() as $type => $emoji)
                                        <button class="reaction-option w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-lg" data-reaction="{{ $type }}">
                                            {{ $emoji }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Comment Button -->
                            <button class="comment-toggle flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-post-id="{{ $post->id }}">
                                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.255-.949L5 20l1.395-3.72C5.512 15.042 5 13.574 5 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Comment</span>
                            </button>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="comments-section" data-post-id="{{ $post->id }}" style="display: none;">
                        <div class="border-t border-gray-100 p-4">
                            <!-- Comment Form -->
                            <form class="comment-form flex space-x-3 mb-4" data-post-id="{{ $post->id }}">
                                @csrf
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        name="content" 
                                        placeholder="Write a comment..." 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    >
                                </div>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                                    Post
                                </button>
                            </form>

                            <!-- Comments List -->
                            <div class="comments-list space-y-3">
                                @foreach($post->comments->whereNull('parent_id') as $comment)
                                    <div class="flex space-x-3">
                                        <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="bg-gray-100 rounded-lg px-3 py-2">
                                                <p class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                                <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                                            </div>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No posts yet</h3>
                    <p class="text-gray-600">Be the first to share something with the community!</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for interactions -->
<script>
// Media Preview Functions
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    const mediaPreview = document.getElementById('mediaPreview');
    
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        mediaPreview.classList.remove('hidden');
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                    <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        mediaPreview.classList.add('hidden');
    }
}

function previewVideo(input) {
    const preview = document.getElementById('videoPreview');
    const mediaPreview = document.getElementById('mediaPreview');
    
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        mediaPreview.classList.remove('hidden');
        
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="relative mt-2">
                    <video src="${e.target.result}" class="w-full h-64 object-cover rounded-lg" controls></video>
                    <button type="button" onclick="removeVideo()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    } else {
        checkMediaPreview();
    }
}

function removeVideo() {
    document.querySelector('input[name="video"]').value = '';
    document.getElementById('videoPreview').innerHTML = '';
    checkMediaPreview();
}

function checkMediaPreview() {
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    const mediaPreview = document.getElementById('mediaPreview');
    
    if (imagePreview.children.length === 0 && videoPreview.children.length === 0) {
        mediaPreview.classList.add('hidden');
    }
}

// Comment Toggle
document.addEventListener('click', function(e) {
    if (e.target.closest('.comment-toggle')) {
        const postId = e.target.closest('.comment-toggle').dataset.postId;
        const commentsSection = document.querySelector(`.comments-section[data-post-id="${postId}"]`);
        
        if (commentsSection.style.display === 'none') {
            commentsSection.style.display = 'block';
        } else {
            commentsSection.style.display = 'none';
        }
    }
});

// Reaction Handling
document.addEventListener('click', function(e) {
    if (e.target.closest('.reaction-option')) {
        const reactionType = e.target.closest('.reaction-option').dataset.reaction;
        const postId = e.target.closest('.reaction-btn').dataset.postId;
        
        fetch(`/community/posts/${postId}/react`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reaction_type: reactionType })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                const reactionBtn = postElement.querySelector('.reaction-btn');
                const reactionEmoji = reactionBtn.querySelector('.reaction-emoji');
                const reactionText = reactionBtn.querySelector('.reaction-text');
                const reactionsCount = postElement.querySelector('.reactions-count');
                
                if (data.user_reaction) {
                    const reactionTypes = {
                        'like': '👍', 'love': '❤️', 'care': '🤗', 
                        'haha': '😂', 'wow': '😮', 'sad': '😢', 'angry': '😠'
                    };
                    reactionEmoji.textContent = reactionTypes[data.user_reaction];
                    reactionText.textContent = data.user_reaction.charAt(0).toUpperCase() + data.user_reaction.slice(1);
                    reactionText.className = 'reaction-text text-sm text-blue-600 font-medium';
                } else {
                    reactionEmoji.textContent = '👍';
                    reactionText.textContent = 'Like';
                    reactionText.className = 'reaction-text text-sm text-gray-600';
                }
                
                reactionsCount.textContent = `${data.reactions_count} ${data.reactions_count === 1 ? 'reaction' : 'reactions'}`;
            }
        });
    }
});

// Comment Submission
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('comment-form')) {
        e.preventDefault();
        
        const form = e.target;
        const postId = form.dataset.postId;
        const formData = new FormData(form);
        
        fetch(`/community/posts/${postId}/comment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentsList = form.parentElement.querySelector('.comments-list');
                const newComment = document.createElement('div');
                newComment.className = 'flex space-x-3';
                newComment.innerHTML = `
                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                        ${data.comment.user.name.charAt(0)}
                    </div>
                    <div class="flex-1">
                        <div class="bg-gray-100 rounded-lg px-3 py-2">
                            <p class="text-sm font-semibold text-gray-900">${data.comment.user.name}</p>
                            <p class="text-sm text-gray-700">${data.comment.content}</p>
                        </div>
                        <div class="flex items-center space-x-4 mt-1">
                            <span class="text-xs text-gray-500">${data.comment.created_at}</span>
                        </div>
                    </div>
                `;
                
                commentsList.appendChild(newComment);
                form.querySelector('input[name="content"]').value = '';
                
                // Update comments count
                const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                const commentsCount = postElement.querySelector('.comments-count');
                commentsCount.textContent = `${data.comments_count} ${data.comments_count === 1 ? 'comment' : 'comments'}`;
            }
        });
    }
});
</script>
@endsection
