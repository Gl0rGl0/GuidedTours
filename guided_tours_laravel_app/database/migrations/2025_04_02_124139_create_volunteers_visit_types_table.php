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
        Schema::create('volunteers_visit_types', function (Blueprint $table) {
            // Define foreign keys first
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('visit_type_id')->constrained('visit_types', 'visit_type_id')->cascadeOnDelete();

            // Define composite primary key
            $table->primary(['user_id', 'visit_type_id']);

            // No timestamps needed for this pivot table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers_visit_types');
    }
};
