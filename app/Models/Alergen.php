<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alergen extends Model
{
    protected $table = 'alergen';
    protected $primaryKey = 'id_alergen';
    public $timestamps = false;
    protected $guarded = [];

    public function pasienAlergi(): HasMany
    {
        return $this->hasMany(PasienAlergi::class, 'id_alergen', 'id_alergen');
    }
}