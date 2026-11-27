<?php

namespace App\Livewire\Master;

use App\Support\MasterCrud;

class JenisDiet extends MasterCrud
{
    protected function modelClass(): string { return \App\Models\JenisDiet::class; }

    protected function judul(): string { return 'Jenis diet'; }

    protected function fields(): array
    {
        return [
            ['key' => 'kode', 'label' => 'Kode', 'rules' => 'required|max:10', 'unique' => true, 'cari' => true],
            ['key' => 'nama_diet', 'label' => 'Nama diet', 'rules' => 'required|max:100', 'cari' => true],
            ['key' => 'keterangan', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => 'nullable|max:255'],
        ];
    }
}