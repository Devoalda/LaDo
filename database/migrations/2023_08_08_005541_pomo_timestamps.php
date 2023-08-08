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
        Schema::table('pomos', function (Blueprint $table) {
            // Add created_at and updated_at columns to pomos table as unix timestamps
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pomos', function (Blueprint $table) {
            // Drop created_at and updated_at columns from pomos table
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
