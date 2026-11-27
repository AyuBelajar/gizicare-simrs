<?php

namespace App\Livewire\Master;

use App\Models\Bangsal;
use App\Support\MasterCrud;

class Kamar extends MasterCrud
{
    protected function modelClass(): string { return \App\Models\Kamar::class; }

    protected function judul(): string { return 'Kamar'; }

    protected function pageName(): string { return 'kamarPage'; }

    protected function relasi(): array { return ['bangsal']; }

    protected function fields(): array
    {
        return [
            ['key' => 'id_bangsal', 'label' => 'Bangsal', 'type' => 'select', 'rules' => 'required|integer', 'col' => 'bangsal.nama_bangsal'],
            ['key' => 'no_kamar', 'label' => 'No. kamar', 'rules' => 'required|max:20', 'unique' => true, 'uniqueWith' => 'id_bangsal', 'cari' => true],
            ['key' => 'kelas', 'label' => 'Kelas', 'type' => 'select', 'rules' => 'required'],
        ];
    }

    protected function opsi(string $key): array
    {
        if ($key === 'id_bangsal') {
            return Bangsal::orderBy('nama_bangsal')->get()
                ->map(fn ($b) => ['id' => $b->id_bangsal, 'name' => $b->nama_bangsal])->all();
        }

        return [
            ['id' => 'I', 'name' => 'Kelas I'],
            ['id' => 'II', 'name' => 'Kelas II'],
            ['id' => 'III', 'name' => 'Kelas III'],
            ['id' => 'VIP', 'name' => 'VIP'],
        ];
    }
}