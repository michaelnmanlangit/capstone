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
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Facebook-like reactions
            $table->string('reaction_type')->default('like'); // like, love, care, haha, wow, sad, angry
            
            $table->timestamps();
            
            // Unique constraint - one reaction per user per post
            $table->unique(['post_id', 'user_id']);
            
            // Indexes for quick reaction counting
            $table->index(['post_id', 'reaction_type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
