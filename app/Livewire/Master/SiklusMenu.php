<?php

namespace App\Livewire\Master;     // sesudah

use App\Models\JenisDiet;
use App\Models\SiklusMenu as Menu;
use App\Models\WaktuMakan;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('components.layouts.app')]
#[Title('Siklus Menu')]
class SiklusMenu extends Component
{
    use Toast, WithPagination;

    /** Panjang satu siklus menu (hari). */
    public const JUMLAH_HARI = 10;

    // Filter tabel
    public $filterHari = null;
    public $filterDiet = null;

    // Form di modal
    public bool $modal = false;
    public $idMenu = null;          // null = tambah, ada isinya = ubah
    public $hari_ke = null;
    public $id_waktu = null;
    public $id_diet = null;
    public $deskripsi_menu = '';

    public function updatedFilterHari(): void { $this->resetPage(); }
    public function updatedFilterDiet(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'hari_ke'        => ['required', 'integer', 'between:1,' . self::JUMLAH_HARI],
            'id_waktu'       => ['required', 'exists:waktu_makan,id_waktu'],
            'id_diet'        => [
                'required',
                'exists:jenis_diet,id_diet',
                // Tidak boleh ada dua menu untuk hari + waktu makan + diet yang sama
                Rule::unique('siklus_menu', 'id_diet')
                    ->where(fn ($q) => $q->where('hari_ke', $this->hari_ke)->where('id_waktu', $this->id_waktu))
                    ->ignore($this->idMenu, 'id_menu'),
            ],
            'deskripsi_menu' => ['required', 'string', 'max:1000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'id_diet.unique'          => 'Menu untuk hari, waktu makan, dan jenis diet ini sudah ada.',
            'deskripsi_menu.required' => 'Deskripsi menu wajib diisi.',
        ];
    }

    public function tambah(): void
    {
        $this->kosongkanForm();
        $this->modal = true;
    }

    public function ubah(int $id): void
    {
        $menu = Menu::findOrFail($id);

        $this->kosongkanForm();
        $this->idMenu         = $menu->id_menu;
        $this->hari_ke        = $menu->hari_ke;
        $this->id_waktu       = $menu->id_waktu;
        $this->id_diet        = $menu->id_diet;
        $this->deskripsi_menu = $menu->deskripsi_menu;
        $this->modal = true;
    }

    public function simpan(): void
    {
        $data = $this->validate();

        if ($this->idMenu) {
            Menu::findOrFail($this->idMenu)->update($data);
            $this->success('Menu berhasil diubah.');
        } else {
            Menu::create($data);
            $this->success('Menu berhasil ditambahkan.');
        }

        $this->modal = false;
        $this->kosongkanForm();
    }

    public function hapus(int $id): void
    {
        Menu::findOrFail($id)->delete();
        $this->success('Menu berhasil dihapus.');
    }

    private function kosongkanForm(): void
    {
        $this->reset(['idMenu', 'hari_ke', 'id_waktu', 'id_diet', 'deskripsi_menu']);
        $this->resetValidation();
    }

    public function render()
    {
        $menu = Menu::with(['waktuMakan', 'jenisDiet'])
            ->when($this->filterHari, fn ($q) => $q->where('hari_ke', $this->filterHari))
            ->when($this->filterDiet, fn ($q) => $q->where('id_diet', $this->filterDiet))
            ->orderBy('hari_ke')->orderBy('id_waktu')->orderBy('id_diet')
            ->paginate(15);

        return view('livewire.master.siklus-menu', [
            'menu'      => $menu,
            'headers'   => [
                ['key' => 'hari_ke',               'label' => 'Hari ke-', 'class' => 'w-20'],
                ['key' => 'waktuMakan.nama_waktu', 'label' => 'Waktu makan'],
                ['key' => 'jenisDiet.nama_diet',   'label' => 'Jenis diet'],
                ['key' => 'deskripsi_menu',        'label' => 'Deskripsi menu'],
            ],
            'opsiHari'  => collect(range(1, self::JUMLAH_HARI))
                ->map(fn ($h) => ['id' => $h, 'name' => "Hari ke-$h"]),
            'opsiWaktu' => WaktuMakan::orderBy('jam')->get()
                ->map(fn ($w) => ['id' => $w->id_waktu, 'name' => "{$w->nama_waktu} ({$w->jam})"]),
            'opsiDiet'  => JenisDiet::orderBy('kode')->get()
                ->map(fn ($d) => ['id' => $d->id_diet, 'name' => "{$d->kode} — {$d->nama_diet}"]),
        ]);
    }
}