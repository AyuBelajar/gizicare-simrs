<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawatInap extends Model
{
    protected $table = 'rawat_inap';
    protected $primaryKey = 'id_rawat';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['tgl_masuk' => 'datetime', 'tgl_keluar' => 'datetime'];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'id_pasien', 'id_pasien');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'id_kamar', 'id_kamar');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /** Hari rawat ke-N (hari masuk = hari ke-1). */
    protected function hariRawat(): Attribute
    {
        return Attribute::get(fn () => (int) abs($this->tgl_masuk->copy()->startOfDay()->diffInDays(today())) + 1);
    }
}