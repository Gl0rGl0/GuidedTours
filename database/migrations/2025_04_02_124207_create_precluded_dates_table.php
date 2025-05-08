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
        Schema::create('precluded_dates', function (Blueprint $table) {
            $table->date('precluded_date')->primary();
            $table->string('reason')->nullable();
            $table->foreignId('set_by_user_id')->nullable()->constrained('users', 'user_id')->nullOnDelete(); // Set null if user deleted
            $table->timestamp('set_at')->useCurrent(); // Only need set_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precluded_dates');
    }
};
