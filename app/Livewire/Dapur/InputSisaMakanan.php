<?php

namespace App\Livewire\Dapur;

use App\Models\Bangsal;
use App\Models\RawatInap;
use App\Models\SisaMakanan;
use App\Models\WaktuMakan;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('components.layouts.app')]
#[Title('Input Sisa Makanan')]
class InputSisaMakanan extends Component
{
    use Toast;

    // Filter
    public $tanggal;
    public $id_bangsal;
    public $id_waktu;

    // Skor per pasien: [id_rawat => 0|25|50|75|100|null]
    public array $skor = [];

    // Salinan skor yang sudah ada di database, untuk menandai baris yang belum disimpan
    public array $tersimpan = [];

    public function mount(): void
    {
        $this->tanggal    = today()->toDateString();
        $this->id_bangsal = Bangsal::orderBy('nama_bangsal')->value('id_bangsal');

        // Pilih otomatis waktu makan terakhir yang jamnya sudah lewat (misalnya jam 13.00 → Siang)
        $waktu = WaktuMakan::orderBy('jam')->get();
        $this->id_waktu = $waktu->filter(fn ($w) => $w->jam <= now()->format('H:i'))->last()?->id_waktu
            ?? $waktu->first()?->id_waktu;

        $this->muat();
    }

    // Setiap filter berubah, ambil ulang daftar pasien dan skornya
    public function updatedTanggal(): void   { $this->muat(); }
    public function updatedIdBangsal(): void { $this->muat(); }
    public function updatedIdWaktu(): void   { $this->muat(); }

    /** Hanya petugas dapur yang boleh mengisi; ahli gizi dan kepala hanya melihat. */
    public function bisaMengisi(): bool
    {
        return auth()->user()->hasRole('petugas_dapur');
    }

    /** Pasien rawat inap aktif di bangsal terpilih. */
    private function daftarPasien(): Collection
    {
        if (! $this->id_bangsal) {
            return collect();
        }

        return RawatInap::aktif()
            ->with(['pasien', 'kamar'])
            ->whereHas('kamar', fn ($q) => $q->where('id_bangsal', $this->id_bangsal))
            ->whereDate('tgl_masuk', '<=', $this->tanggal)
            ->get()
            ->sortBy(fn ($r) => [$r->kamar->no_kamar, $r->pasien->nama_pasien])
            ->values();
    }

    private function muat(): void
    {
        $idRawat = $this->daftarPasien()->pluck('id_rawat');

        $ada = SisaMakanan::whereIn('id_rawat', $idRawat)
            ->where('id_waktu', $this->id_waktu)
            ->whereDate('tanggal', $this->tanggal)
            ->pluck('skor_sisa', 'id_rawat');

        $this->skor = $idRawat->mapWithKeys(fn ($id) => [$id => $ada[$id] ?? null])->all();
        $this->tersimpan = $this->skor;
    }

    public function pilih(int $idRawat, int $nilai): void
    {
        if (! $this->bisaMengisi() || ! in_array($nilai, SisaMakanan::SKOR, true)) {
            return;
        }

        $this->skor[$idRawat] = $nilai;
    }

    /** Isi semua baris yang masih kosong dengan nilai tertentu (biasanya 0% = habis). */
    public function isiYangKosong(int $nilai): void
    {
        if (! $this->bisaMengisi()) {
            return;
        }

        foreach ($this->skor as $id => $s) {
            if ($s === null) {
                $this->skor[$id] = $nilai;
            }
        }
    }

    public function simpan(): void
    {
        if (! $this->bisaMengisi()) {
            $this->error('Hanya petugas dapur yang dapat menyimpan sisa makanan.');
            return;
        }

        $this->validate([
            'tanggal'    => ['required', 'date', 'before_or_equal:today'],
            'id_waktu'   => ['required', 'exists:waktu_makan,id_waktu'],
            'id_bangsal' => ['required', 'exists:bangsal,id_bangsal'],
        ], [
            'tanggal.before_or_equal' => 'Sisa makanan tidak bisa diisi untuk tanggal yang akan datang.',
        ]);

        $berubah = [];

        foreach ($this->skor as $idRawat => $nilai) {
            if ($nilai === null || $nilai === ($this->tersimpan[$idRawat] ?? null)) {
                continue;   // kosong atau tidak berubah → lewati
            }

            SisaMakanan::updateOrCreate(
                ['id_rawat' => $idRawat, 'id_waktu' => $this->id_waktu, 'tanggal' => $this->tanggal],
                ['skor_sisa' => $nilai, 'id_user' => auth()->id()],
            );

            $berubah[] = $idRawat;
        }

        if (empty($berubah)) {
            $this->info('Tidak ada perubahan untuk disimpan.');
            return;
        }

        $this->cekPeringatan($berubah);
        $this->tersimpan = $this->skor;
        $this->success(count($berubah) . ' data sisa makanan tersimpan.');
    }

    /**
     * Dipanggil setelah simpan, untuk pasien yang skornya baru/berubah.
     * TODO (minggu 4): sambungkan ke service peringatan buatan Orang 4,
     * yang membuat peringatan bila sisa > 50% tiga kali berturut-turut.
     */
    private function cekPeringatan(array $idRawat): void
    {
        // contoh nanti: app(\App\Services\PeringatanService::class)->cekSisaMakanan($idRawat);
    }

    public function render()
    {
        $pasien = $this->daftarPasien();

        return view('livewire.dapur.input-sisa-makanan', [
            'pasien'       => $pasien,
            'opsiBangsal'  => Bangsal::orderBy('nama_bangsal')->get()
                ->map(fn ($b) => ['id' => $b->id_bangsal, 'name' => $b->nama_bangsal]),
            'opsiWaktu'    => WaktuMakan::orderBy('jam')->get()
                ->map(fn ($w) => ['id' => $w->id_waktu, 'name' => "{$w->nama_waktu} ({$w->jam})"]),
            'jumlahTerisi' => collect($this->skor)->filter(fn ($s) => $s !== null)->count(),
            'adaPerubahan' => $this->skor != $this->tersimpan,
            'bisaMengisi'  => $this->bisaMengisi(),
        ]);
    }
}