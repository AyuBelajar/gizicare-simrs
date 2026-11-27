<?php

namespace App\Livewire\Dapur;

use App\Models\Bangsal;
use App\Models\DistribusiMakanan;
use App\Models\WaktuMakan;
use Livewire\Component;
use Mary\Traits\Toast;

/**
 * Status distribusi makanan per bangsal per waktu makan.
 * Alur: belum → disiapkan → dikirim → diterima.
 * Ditampilkan di halaman Rekap Porsi.
 */
class StatusDistribusi extends Component
{
    use Toast;

    public $tanggal;

    public function mount(): void
    {
        $this->tanggal = today()->toDateString();
    }

    /** Hanya petugas dapur yang boleh mengubah status, dan hanya untuk hari ini. */
    public function bisaMengubah(): bool
    {
        return auth()->user()->hasRole('petugas_dapur')
            && $this->tanggal === today()->toDateString();
    }

    /** Majukan status satu langkah. Kalau belum ada data, dibuat dengan status "disiapkan". */
    public function majukan(int $idBangsal, int $idWaktu): void
    {
        if (! $this->bisaMengubah()) {
            $this->error('Status hanya bisa diubah petugas dapur untuk tanggal hari ini.');
            return;
        }

        $d = DistribusiMakanan::firstOrNew([
            'id_bangsal' => $idBangsal,
            'id_waktu'   => $idWaktu,
            'tanggal'    => $this->tanggal,
        ]);

        if (! $d->exists) {
            $d->status = 'disiapkan';
        } else {
            $urutan = array_search($d->status, DistribusiMakanan::STATUS, true);
            if ($urutan === count(DistribusiMakanan::STATUS) - 1) {
                $this->info('Makanan sudah diterima bangsal.');
                return;
            }
            $d->status = DistribusiMakanan::STATUS[$urutan + 1];
        }

        $d->save();
        $this->success('Status diperbarui: ' . $d->status . '.');
    }

    /** Mundurkan satu langkah, untuk membetulkan salah klik. */
    public function mundurkan(int $idBangsal, int $idWaktu): void
    {
        if (! $this->bisaMengubah()) {
            return;
        }

        $d = DistribusiMakanan::where('id_bangsal', $idBangsal)
            ->where('id_waktu', $idWaktu)
            ->whereDate('tanggal', $this->tanggal)
            ->first();

        if (! $d) {
            return;
        }

        $urutan = array_search($d->status, DistribusiMakanan::STATUS, true);

        if ($urutan === 0) {
            $d->delete();   // dari "disiapkan" kembali ke "belum"
        } else {
            $d->update(['status' => DistribusiMakanan::STATUS[$urutan - 1]]);
        }

        $this->info('Status dikembalikan satu langkah.');
    }

    public function render()
    {
        $bangsal = Bangsal::orderBy('nama_bangsal')->get();
        $waktu   = WaktuMakan::orderBy('jam')->get();

        // Kunci "idBangsal-idWaktu" supaya mudah dicari di tabel
        $status = DistribusiMakanan::whereDate('tanggal', $this->tanggal)->get()
            ->keyBy(fn ($d) => $d->id_bangsal . '-' . $d->id_waktu);

        return view('livewire.dapur.status-distribusi', [
            'bangsal'       => $bangsal,
            'waktu'         => $waktu,
            'status'        => $status,
            'totalSel'      => $bangsal->count() * $waktu->count(),
            'jumlahDiterima'=> $status->where('status', 'diterima')->count(),
            'bisaMengubah'  => $this->bisaMengubah(),
        ]);
    }
}