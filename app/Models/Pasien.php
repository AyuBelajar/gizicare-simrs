<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';
    protected $primaryKey = 'id_pasien';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['tgl_lahir' => 'date'];

    public function rawatInap(): HasMany
    {
        return $this->hasMany(RawatInap::class, 'id_pasien', 'id_pasien');
    }

    public function alergi(): HasMany
    {
        return $this->hasMany(PasienAlergi::class, 'id_pasien', 'id_pasien');
    }

    protected function umur(): Attribute
    {
        return Attribute::get(fn () => $this->tgl_lahir ? (int) abs($this->tgl_lahir->diffInYears(now())) : null);
    }

    /** Dua huruf awal untuk avatar, mis. SA dari Siti Aminah. */
    protected function inisial(): Attribute
    {
        return Attribute::get(fn () => collect(explode(' ', trim($this->nama_pasien)))
            ->take(2)->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))->implode(''));
    }
}