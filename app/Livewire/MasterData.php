<?php

namespace App\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;

class MasterData extends Component
{
    #[Url]
    public string $tab = 'jenis-diet';

    /** kunci tab => [label, [nama komponen Livewire yang ditampilkan]] */
    public function tabs(): array
    {
        $tabs = [
            'jenis-diet' => ['Jenis diet', ['master.jenis-diet']],
            'bentuk-makanan' => ['Bentuk makanan', ['master.bentuk-makanan']],
            'bangsal-kamar' => ['Bangsal & kamar', ['master.bangsal', 'master.kamar']],
            'waktu-makan' => ['Waktu makan', ['master.waktu-makan']],
            'alergen' => ['Alergen', ['master.alergen']],
        ];

        // Tab ini muncul otomatis setelah Orang 5 membuat kelas App\Livewire\Master\SiklusMenu
        if (class_exists(\App\Livewire\Master\SiklusMenu::class)) {
            $tabs['siklus-menu'] = ['Siklus menu', ['master.siklus-menu']];
        }

        return $tabs;
    }

    public function render()
    {
        $tabs = $this->tabs();
        if (! isset($tabs[$this->tab])) {
            $this->tab = 'jenis-diet';
        }

        return view('livewire.master-data', ['tabs' => $tabs]);
    }
}