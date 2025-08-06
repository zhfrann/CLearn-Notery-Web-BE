<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\FEB\BBA\BBACourseSeeder;
use Database\Seeders\FEB\BLM\BLMCourseSeeder;
use Database\Seeders\FEB\BMS\BMSCourseSeeder;
use Database\Seeders\FEB\FEBMajorSeeder;
use Database\Seeders\FEB\FEBSemesterSeeder;
use Database\Seeders\FIT\ABSCIS\ABSCISCourseSeeder;
use Database\Seeders\FIT\DAC\DACCourseSeeder;
use Database\Seeders\FIT\DCE\DCECourseSeeder;
use Database\Seeders\FIT\DHO\DHOCourseSeeder;
use Database\Seeders\FIT\DIF\DIFCourseSeeder;
use Database\Seeders\FIT\DIM\DIMCourseSeeder;
use Database\Seeders\FIT\DMM\DMMCourseSeeder;
use Database\Seeders\FIT\DSM\DSMCourseSeeder;
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
            ABSCISCourseSeeder::class,
            DACCourseSeeder::class,
            DCECourseSeeder::class,
            DIMCourseSeeder::class,
            DMMCourseSeeder::class,
            DSMCourseSeeder::class,
            DHOCourseSeeder::class,

            FEBMajorSeeder::class,
            FEBSemesterSeeder::class,
            BMSCourseSeeder::class,
            BBACourseSeeder::class,
            BLMCourseSeeder::class,
            BMSCourseSeeder::class,

            ReportTypeSeeder::class,
            NoteSeeder::class,
            InteractionSeeder::class,
            ReportSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
