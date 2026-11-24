<?php

namespace Database\Seeders;

use App\Models\Alergen;
use App\Models\Kamar;
use App\Models\Pasien;
use App\Models\PasienAlergi;
use App\Models\RawatInap;
use Illuminate\Database\Seeder;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        if (Pasien::exists()) {
            $this->command->warn('Data pasien sudah ada, seeder dilewati.');
            return;
        }

        $faker = fake('id_ID');
        $alergen = Alergen::pluck('id_alergen', 'nama_alergen');
        $kamarAnak = Kamar::whereHas('bangsal', fn ($q) => $q->where('nama_bangsal', 'Anggrek (anak)'))->pluck('id_kamar')->all();
        $kamarDewasa = Kamar::whereHas('bangsal', fn ($q) => $q->where('nama_bangsal', '!=', 'Anggrek (anak)'))->pluck('id_kamar')->all();

        $dxDewasa = ['DM tipe 2, ulkus kaki', 'Gagal jantung kongestif', 'Gastritis kronik', 'CKD stadium 4', 'Pneumonia',
            'Hipertensi urgensi', 'Stroke non-hemoragik', 'Post operasi laparotomi', 'Demam tifoid', 'Sirosis hati'];
        $dxAnak = ['Diare akut', 'Pneumonia', 'Demam berdarah dengue', 'Gizi kurang', 'Bronkiolitis'];

        // Pasien contoh (sama seperti mockup A6): Siti Aminah, Melati 203, alergi udang
        $siti = Pasien::factory()->create([
            'no_rm' => '00-12-3481', 'nama_pasien' => 'Siti Aminah', 'jenis_kelamin' => 'P',
            'tgl_lahir' => now()->subYears(54)->subMonths(2)->toDateString(),
        ]);
        RawatInap::create([
            'id_pasien' => $siti->id_pasien,
            'id_kamar' => Kamar::where('no_kamar', '203')->value('id_kamar'),
            'tgl_masuk' => now()->subDays(3)->setTime(14, 20),
            'diagnosis_medis' => 'DM tipe 2, ulkus kaki',
            'status' => 'aktif',
        ]);
        PasienAlergi::create(['id_pasien' => $siti->id_pasien, 'id_alergen' => $alergen['Udang'], 'reaksi' => 'Gatal dan bengkak pada bibir']);

        // 29 pasien lain: 7 anak, sisanya dewasa
        for ($i = 1; $i <= 29; $i++) {
            $anak = $i > 22;
            $pasien = ($anak ? Pasien::factory()->anak() : Pasien::factory())->create(['no_rm' => sprintf('00-12-%04d', 3500 + $i)]);

            RawatInap::create([
                'id_pasien' => $pasien->id_pasien,
                'id_kamar' => $faker->randomElement($anak ? $kamarAnak : $kamarDewasa),
                'tgl_masuk' => now()->subDays(rand(0, 9))->setTime(rand(6, 20), rand(0, 59)),
                'diagnosis_medis' => $faker->randomElement($anak ? $dxAnak : $dxDewasa),
                'status' => 'aktif',
            ]);

            if (rand(1, 100) <= 40) {
                foreach ($faker->randomElements($alergen->values()->all(), rand(1, 2)) as $idAlergen) {
                    PasienAlergi::create(['id_pasien' => $pasien->id_pasien, 'id_alergen' => $idAlergen, 'reaksi' => 'Ruam kulit']);
                }
            }
        }
    }
}