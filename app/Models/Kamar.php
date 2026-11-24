<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $primaryKey = 'id_kamar';
    public $timestamps = false;
    protected $guarded = [];

    public function bangsal(): BelongsTo
    {
        return $this->belongsTo(Bangsal::class, 'id_bangsal', 'id_bangsal');
    }

    public function rawatInap(): HasMany
    {
        return $this->hasMany(RawatInap::class, 'id_kamar', 'id_kamar');
    }

    /** Contoh hasil: Melati 203 (kata dalam kurung pada nama bangsal dibuang). */
    protected function label(): Attribute
    {
        return Attribute::get(function () {
            $nama = preg_replace('/\s*\(.*\)/', '', $this->bangsal?->nama_bangsal ?? '');
            return trim($nama.' '.$this->no_kamar);
        });
    }
}