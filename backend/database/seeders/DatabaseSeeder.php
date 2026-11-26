<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StudyProgramSeeder::class,
            RoleSeeder::class,
            PeriodSeeder::class,
            ProgramConfigSeeder::class,   // profil + tahap + dokumen wajib + syarat + bobot + skala
            InitialAccountSeeder::class,  // BAAF + Koordinator per prodi (FR-M00-03 AC7)
        ]);

        // BR-09: akun contoh hanya untuk demo, tidak di produksi
        if (! app()->isProduction()) {
            $this->call(DemoAccountSeeder::class);
        }
    }
}