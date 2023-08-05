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
        Schema::table('todos', function (Blueprint $table) {
            $table->string('due_start')->default('')->change();
            $table->string('due_end')->default('')->change();
            $table->string('due_start')->nullable()->change();
            $table->string('due_end')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->string('due_start')->default('')->change();
            $table->string('due_end')->default('')->change();
            $table->string('due_start')->nullable()->change();
            $table->string('due_end')->nullable()->change();
        });
    }
};
