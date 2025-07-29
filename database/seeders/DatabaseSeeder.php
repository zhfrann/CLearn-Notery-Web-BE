<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\FIT\DIF\DIFCourseSeeder;
use Database\Seeders\FIT\FITMajorSeeder;
use Database\Seeders\FIT\FITSemesterSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            TagSeeder::class,
            FacultySeeder::class,
            FITMajorSeeder::class,
            FITSemesterSeeder::class,
            DIFCourseSeeder::class,
            ReportTypeSeeder::class, // Add this
            NoteSeeder::class,
            InteractionSeeder::class,
        ]);
    }
}
