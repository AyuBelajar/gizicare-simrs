<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Auditable, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public const ROLE_LABEL = [
        'ahli_gizi'        => 'Ahli Gizi',
        'petugas_dapur'    => 'Petugas Dapur',
        'admin'            => 'Admin',
        'kepala_instalasi' => 'Kepala Instalasi',
    ];

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',   // otomatis di-hash saat disimpan
            'is_active'     => 'boolean',
            'last_login'    => 'datetime',
        ];
    }

    /**
     * Beri tahu Laravel bahwa kolom password bernama password_hash (sesuai ERD).
     */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    /**
     * Contoh: auth()->user()->hasRole('ahli_gizi', 'kepala_instalasi')
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Contoh: auth()->user()->role_label  → "Ahli Gizi"
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABEL[$this->role] ?? $this->role;
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'id_user', 'id_user');
    }
}