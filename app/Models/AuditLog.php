<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    protected $primaryKey = 'id_log';
    const UPDATED_AT = null;   // tabel ini hanya punya created_at

    protected $fillable = ['id_user', 'aksi', 'tabel_terkait', 'id_record',
                           'data_before', 'data_after', 'ip_address'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user')->withTrashed();
    }
}
