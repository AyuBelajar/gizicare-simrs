<?php

namespace App\Livewire\Dapur;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Rekap Porsi')]
class RekapPorsi extends Component
{
    // TODO: rekap porsi (GROUP BY bangsal × jenis diet × bentuk makanan)
    // dikerjakan setelah tabel order_diet milik Orang 4 masuk ke main.

    public function render()
    {
        return view('livewire.dapur.rekap-porsi');
    }
}