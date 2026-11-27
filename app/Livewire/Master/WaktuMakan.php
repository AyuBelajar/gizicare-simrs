<?php

namespace App\Livewire\Master;

use App\Support\MasterCrud;

class WaktuMakan extends MasterCrud
{
    protected function modelClass(): string { return \App\Models\WaktuMakan::class; }

    protected function judul(): string { return 'Waktu makan'; }

    protected function fields(): array
    {
        return [
            ['key' => 'nama_waktu', 'label' => 'Nama waktu makan', 'rules' => 'required|max:30', 'unique' => true, 'cari' => true],
            ['key' => 'jam', 'label' => 'Jam', 'type' => 'time', 'rules' => 'required|date_format:H:i'],
        ];
    }

    protected function urutan(): string { return 'jam'; }
}