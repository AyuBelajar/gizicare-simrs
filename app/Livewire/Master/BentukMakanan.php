<?php

namespace App\Livewire\Master;

use App\Support\MasterCrud;

class BentukMakanan extends MasterCrud
{
    protected function modelClass(): string { return \App\Models\BentukMakanan::class; }

    protected function judul(): string { return 'Bentuk makanan'; }

    protected function fields(): array
    {
        return [
            ['key' => 'nama_bentuk', 'label' => 'Nama bentuk', 'rules' => 'required|max:50', 'unique' => true, 'cari' => true],
        ];
    }
}