<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update role names
        Role::where('name', 'configurator')->update(['name' => 'Admin']);
        Role::where('name', 'volunteer')->update(['name' => 'Guide']);
        Role::where('name', 'fruitore')->update(['name' => 'Customer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert role names
        Role::where('name', 'Admin')->update(['name' => 'configurator']);
        Role::where('name', 'Guide')->update(['name' => 'volunteer']);
        Role::where('name', 'Customer')->update(['name' => 'fruitore']);
    }
};
