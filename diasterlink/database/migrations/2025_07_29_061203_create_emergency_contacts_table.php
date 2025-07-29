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
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            
            // Contact information
            $table->string('name');
            $table->string('organization'); // Police, Fire Dept, Hospital, etc.
            $table->string('contact_type'); // police, fire, medical, rescue, utility, government
            $table->string('phone_number');
            $table->string('alternate_number')->nullable();
            $table->string('email')->nullable();
            
            // Location and coverage
            $table->string('address')->nullable();
            $table->string('barangay')->nullable(); // Specific to barangay or city-wide
            $table->string('city')->default('Sto. Tomas');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Service details
            $table->text('description')->nullable();
            $table->json('services_offered')->nullable(); // Array of services
            $table->string('availability')->default('24/7'); // Operating hours
            $table->boolean('accepts_emergency_calls')->default(true);
            
            // Contact hierarchy
            $table->integer('priority_order')->default(1); // 1 = primary, 2 = secondary, etc.
            $table->boolean('is_primary')->default(false); // Primary contact for this type
            
            // Status and verification
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('last_verified')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Response tracking
            $table->decimal('average_response_time', 5, 2)->nullable(); // In minutes
            $table->integer('total_responses')->default(0);
            $table->timestamp('last_contacted')->nullable();
            
            // Administrative
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for emergency response speed
            $table->index(['contact_type', 'is_active', 'priority_order']);
            $table->index(['barangay', 'contact_type']);
            $table->index(['is_primary', 'contact_type']);
            $table->index(['accepts_emergency_calls', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};
