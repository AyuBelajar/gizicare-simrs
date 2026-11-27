<?php

namespace App\Livewire\Master;

use App\Support\MasterCrud;

class Bangsal extends MasterCrud
{
    protected function modelClass(): string { return \App\Models\Bangsal::class; }

    protected function judul(): string { return 'Bangsal'; }

    protected function pageName(): string { return 'bangsalPage'; }

    protected function fields(): array
    {
        return [
            ['key' => 'nama_bangsal', 'label' => 'Nama bangsal', 'rules' => 'required|max:50', 'unique' => true, 'cari' => true],
            ['key' => 'kapasitas', 'label' => 'Kapasitas (tempat tidur)', 'type' => 'number', 'rules' => 'required|integer|min:1', 'default' => 10],
        ];
    }
}