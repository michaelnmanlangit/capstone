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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('general'); // general, emergency, announcement, barangay-specific
            $table->string('cover_image')->nullable();
            
            // Community settings
            $table->boolean('is_public')->default(true);
            $table->boolean('allow_posts')->default(true);
            $table->boolean('require_approval')->default(false); // Posts need admin approval
            $table->json('allowed_roles')->nullable(); // Which roles can post ['admin', 'responder', 'civilian']
            
            // Geographic scope
            $table->string('barangay')->nullable(); // Specific to a barangay
            $table->string('city')->default('Sto. Tomas');
            
            // Admin management
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('moderators')->nullable(); // Array of user IDs who can moderate
            
            // Activity tracking
            $table->integer('total_posts')->default(0);
            $table->integer('total_members')->default(0);
            $table->timestamp('last_activity')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'is_public', 'is_active']);
            $table->index(['barangay', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
