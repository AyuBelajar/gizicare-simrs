<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bangsal extends Model
{
    protected $table = 'bangsal';
    protected $primaryKey = 'id_bangsal';
    public $timestamps = false;
    protected $guarded = [];

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class, 'id_bangsal', 'id_bangsal');
    }
}