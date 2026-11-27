<?php

namespace App\Livewire\Master;

use App\Support\MasterCrud;

class Alergen extends MasterCrud
{
    protected function modelClass(): string { return \App\Models\Alergen::class; }

    protected function judul(): string { return 'Alergen'; }

    protected function fields(): array
    {
        return [
            ['key' => 'nama_alergen', 'label' => 'Nama alergen', 'rules' => 'required|max:50', 'unique' => true, 'cari' => true],
        ];
    }
}