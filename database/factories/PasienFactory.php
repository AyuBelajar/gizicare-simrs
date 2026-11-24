<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PasienFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');
        $jk = $faker->randomElement(['L', 'P']);

        return [
            'no_rm' => $faker->unique()->numerify('00-##-####'),
            'nama_pasien' => ($jk === 'L' ? $faker->firstNameMale() : $faker->firstNameFemale()).' '.$faker->lastName(),
            'tgl_lahir' => now()->subYears(rand(19, 78))->subDays(rand(0, 300))->toDateString(),
            'jenis_kelamin' => $jk,
            'kategori' => 'dewasa',
            'no_hp' => $faker->numerify('08##########'),
        ];
    }

    public function anak(): static
    {
        return $this->state(fn () => [
            'tgl_lahir' => now()->subYears(rand(1, 12))->subDays(rand(0, 300))->toDateString(),
            'kategori' => 'anak',
        ]);
    }
}