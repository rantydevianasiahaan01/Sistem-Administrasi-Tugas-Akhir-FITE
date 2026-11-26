<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class InitialAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Kata sandi awal dari .env, bukan hardcode (NFR-07). Wajib diganti saat login pertama.
        $password = env('SEED_INITIAL_PASSWORD') ?: throw new \RuntimeException('SEED_INITIAL_PASSWORD belum diisi di .env');

        // BAAF: level fakultas, team_id = 0
        $baaf = $this->makeUser('baaf01', 'Staf BAAF FITE', null, $password);
        $this->assign($baaf, 'BAAF', 0);

        // Koordinator TA: satu per prodi, peran Dosen + Koordinator TA
        foreach (DB::table('study_programs')->get() as $prodi) {
            $koor = $this->makeUser('koor' . strtolower($prodi->code), "Koordinator TA {$prodi->name}", $prodi->id, $password);
            $this->assign($koor, 'Dosen', $prodi->id);
            $this->assign($koor, 'Koordinator TA', $prodi->id);
        }
    }

    private function makeUser(string $username, string $name, ?int $prodiId, string $password): User
    {
        return User::updateOrCreate(['username' => $username], [
            'name' => $name,
            'study_program_id' => $prodiId,
            'password' => Hash::make($password), // bcrypt (BR-13, NFR-25)
            'is_active' => true,
            'must_change_password' => true,
        ]);
    }

    private function assign(User $user, string $role, int $teamId): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);
        $user->unsetRelation('roles')->unsetRelation('permissions');
        $user->assignRole($role);
    }
}