<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkriningGizi extends Model
{
    protected $table = 'skrining_gizi';
    protected $primaryKey = 'id_skrining';
    public $timestamps = false;

    protected $fillable = [
        'id_rawat',
        'id_user',
        'instrumen',
        'skor',
        'status_risiko',
        'tanggal',
        'jadwal_reskrining',
    ];
}