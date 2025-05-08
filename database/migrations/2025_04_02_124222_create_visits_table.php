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
        Schema::create('visits', function (Blueprint $table) {
            $table->id('visit_id');
            $table->foreignId('visit_type_id')->constrained('visit_types', 'visit_type_id')->cascadeOnDelete();
            $table->date('visit_date');
            $table->foreignId('assigned_volunteer_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->enum('status', ['proposed', 'complete', 'confirmed', 'cancelled', 'effected'])->default('proposed');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('status_updated_at')->nullable();

            // Add index
            $table->index(['visit_date', 'status'], 'idx_visit_date_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
