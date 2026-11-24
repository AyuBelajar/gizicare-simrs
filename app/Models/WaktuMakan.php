<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class WaktuMakan extends Model
{
    protected $table = 'waktu_makan';
    protected $primaryKey = 'id_waktu';
    public $timestamps = false;
    protected $guarded = [];

    /** PostgreSQL mengembalikan 07:00:00, kita tampilkan 07:00 saja. */
    protected function jam(): Attribute
    {
        return Attribute::get(fn ($nilai) => $nilai ? substr($nilai, 0, 5) : $nilai);
    }
}