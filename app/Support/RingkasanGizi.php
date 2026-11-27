<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/** Mengambil diet aktif dan skrining terakhir banyak pasien sekaligus (tanpa N+1 query). */
class RingkasanGizi
{
    /** @param array<int> $idRawat */
    public static function untuk(array $idRawat): array
    {
        $diet = collect();
        $risiko = collect();

        if ($idRawat && Tabel::ada('order_diet')) {
            $diet = DB::table('order_diet as o')
                ->join('jenis_diet as d', 'd.id_diet', '=', 'o.id_diet')
                ->leftJoin('bentuk_makanan as b', 'b.id_bentuk', '=', 'o.id_bentuk')
                ->whereIn('o.id_rawat', $idRawat)
                ->where('o.status', 'aktif')
                ->orderBy('o.id_order')
                ->get(['o.id_rawat', 'd.nama_diet', 'b.nama_bentuk', 'o.catatan'])
                ->keyBy('id_rawat'); // jika ada beberapa, yang terakhir dipakai
        }

        if ($idRawat && Tabel::ada('skrining_gizi')) {
            $risiko = DB::table('skrining_gizi')
                ->whereIn('id_rawat', $idRawat)
                ->orderBy('tanggal')->orderBy('id_skrining')
                ->get(['id_rawat', 'instrumen', 'skor', 'status_risiko'])
                ->keyBy('id_rawat');
        }

        return ['diet' => $diet, 'risiko' => $risiko];
    }

    /** Kelas warna badge daisyUI untuk status risiko. */
    public static function badgeRisiko(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'tinggi' => 'badge-error',
            'sedang' => 'badge-warning',
            'rendah' => 'badge-success',
            default => 'badge-ghost',
        };
    }
}