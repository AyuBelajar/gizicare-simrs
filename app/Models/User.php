<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id_user';

    protected $fillable = ['name', 'email', 'password_hash', 'role', 'is_active', 'last_login'];

    protected $hidden = ['password_hash'];

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

    // Beri tahu Laravel bahwa kolom password bernama password_hash
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABEL[$this->role] ?? $this->role;
    }
}
