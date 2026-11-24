<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasienAlergi extends Model
{
    protected $table = 'pasien_alergi';
    protected $primaryKey = 'id_pa';
    public $timestamps = false;
    protected $guarded = [];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'id_pasien', 'id_pasien');
    }

    public function alergen(): BelongsTo
    {
        return $this->belongsTo(Alergen::class, 'id_alergen', 'id_alergen');
    }
}