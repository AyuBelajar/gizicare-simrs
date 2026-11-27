<?php

namespace App\Livewire;

use App\Models\Bangsal;
use App\Models\JenisDiet;
use App\Models\RawatInap;
use App\Support\RingkasanGizi;
use App\Support\Tabel;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DaftarPasien extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $bangsal = '';

    #[Url]
    public string $diet = '';

    #[Url]
    public string $risiko = '';

    public function updated(string $name): void
    {
        if (in_array($name, ['search', 'bangsal', 'diet', 'risiko'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = RawatInap::query()->aktif()->with(['pasien.alergi.alergen', 'kamar.bangsal']);

        // Pencarian: nama, No. RM, atau kamar (boleh ditulis "Melati 203")
        if ($this->search !== '') {
            $like = '%'.$this->search.'%';
            $query->where(function ($w) use ($like) {
                $w->whereHas('pasien', fn ($p) => $p->where(fn ($x) => $x->where('nama_pasien', 'ilike', $like)->orWhere('no_rm', 'ilike', $like)))
                  ->orWhereHas('kamar', fn ($k) => $k
                      ->join('bangsal', 'bangsal.id_bangsal', '=', 'kamar.id_bangsal')
                      ->whereRaw("(bangsal.nama_bangsal || ' ' || kamar.no_kamar) ilike ?", [$like]));
            });
        }

        if ($this->bangsal !== '') {
            $query->whereHas('kamar', fn ($k) => $k->where('id_bangsal', (int) $this->bangsal));
        }

        // Filter diet: hanya jalan jika tabel order_diet (Orang 4) sudah ada
        if ($this->diet !== '' && Tabel::ada('order_diet')) {
            $query->whereExists(fn ($x) => $x->select(DB::raw(1))->from('order_diet')
                ->whereColumn('order_diet.id_rawat', 'rawat_inap.id_rawat')
                ->where('order_diet.status', 'aktif')
                ->where('order_diet.id_diet', (int) $this->diet));
        }

        // Filter risiko: hanya jalan jika tabel skrining_gizi (Orang 3) sudah ada
        if ($this->risiko !== '' && Tabel::ada('skrining_gizi')) {
            if ($this->risiko === 'belum') {
                $query->whereNotExists(fn ($x) => $x->select(DB::raw(1))->from('skrining_gizi')
                    ->whereColumn('skrining_gizi.id_rawat', 'rawat_inap.id_rawat'));
            } else {
                $query->whereRaw('(select s.status_risiko from skrining_gizi s where s.id_rawat = rawat_inap.id_rawat order by s.tanggal desc, s.id_skrining desc limit 1) = ?', [$this->risiko]);
            }
        }

        $rows = $query->orderByDesc('tgl_masuk')->paginate(12);
        $gizi = RingkasanGizi::untuk($rows->pluck('id_rawat')->all());

        return view('livewire.daftar-pasien', [
            'rows' => $rows,
            'diet_aktif' => $gizi['diet'],
            'skrining' => $gizi['risiko'],
            'bangsalOptions' => Bangsal::orderBy('nama_bangsal')->get()
                ->map(fn ($b) => ['id' => $b->id_bangsal, 'name' => $b->nama_bangsal])->all(),
            'dietOptions' => JenisDiet::orderBy('nama_diet')->get()
                ->map(fn ($d) => ['id' => $d->id_diet, 'name' => $d->nama_diet])->all(),
            'risikoOpsi' => ['' => 'Semua', 'tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah', 'belum' => 'Belum diskrining'],
        ]);
    }
}