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
        // Add Completed at column
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop Completed at column
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
