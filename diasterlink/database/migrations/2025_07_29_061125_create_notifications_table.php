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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Recipient
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null'); // Who triggered it
            
            // Notification content
            $table->string('type'); // incident_verified, sos_response, community_post, system_alert, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data (URLs, IDs, etc.)
            
            // Related entities (polymorphic relationships)
            $table->string('notifiable_type')->nullable(); // Incident, SosRequest, CommunityPost, etc.
            $table->unsignedBigInteger('notifiable_id')->nullable();
            
            // Delivery channels
            $table->boolean('sent_via_email')->default(false);
            $table->boolean('sent_via_sms')->default(false);
            $table->boolean('sent_via_push')->default(false);
            $table->boolean('sent_via_web')->default(true);
            
            // Status tracking
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            
            // Delivery tracking
            $table->timestamp('scheduled_for')->nullable(); // For delayed notifications
            $table->timestamp('delivered_at')->nullable();
            $table->json('delivery_status')->nullable(); // Track delivery across channels
            
            // Action tracking
            $table->string('action_url')->nullable(); // Where to redirect when clicked
            $table->boolean('requires_action')->default(false);
            $table->timestamp('action_taken_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for notification performance
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['type', 'priority', 'created_at']);
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['scheduled_for', 'delivered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
