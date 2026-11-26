<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramConfigSeeder extends Seeder
{
    public function run(): void
    {
        $ts = ['created_at' => now(), 'updated_at' => now()];

        // Nilai dari Tabel 2.2 SRS. Tanda (ASUMSI) = perlu konfirmasi.
        $profiles = [
            'IF' => [
                'params' => [
                    'max_group_members' => 3, // (ASUMSI) IF mengikuti Surat Edaran Kaprodi
                    'co_supervisor_count' => 1,
                    'min_guidance' => ['count' => 8, 'per' => 'semester'],
                    'lecturer_quota' => null,
                    'timeline_weeks' => ['first' => 1, 'last' => 22],
                    'extra_window_label' => 'Tambahan',
                ],
                'stages' => [['Seminar Proposal', 'ta_1'], ['Seminar TA-I', 'ta_1'], ['Seminar Hasil', 'ta_2'], ['Sidang TA-II', 'ta_2']],
                'similarity' => 20,
                // [nama, bobot, peran penanggung jawab, [peran => porsi]]
                'components' => [
                    ['Administrasi', 15, 'coordinator', ['coordinator' => 100]],
                    ['Seminar/Sidang', 40, 'examiner_1', ['examiner_1' => 50, 'examiner_2' => 50]],
                    ['Pembimbingan', 45, 'supervisor', ['supervisor' => 100]],
                ],
                'requirements' => [
                    ['Jumlah SKS lulus (min. nilai C)', 'number', '73'],
                    ['IPK', 'number', '2.00'],
                    ['Nilai Perilaku', 'letter', 'C'],
                    ['Skor TOEFL', 'number', '475'],
                ],
                'extra_max' => 'AB',
                'classes' => ['Lulus', 'Lulus Bersyarat', 'Tidak Lulus'],
            ],
            'SI' => [
                'params' => [
                    'max_group_members' => 3,
                    'co_supervisor_count' => 1,
                    'min_guidance' => ['count' => 8, 'per' => 'semester'],
                    'lecturer_quota' => null,
                    'timeline_weeks' => ['first' => 1, 'last' => 22],
                    'extra_window_label' => 'Tambahan',
                ],
                'stages' => [['Seminar Proposal', 'ta_1'], ['Seminar TA-I', 'ta_1'], ['Seminar Hasil', 'ta_2'], ['Sidang TA-II', 'ta_2']],
                'similarity' => 20,
                'components' => [
                    ['Administrasi', 10, 'coordinator', ['coordinator' => 100]],
                    ['Seminar TA-I', 25, 'examiner_1', ['examiner_1' => 50, 'examiner_2' => 50]],
                    ['Sidang TA-II', 25, 'examiner_1', ['examiner_1' => 50, 'examiner_2' => 50]],
                    ['Pembimbingan', 40, 'supervisor', ['supervisor' => 100]],
                ],
                'requirements' => [
                    ['Jumlah SKS diambil', 'number', '78'],
                    ['Jumlah SKS lulus', 'number', '70'],
                    ['IPK', 'number', '2.00'],
                    ['Nilai Perilaku', 'letter', 'C'],
                    ['Skor TOEFL', 'number', '470'],
                ],
                'extra_max' => 'AB',
                'classes' => ['Minor', 'Mayor'],
            ],
            'TE' => [
                'params' => [
                    'max_group_members' => 3,
                    'co_supervisor_count' => 1,
                    'min_guidance' => ['count' => 10, 'per' => 'phase'],
                    'lecturer_quota' => 10,
                    'timeline_weeks' => ['first' => 0, 'last' => 22],
                    'extra_window_label' => 'Toleransi',
                ],
                'stages' => [['Seminar Proposal', 'ta_1'], ['Sidang TA I', 'ta_1'], ['Pra-Sidang', 'ta_2'], ['Sidang TA II', 'ta_2']],
                'similarity' => 20,
                'components' => [
                    // (ASUMSI) porsi 35/35/30 = penguji 1 / penguji 2 / pembimbing
                    ['Sidang', 50, 'examiner_1', ['examiner_1' => 35, 'examiner_2' => 35, 'supervisor' => 30]],
                    ['Pembimbingan', 40, 'supervisor', ['supervisor' => 100]],
                    ['Administrasi', 10, 'coordinator', ['coordinator' => 100]],
                ],
                'requirements' => [
                    ['Jumlah SKS', 'number', '110'],
                    ['Persentase nilai D (maks.)', 'number', '10'],
                    ['Metodologi Penelitian lulus', 'file_only', null],
                    ['Kerja Praktik selesai', 'file_only', null],
                ],
                'extra_max' => null,
                'classes' => ['Lulus', 'Lulus Bersyarat', 'Sidang Ulang', 'Tidak Lulus'],
            ],
        ];

        // Skala default (ASUMSI rentang) – dibaca M10 sejak awal Sprint 5
        $scale = [['A', 4.00, 85, 100], ['AB', 3.50, 80, 84.99], ['B', 3.00, 70, 79.99], ['BC', 2.50, 65, 69.99],
                  ['C', 2.00, 55, 64.99], ['D', 1.00, 40, 54.99], ['E', 0.00, 0, 39.99]];

        foreach ($profiles as $code => $p) {
            $prodiId = DB::table('study_programs')->where('code', $code)->value('id');
            $periodId = DB::table('academic_periods')
                ->where('study_program_id', $prodiId)->where('name', '2026/2027')->value('id');
            $scope = ['study_program_id' => $prodiId, 'academic_period_id' => $periodId];

            // 1. Profil konfigurasi (version = 1)
            DB::table('program_configurations')->updateOrInsert($scope, [
                'parameters' => json_encode($p['params'] + [
                    'inactive_weeks_n' => 2,
                    'la_frequency' => 'weekly',
                    'la_deadline' => ['day' => 'friday', 'time' => '23:59'],
                ]),
                'version' => 1,
            ] + $ts);

            // 2. Tahapan + rantai approval (Pembimbing -> Koordinator, tanpa Kaprodi)
            foreach ($p['stages'] as $i => [$name, $phase]) {
                DB::table('stages')->updateOrInsert($scope + ['name' => $name], [
                    'order' => $i + 1, 'phase' => $phase, 'mode' => 'approval_chain',
                ] + $ts);
                $stageId = DB::table('stages')->where($scope)->where('name', $name)->value('id');

                foreach ([[1, 'supervisor'], [2, 'coordinator']] as [$order, $role]) {
                    DB::table('stage_approval_steps')->updateOrInsert(
                        ['stage_id' => $stageId, 'step_order' => $order],
                        ['approver_role' => $role] + $ts
                    );
                }

                // 3. Dokumen wajib contoh: Seminar Proposal (FR-M01-04, FR-M05-08)
                if ($i === 0) {
                    $docs = [
                        ['Proposal TA', 'pdf', 10, 'per_group', $p['similarity'], 10],
                        ['Rangkuman Paper', 'pdf', 10, 'per_group', null, null],
                        ['Sertifikat TOEFL', 'pdf,jpg,png', 5, 'per_student', null, null],
                    ];
                    foreach ($docs as [$dn, $fmt, $mb, $sc, $turn, $gen]) {
                        DB::table('required_documents')->updateOrInsert(
                            ['stage_id' => $stageId, 'name' => $dn],
                            ['allowed_formats' => $fmt, 'max_size_mb' => $mb, 'scope' => $sc,
                             'is_required' => true, 'turnitin_limit' => $turn, 'genai_limit' => $gen] + $ts
                        );
                    }
                }
            }

            // 4. Syarat kelayakan (FR-M01-05)
            foreach ($p['requirements'] as [$name, $type, $min]) {
                DB::table('eligibility_requirements')->updateOrInsert($scope + ['name' => $name], [
                    'value_type' => $type, 'min_value' => $min, 'requires_proof' => true, 'is_active' => true,
                ] + $ts);
            }

            // 5. Komponen & bobot (total 100%, BR-37).
            // (ASUMSI) semua ditaruh di fase ta_2 dulu; pecah per fase bila pedoman menuntut.
            foreach ($p['components'] as [$name, $weight, $resp, $shares]) {
                DB::table('assessment_components')->updateOrInsert($scope + ['phase' => 'ta_2', 'name' => $name], [
                    'weight' => $weight, 'responsible_role' => $resp,
                ] + $ts);
                $compId = DB::table('assessment_components')
                    ->where($scope)->where('phase', 'ta_2')->where('name', $name)->value('id');

                foreach ($shares as $role => $share) {
                    DB::table('component_assessor_shares')->updateOrInsert(
                        ['assessment_component_id' => $compId, 'assessor_role' => $role],
                        ['share' => $share]
                    );
                }
            }

            // 6. Skala nilai (is_min_pass = BC; is_max_extra_window = AB bila ada)
            foreach ($scale as [$letter, $gp, $min, $max]) {
                DB::table('grade_scales')->updateOrInsert($scope + ['letter' => $letter], [
                    'grade_point' => $gp, 'min_score' => $min, 'max_score' => $max,
                    'is_min_pass' => $letter === 'BC',
                    'is_max_extra_window' => $letter === $p['extra_max'],
                ] + $ts);
            }

            // 7. Klasifikasi hasil sidang
            foreach ($p['classes'] as $i => $name) {
                DB::table('result_classifications')->updateOrInsert($scope + ['name' => $name], [
                    'order' => $i + 1,
                ] + $ts);
            }
        }
    }
}