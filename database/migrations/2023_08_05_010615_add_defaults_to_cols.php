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
            $table->string('title')->default('')->change();
            $table->text('description')->default('')->change();
            $table->dateTime('due_start')->default(now())->change();
            $table->dateTime('due_end')->default(now())->change();
            $table->uuid('user_id')->default('')->change();
            $table->dateTime('completed_at')->default(now())->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->string('title')->default(null)->change();
            $table->text('description')->default(null)->change();
            $table->dateTime('due_start')->default(null)->change();
            $table->dateTime('due_end')->default(null)->change();
            $table->uuid('user_id')->default(null)->change();
            $table->dateTime('completed_at')->default(null)->change();
        });
    }
};
