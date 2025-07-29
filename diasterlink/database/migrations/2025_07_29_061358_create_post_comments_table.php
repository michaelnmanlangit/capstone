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
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Nested comments support (replies to comments)
            $table->foreignId('parent_comment_id')->nullable()->constrained('post_comments')->onDelete('cascade');
            
            // Comment content
            $table->text('content');
            $table->string('image_path')->nullable(); // Comments can have images too
            
            // Moderation
            $table->string('status')->default('published'); // published, hidden, deleted
            $table->text('moderation_reason')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Engagement on comments
            $table->integer('likes_count')->default(0);
            $table->integer('replies_count')->default(0); // Count of child comments
            
            // Tracking
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for threaded comments performance
            $table->index(['post_id', 'parent_comment_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['parent_comment_id', 'created_at']); // For replies
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
