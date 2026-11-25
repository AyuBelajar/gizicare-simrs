<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdukasiGizi extends Model
{
    protected $table = 'edukasi_gizi';
    protected $primaryKey = 'id_edukasi';
    public $timestamps = false;

    protected $fillable = [
        'id_rawat',
        'id_user',
        'materi',
        'tanggal',
    ];
}