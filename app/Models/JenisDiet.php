<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisDiet extends Model
{
    protected $table = 'jenis_diet';
    protected $primaryKey = 'id_diet';
    public $timestamps = false;
    protected $guarded = [];
}