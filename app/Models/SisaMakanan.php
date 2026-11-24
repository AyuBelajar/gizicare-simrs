<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SisaMakanan extends Model
{
    use Auditable;

    protected $table = 'sisa_makanan';
    protected $primaryKey = 'id_sisa';
    public $timestamps = false;   // created_at diisi otomatis oleh database
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date', 'skor_sisa' => 'integer'];

    public const SKOR = [0, 25, 50, 75, 100];

    public function rawatInap(): BelongsTo
    {
        return $this->belongsTo(RawatInap::class, 'id_rawat', 'id_rawat');
    }

    public function waktuMakan(): BelongsTo
    {
        return $this->belongsTo(WaktuMakan::class, 'id_waktu', 'id_waktu');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}