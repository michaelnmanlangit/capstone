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
                        value="{{ $query }}"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Back to Community -->
        <div class="mb-6">
            <a href="{{ route('community.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Community
            </a>
        </div>

        <!-- Search Results Header -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">
                Search results for "{{ $query }}"
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Found {{ $posts->total() }} posts
                @if($users->count() > 0)
                    and {{ $users->count() }} people
                @endif
            </p>
        </div>

        <!-- People Results -->
        @if($users->count() > 0)
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <h3 class="text-md font-semibold text-gray-900 mb-3">People</h3>
                <div class="space-y-2">
                    @foreach($users as $user)
                        <div class="flex items-center p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Posts Results -->
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
                        <p class="text-gray-900 mb-4">
                            {!! str_ireplace($query, '<mark class="bg-yellow-200">' . $query . '</mark>', e($post->content)) !!}
                        </p>

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
                            <span>{{ $post->likes_count }} {{ $post->likes_count == 1 ? 'reaction' : 'reactions' }}</span>
                            <span>{{ $post->comments_count }} {{ $post->comments_count == 1 ? 'comment' : 'comments' }}</span>
                        </div>
                    </div>

                    <!-- View Full Post Link -->
                    <div class="px-4 py-3 border-t border-gray-100">
                        <a href="{{ route('community.index') }}#post-{{ $post->id }}" class="text-blue-600 hover:text-blue-700 text-sm">
                            View full post
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20.4a7.962 7.962 0 01-5.207-1.909C6.076 17.914 6.076 17.914 6.114 17.914L3 20.5v-8.093a7.962 7.962 0 01-.207-1.907c0-4.418 3.582-8 8-8s8 3.582 8 8c0 .68-.107 1.336-.307 1.952"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No posts found</h3>
                    <p class="text-gray-600">Try searching with different keywords.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="mt-8">
                {{ $posts->appends(['q' => $query])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
