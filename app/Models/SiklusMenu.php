<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiklusMenu extends Model
{
    use Auditable;

    protected $table = 'siklus_menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false;
    protected $guarded = [];

    public function waktuMakan(): BelongsTo
    {
        return $this->belongsTo(WaktuMakan::class, 'id_waktu', 'id_waktu');
    }

    public function jenisDiet(): BelongsTo
    {
        return $this->belongsTo(JenisDiet::class, 'id_diet', 'id_diet');
    }
}