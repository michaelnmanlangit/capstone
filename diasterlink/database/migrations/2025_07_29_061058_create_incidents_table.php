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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('incident_type'); // flood, earthquake, fire, etc.
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('pending'); // pending, verified, investigating, resolved
            
            // Location data
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_address')->nullable();
            $table->string('barangay')->nullable();
            
            // Media attachments
            $table->json('images')->nullable(); // Store multiple image paths
            $table->string('video_path')->nullable();
            
            // ML Verification
            $table->boolean('is_verified')->default(false);
            $table->decimal('ml_confidence_score', 5, 4)->nullable(); // 0.0000 to 1.0000
            $table->text('ml_verification_details')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Response tracking
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('response_notes')->nullable();
            
            // Additional metadata
            $table->integer('priority_score')->default(0); // Auto-calculated priority
            $table->boolean('is_public')->default(true);
            $table->json('metadata')->nullable(); // Store additional data like weather, etc.
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['incident_type', 'severity']);
            $table->index(['latitude', 'longitude']);
            $table->index(['is_verified', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
