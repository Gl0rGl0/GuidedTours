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
        Schema::create('volunteer_availability', function (Blueprint $table) {
            $table->id('availability_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->date('available_date');
            $table->char('month_year', 7)->index();
            $table->timestamp('declared_at')->useCurrent();

            // Define unique constraint
            $table->unique(['user_id', 'available_date'], 'unique_availability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_availability');
    }
};
