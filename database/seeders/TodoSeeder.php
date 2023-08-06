<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('todos')->insert([
            'id' => 'c2d4e6f8-g7h8-i9j0-k1l2m3n4o5p6',
            'title' => 'First Todo',
            'description' => 'This is the first todo',
            'due_start' => strtotime('now'),
            'due_end' => strtotime('+1 week'),
        ]);

        DB::table('project_todo')->insert([
            'project_id' => "a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
            'todo_id' => "c2d4e6f8-g7h8-i9j0-k1l2m3n4o5p6",
        ]);
    }
}
