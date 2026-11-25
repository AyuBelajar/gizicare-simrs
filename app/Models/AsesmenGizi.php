<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsesmenGizi extends Model
{
    protected $table = 'asesmen_gizi';
    protected $primaryKey = 'id_asesmen';
    public $timestamps = false;

    protected $fillable = [
        'id_rawat',
        'id_user',
        'bb',
        'tb',
        'imt',
        'kebutuhan_energi',
        'kebutuhan_protein',
        'diagnosis_gizi',
        'intervensi',
        'rencana_monitor',
        'tanggal',
    ];
}