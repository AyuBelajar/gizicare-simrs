<?php

namespace App\Livewire;

use App\Models\Alergen;
use App\Models\PasienAlergi;
use App\Models\RawatInap;
use App\Support\RingkasanGizi;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Mary\Traits\Toast;

class DetailPasien extends Component
{
    use Toast;

    #[Url]
    public string $tab = 'ringkasan';

    public int $idRawat;

    public bool $showAlergi = false;
    public ?int $alergenId = null;
    public string $reaksi = '';

    public function mount(int $idRawat): void
    {
        $this->idRawat = $idRawat;
        $this->rawat; // jika id tidak ada, langsung tampil halaman 404
    }

    #[Computed]
    public function rawat(): RawatInap
    {
        return RawatInap::with(['pasien.alergi.alergen', 'kamar.bangsal'])->findOrFail($this->idRawat);
    }

    /**
     * kunci tab => [label, nama komponen, class komponen, pemilik]
     * Tab selain ringkasan otomatis aktif begitu class-nya dibuat oleh pemiliknya.
     */
    public function tabs(): array
    {
        return [
            'ringkasan' => ['Ringkasan', null, null, null],
            'skrining' => ['Skrining', 'pasien.tab-skrining', \App\Livewire\Pasien\TabSkrining::class, 'Orang 3'],
            'asesmen' => ['Asesmen ADIME', 'pasien.tab-asesmen', \App\Livewire\Pasien\TabAsesmen::class, 'Orang 3'],
            'order-diet' => ['Order Diet', 'pasien.tab-order-diet', \App\Livewire\Pasien\TabOrderDiet::class, 'Orang 4'],
            'asupan' => ['Asupan & Sisa', 'pasien.tab-asupan', \App\Livewire\Pasien\TabAsupan::class, 'Orang 3'],
            'edukasi' => ['Edukasi', 'pasien.tab-edukasi', \App\Livewire\Pasien\TabEdukasi::class, 'Orang 3'],
        ];
    }

    public function tambahAlergi(): void
    {
        $this->validate([
            'alergenId' => ['required', 'integer', 'exists:alergen,id_alergen'],
            'reaksi' => ['nullable', 'max:150'],
        ], [
            'alergenId.required' => 'Pilih alergen dulu.',
            'reaksi.max' => 'Reaksi maksimal 150 karakter.',
        ]);

        $idPasien = $this->rawat->id_pasien;

        if (PasienAlergi::where('id_pasien', $idPasien)->where('id_alergen', $this->alergenId)->exists()) {
            $this->addError('alergenId', 'Alergen ini sudah tercatat untuk pasien.');
            return;
        }

        PasienAlergi::create([
            'id_pasien' => $idPasien,
            'id_alergen' => $this->alergenId,
            'reaksi' => $this->reaksi === '' ? null : $this->reaksi,
        ]);

        $this->reset('alergenId', 'reaksi');
        $this->showAlergi = false;
        unset($this->rawat); // muat ulang data
        $this->success('Alergi ditambahkan.');
    }

    public function hapusAlergi(int $idPa): void
    {
        PasienAlergi::where('id_pa', $idPa)->where('id_pasien', $this->rawat->id_pasien)->delete();
        unset($this->rawat);
        $this->success('Alergi dihapus.');
    }

    public function render()
    {
        $r = $this->rawat;
        $gizi = RingkasanGizi::untuk([$r->id_rawat]);
        $sudahAda = $r->pasien->alergi->pluck('id_alergen')->all();
        $tabs = $this->tabs();

        if (! isset($tabs[$this->tab])) {
            $this->tab = 'ringkasan';
        }

        return view('livewire.detail-pasien', [
            'r' => $r,
            'p' => $r->pasien,
            'skrining' => $gizi['risiko']->get($r->id_rawat),
            'dietAktif' => $gizi['diet']->get($r->id_rawat),
            'tabs' => $tabs,
            'alergenOptions' => Alergen::whereNotIn('id_alergen', $sudahAda)->orderBy('nama_alergen')->get()
                ->map(fn ($a) => ['id' => $a->id_alergen, 'name' => $a->nama_alergen])->all(),
        ]);
    }
}