<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistribusiMakanan extends Model
{
    use Auditable;

    protected $table = 'distribusi_makanan';
    protected $primaryKey = 'id_distribusi';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date'];

    public const STATUS = ['disiapkan', 'dikirim', 'diterima'];

    public function bangsal(): BelongsTo
    {
        return $this->belongsTo(Bangsal::class, 'id_bangsal', 'id_bangsal');
    }

    public function waktuMakan(): BelongsTo
    {
        return $this->belongsTo(WaktuMakan::class, 'id_waktu', 'id_waktu');
    }
}