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
            // Create due_start, due_end, completed_at, created_at, updated_at as unsigned integer
            $table->integer('due_start')->unsigned()->nullable();
            $table->integer('due_end')->unsigned()->nullable()->after('due_start');
            $table->integer('completed_at')->unsigned()->nullable();
            $table->integer('created_at')->unsigned()->nullable()->default(strtotime('now'));
            $table->integer('updated_at')->unsigned()->nullable()->default(strtotime('now'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            // Drop due_start, due_end, completed_at, created_at, updated_at
            $table->dropColumn('due_start');
            $table->dropColumn('due_end');
            $table->dropColumn('completed_at');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
