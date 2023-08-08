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
        Schema::create('pomos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('todo_id')->constrained('todos')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->integer('pomo_start')->nullable();
            $table->integer('pomo_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pomos');
    }
};
