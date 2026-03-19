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
        Schema::create('visit_types', function (Blueprint $table) {
            $table->id('visit_type_id');
            $table->foreignId('place_id')->constrained('places', 'place_id')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_point');
            $table->date('period_start');
            $table->date('period_end');
            $table->time('start_time');
            $table->integer('duration_minutes');
            $table->boolean('requires_ticket')->default(false);
            $table->integer('min_participants');
            $table->integer('max_participants');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->timestamps(); // Handles created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_types');
    }
};
