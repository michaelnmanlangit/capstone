<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Post content
            $table->text('content');
            $table->string('post_type')->default('text'); // text, image, video, link, announcement
            
            // Media attachments
            $table->json('images')->nullable(); // Multiple images
            $table->string('video_path')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_title')->nullable();
            $table->text('link_description')->nullable();
            
            // Location (optional for posts)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            
            // Post settings
            $table->boolean('is_pinned')->default(false); // Pinned by moderators
            $table->boolean('is_announcement')->default(false); // Official announcements
            $table->boolean('allow_comments')->default(true);
            $table->boolean('is_public')->default(true);
            
            // Moderation
            $table->string('status')->default('published'); // draft, pending, published, hidden, deleted
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('moderation_notes')->nullable();
            
            // Engagement metrics (for performance)
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('views_count')->default(0);
            
            // Activity tracking
            $table->timestamp('last_activity')->nullable(); // Last comment/reaction
            
            $table->timestamps();
            
            // Indexes for Facebook-like feed performance
            $table->index(['community_id', 'status', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_pinned', 'created_at']);
            $table->index(['post_type', 'status']);
            $table->index(['last_activity']); // For "most active" sorting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};
