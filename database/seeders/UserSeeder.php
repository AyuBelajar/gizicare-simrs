<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [
            ['Dewi Lestari, S.Gz',   'dewi@rs.local',   'ahli_gizi'],
            ['Putri Maharani, S.Gz', 'putri@rs.local',  'ahli_gizi'],
            ['Rudi Hartono',         'rudi@rs.local',   'petugas_dapur'],
            ['Siti Nurhaliza',       'siti.n@rs.local', 'petugas_dapur'],
            ['Andi Nugroho',         'andi@rs.local',   'admin'],
            ['dr. Sri Rahayu, M.Gz', 'sri@rs.local',    'kepala_instalasi'],
        ];

        foreach ($akun as [$nama, $email, $role]) {
            \App\Models\User::create([
                'name' => $nama, 'email' => $email, 'role' => $role,
                'password_hash' => 'password',   // ter-hash otomatis
            ]);
        }
    }
}
