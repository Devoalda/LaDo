<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 project with uuid
        DB::table('projects')->insert([
            'name' => 'First Project',
            'description' => 'This is the first project',
            'id' => 'a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6',
        ]);

        // Create corresponding project_user
        DB::table('project_user')->insert([
            'project_id' => "a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6",
            'user_id' => "9903bb7e-9743-4c09-bacc-4fc5d1aa7a66",
        ]);
    }
}
