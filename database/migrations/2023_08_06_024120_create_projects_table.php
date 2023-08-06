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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable()->default('');
            $table->string('description')->nullable()->default('');
            $table->integer('created_at')->unsigned()->nullable()->default(strtotime('now'));
            $table->integer('updated_at')->unsigned()->nullable()->default(strtotime('now'));
        });

        // Todo and Project BCNF middle table (2 foreign keys)
        Schema::create('project_todo', function (Blueprint $table) {
            $table->foreignUuid('project_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUuid('todo_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();


            $table->primary(['project_id', 'todo_id']);
        });

        Schema::create('project_user', function (Blueprint $table) {
            $table->foreignUuid('project_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade')
                ->cascadeOnUpdate();

            $table->primary(['project_id', 'user_id']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all tables
        Schema::dropIfExists('project_user');
        Schema::dropIfExists('project_todo');
        Schema::dropIfExists('projects');

    }
};
