<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(3)
             ->has(\App\Models\Project::factory()->count(1)
                 ->has(\App\Models\Todo::factory()->count(5)
                     ->has(\App\Models\Pomo::factory()->count(20))
                 ))
                ->create();
    }
}
