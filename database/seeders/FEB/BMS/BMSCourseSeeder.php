<?php

namespace Database\Seeders\FEB\BMS;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class BMSCourseSeeder extends Seeder
{
    private $BMS_semester1 = [
        'Business Statistics',
        'Interpersonal and Business Communication',
        'Economics',
        'Pancasila',
        'Introduction to Management',
        'Introduction to the Telecommunications and Information Business',
        'HEI'
    ];
    private $BMS_semester2 = [
        'English',
        'Mathematics for Economics and Business',
        'Introduction of Accounting',
        'Religion and Ethics',
        'ICT / Technology Literacy',
        'Introduction to Business',
        'Indonesian civics'
    ];
    private $BMS_semester3 = [
        'Managerial Economics',
        'Data Management',
        'Quantitative Methods for Business',
        'Financial Management',
        'Organizational Behavior',
        'Management Information System'
    ];
    private $BMS_semester4 = [
        'Creative thinking in Business',
        'Entrepreneurship',
        'Marketing Management',
        'Strategic Human Resource Management',
        'Operations and Quality Management',
        'Econometrics'
    ];
    private $BMS_semester5 = [
        'Design Thinking',
        'Big Data and Data Analytics',
        'Consumer Behavior',
        'Supply Chain Management in ICT Services',
        'Indonesian language',
        'International business and trade (elct 1)'
    ];
    private $BMS_semester6 = [
        'Entrepreneurship Project',
        'Research Methodology & Academic Writing',
        'System Analysis and Design',
        'Strategy Management',
        'Internship*',
        'Elective course 2 (LSS Sertification)'
    ];
    private $BMS_semester7 = [
        'Business Ethics and Corporate Governance',
        'Research seminar',
        'Business law and regulation',
        'Digital Economy',
        'Project Management',
        'Ai for Business (elct 3)'
    ];
    private $BMS_semester8 = [
        'Thesis',
        'Colloqium *',
        'Eccomerce and Information Business',
        'Certification (TUK)'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'BMS')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 8; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->BMS_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BMS_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BMS_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BMS_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BMS_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BMS_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BMS_semester7 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[7],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BMS_semester8 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[8],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
