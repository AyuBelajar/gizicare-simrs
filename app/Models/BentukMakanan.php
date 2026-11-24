<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BentukMakanan extends Model
{
    protected $table = 'bentuk_makanan';
    protected $primaryKey = 'id_bentuk';
    public $timestamps = false;
    protected $guarded = [];
}