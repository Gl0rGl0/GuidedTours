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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Use 'user_id' as the primary key name
            $table->string('username', 50)->unique();
            $table->string('password'); // Laravel handles hashing; original name was password_hash
            $table->enum('role', ['configurator', 'volunteer', 'fruitore'])->nullable(); // Make role nullable
            $table->boolean('first_login')->default(true);
            $table->timestamps(); // Handles created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
