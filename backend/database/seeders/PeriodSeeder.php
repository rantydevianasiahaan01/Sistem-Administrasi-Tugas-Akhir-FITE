<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DB::table('study_programs')->get() as $prodi) {
            DB::table('academic_periods')->updateOrInsert(
                ['study_program_id' => $prodi->id, 'name' => '2026/2027', 'semester' => 'odd'],
                [
                    'start_date' => '2026-08-17',
                    'end_date'   => '2027-01-15',
                    'status'     => 'active', // satu aktif per prodi (BR-35)
                    'created_at' => now(), 'updated_at' => now(),
                ]
            );
        }
    }
}