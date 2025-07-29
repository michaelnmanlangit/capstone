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
        Schema::create('sos_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Emergency details
            $table->string('emergency_type')->default('general'); // medical, fire, flood, accident, etc.
            $table->text('message')->nullable();
            $table->string('severity')->default('high'); // medium, high, critical
            $table->string('status')->default('active'); // active, responding, resolved, cancelled
            
            // Location data (crucial for SOS)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('location_address')->nullable();
            $table->string('nearest_landmark')->nullable();
            
            // Contact information
            $table->string('contact_number')->nullable();
            $table->string('alternate_contact')->nullable();
            $table->integer('people_affected')->default(1);
            
            // Response tracking
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->text('response_notes')->nullable();
            
            // Notification tracking
            $table->json('notified_contacts')->nullable(); // Track who was notified
            $table->timestamp('last_notification_sent')->nullable();
            $table->integer('notification_count')->default(0);
            
            // Media evidence
            $table->string('image_path')->nullable();
            $table->string('audio_path')->nullable(); // Voice message
            
            // Additional metadata
            $table->json('device_info')->nullable(); // Device type, battery level, etc.
            $table->boolean('is_test')->default(false); // For testing purposes
            
            $table->timestamps();
            
            // Indexes for emergency response speed
            $table->index(['status', 'severity', 'created_at']);
            $table->index(['latitude', 'longitude']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos_requests');
    }
};
