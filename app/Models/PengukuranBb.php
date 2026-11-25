<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengukuranBb extends Model
{
    protected $table = 'pengukuran_bb';
    protected $primaryKey = 'id_bb';
    public $timestamps = false;

    protected $fillable = [
        'id_rawat',
        'id_user',
        'berat_badan',
        'tanggal',
    ];
}