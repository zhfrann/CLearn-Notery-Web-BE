<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil hanya user dengan role student
        $reportedUsers = User::where('role', 'student')->inRandomOrder()->take(5)->get();
        $reportTypes = ReportType::active()->get();
        $allReporters = User::where('role', 'student')->get();

        foreach ($reportedUsers as $reportedUser) {
            foreach ($reportTypes as $type) {
                // Ambil reporter yang berbeda dari reportedUser
                $reporterCandidates = $allReporters->where('user_id', '!=', $reportedUser->user_id)->shuffle();
                // Misal, 3-7 reporter untuk setiap kombinasi
                $jumlahReporter = rand(3, 7);
                $selectedReporters = $reporterCandidates->take($jumlahReporter);

                foreach ($selectedReporters as $reporter) {
                    // Kombinasi unik, tidak akan duplikat
                    Report::updateOrCreate([
                        'reporter_id' => $reporter->user_id,
                        'reported_user_id' => $reportedUser->user_id,
                        'report_type_value' => $type->value,
                    ], [
                        'description' => 'Contoh laporan ' . $type->label . ' terhadap ' . $reportedUser->username,
                        'evidence_files' => null,
                        'status' => 'pending',
                        'admin_notes' => null,
                        'handled_by_admin_id' => null,
                        'resolved_at' => null,
                    ]);
                }
            }
        }
    }
}
