<?php

namespace Database\Seeders;

use App\Models\Alergen;
use App\Models\Bangsal;
use App\Models\BentukMakanan;
use App\Models\JenisDiet;
use App\Models\Kamar;
use App\Models\WaktuMakan;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $bangsal = [
            ['Melati', 12, 'II', [201, 202, 203, 204, 205, 206]],
            ['Mawar', 12, 'III', [301, 302, 303, 304, 305, 306]],
            ['Anggrek (anak)', 8, 'II', [101, 102, 103, 104]],
        ];
        foreach ($bangsal as [$nama, $kapasitas, $kelas, $kamarList]) {
            $b = Bangsal::firstOrCreate(['nama_bangsal' => $nama], ['kapasitas' => $kapasitas]);
            foreach ($kamarList as $no) {
                Kamar::firstOrCreate(['id_bangsal' => $b->id_bangsal, 'no_kamar' => (string) $no], ['kelas' => $kelas]);
            }
        }

        $diet = [
            ['DB', 'Diet Biasa', 'Makanan standar rumah sakit'],
            ['DM17', 'Diet DM 1700', 'Diabetes, 1.700 kkal'],
            ['DM19', 'Diet DM 1900', 'Diabetes, 1.900 kkal'],
            ['RG2', 'Rendah Garam II', 'Natrium 600–800 mg/hari'],
            ['RP40', 'Rendah Protein 40 g', 'Gangguan ginjal kronik'],
            ['DJ3', 'Diet Jantung III', 'Transisi ke makanan lunak'],
            ['LB2', 'Diet Lambung II', 'Tidak merangsang lambung'],
            ['TKTP', 'TKTP', 'Tinggi kalori tinggi protein'],
        ];
        foreach ($diet as [$kode, $nama, $ket]) {
            JenisDiet::firstOrCreate(['kode' => $kode], ['nama_diet' => $nama, 'keterangan' => $ket]);
        }

        foreach (['Biasa', 'Lunak', 'Saring', 'Cair', 'Sonde'] as $bentuk) {
            BentukMakanan::firstOrCreate(['nama_bentuk' => $bentuk]);
        }

        foreach ([['Pagi', '07:00'], ['Siang', '12:00'], ['Sore', '17:30']] as [$nama, $jam]) {
            WaktuMakan::firstOrCreate(['nama_waktu' => $nama], ['jam' => $jam]);
        }

        foreach (['Udang', 'Telur', 'Susu sapi', 'Kacang tanah', 'Ikan', 'Gluten/gandum', 'Kedelai', 'Kerang'] as $a) {
            Alergen::firstOrCreate(['nama_alergen' => $a]);
        }
    }
}