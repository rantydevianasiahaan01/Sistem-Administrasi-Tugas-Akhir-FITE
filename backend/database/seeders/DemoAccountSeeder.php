<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new \RuntimeException('Seeder demo tidak boleh dijalankan di produksi.'); // US-M00-23 AC3
        }

        $password = env('SEED_DEMO_PASSWORD', 'Demo12345'); // memenuhi kebijakan: >= 8, huruf + angka
        $prodiIF  = DB::table('study_programs')->where('code', 'IF')->value('id');

        $accounts = [
            // [username, nama, peran]
            ['dosen.ranti', 'Bu Ranti (Pembimbing)', 'Dosen'],
            ['dosen.budi',  'Pak Budi (Penguji)',    'Dosen'],
            ['13IF0001',    'Rachel (Mahasiswa)',    'Mahasiswa'],
            ['13IF0002',    'Eliza (Mahasiswa)',     'Mahasiswa'],
            ['13IF0003',    'Miftahul (Mahasiswa)',  'Mahasiswa'],
        ];

        foreach ($accounts as [$username, $name, $role]) {
            $user = User::updateOrCreate(['username' => $username], [
                'name' => $name,
                'study_program_id' => $prodiIF,
                'class' => $role === 'Mahasiswa' ? '13IF1' : null,
                'password' => Hash::make($password),
                'is_active' => true,
                'must_change_password' => false,
            ]);
            app(PermissionRegistrar::class)->setPermissionsTeamId($prodiIF);
            $user->unsetRelation('roles')->unsetRelation('permissions');
            $user->assignRole($role);
        }
    }
}