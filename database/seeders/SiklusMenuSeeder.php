<?php

namespace Database\Seeders;

use App\Models\JenisDiet;
use App\Models\SiklusMenu;
use App\Models\WaktuMakan;
use Illuminate\Database\Seeder;

class SiklusMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Sengaja memakai bahan yang sering jadi alergen (telur, udang, ikan, kacang, susu)
        // supaya fitur cek bentrok alergi bisa diuji.
        $menu = [
            'Pagi'  => ['Bubur ayam, telur rebus, pisang', 'Nasi tim, omelet sayur, susu',
                        'Roti tawar, selai kacang, susu', 'Nasi, tahu bacem, sup wortel', 'Bubur kacang hijau, roti'],
            'Siang' => ['Nasi, ikan kakap kukus, sayur bening bayam, jeruk', 'Nasi, ayam bakar, tumis buncis, pepaya',
                        'Nasi, udang goreng tepung, capcay, semangka', 'Nasi, semur daging, sayur asem, melon',
                        'Nasi, pepes ikan, oseng labu siam, pisang'],
            'Sore'  => ['Nasi, telur dadar, sup sayur, pepaya', 'Nasi, ayam suwir, sayur sop, jeruk',
                        'Nasi, tempe goreng, gado-gado (saus kacang), melon', 'Nasi, ikan bandeng presto, tumis kangkung, pisang',
                        'Nasi, rolade tahu, sup jagung, semangka'],
        ];

        foreach (WaktuMakan::all() as $waktu) {
            $daftar = $menu[$waktu->nama_waktu] ?? null;
            if (! $daftar) {
                continue;
            }

            foreach (JenisDiet::all() as $diet) {
                for ($hari = 1; $hari <= 10; $hari++) {
                    SiklusMenu::firstOrCreate(
                        ['hari_ke' => $hari, 'id_waktu' => $waktu->id_waktu, 'id_diet' => $diet->id_diet],
                        ['deskripsi_menu' => $daftar[($hari - 1) % count($daftar)]],
                    );
                }
            }
        }
    }
}