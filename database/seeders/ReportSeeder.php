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
        $allStudents = User::where('role', 'student')->get();
        $reportTypes = ReportType::active()->get();
        $allReporters = $allStudents;

        // Pilih random 30-60% user sebagai reported user
        $totalReported = (int) ceil($allStudents->count() * rand(30, 60) / 100);
        $reportedUsers = $allStudents->shuffle()->take($totalReported);

        foreach ($reportedUsers as $reportedUser) {
            // Setiap reported user hanya akan mendapat 1-3 jenis report
            $userReportTypes = $reportTypes->shuffle()->take(rand(1, 3));
            foreach ($userReportTypes as $type) {
                // Ambil reporter yang berbeda dari reportedUser
                $reporterCandidates = $allReporters->where('user_id', '!=', $reportedUser->user_id)->shuffle();
                // 2-5 reporter untuk setiap kombinasi
                $jumlahReporter = rand(2, 5);
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
