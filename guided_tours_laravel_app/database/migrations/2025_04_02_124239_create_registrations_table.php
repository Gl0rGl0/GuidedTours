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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id('registration_id');
            $table->foreignId('visit_id')->constrained('visits', 'visit_id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->integer('num_participants');
            $table->string('booking_code', 20)->unique();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('cancelled_at')->nullable();

            // Add indexes
            $table->index('visit_id', 'idx_registration_visit');
            $table->index('user_id', 'idx_registration_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
