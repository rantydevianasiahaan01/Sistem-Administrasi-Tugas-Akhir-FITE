<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['IF', 'S1IF'], ['SI', 'S1SI'], ['TE', 'S1TE']] as [$code, $name]) {
            DB::table('study_programs')->updateOrInsert(
                ['code' => $code],
                ['name' => $name, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}