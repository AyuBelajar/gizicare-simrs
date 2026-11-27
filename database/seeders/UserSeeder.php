<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun demo sesuai mockup. Semua kata sandi: password
     * Memakai updateOrCreate, jadi aman dijalankan berulang kali (tidak dobel).
     */
    public function run(): void
    {
        $akun = [
            // [nama, email, role, aktif?]
            ['Dewi Lestari, S.Gz',   'dewi@rs.local',    'ahli_gizi',        true],
            ['Putri Maharani, S.Gz', 'putri@rs.local',   'ahli_gizi',        true],
            ['Rudi Hartono',         'rudi@rs.local',    'petugas_dapur',    true],
            ['Siti Nurhaliza',       'siti.n@rs.local',  'petugas_dapur',    true],
            ['Bambang Irawan',       'bambang@rs.local', 'petugas_dapur',    false],
            ['Andi Nugroho',         'andi@rs.local',    'admin',            true],
            ['dr. Sri Rahayu, M.Gz', 'sri@rs.local',     'kepala_instalasi', true],
        ];

        foreach ($akun as [$nama, $email, $role, $aktif]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'          => $nama,
                    'role'          => $role,
                    'is_active'     => $aktif,
                    'password_hash' => 'password',   // ter-hash otomatis
                ]
            );
        }
    }
}