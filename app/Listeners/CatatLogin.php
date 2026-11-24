<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;

/**
 * Dijalankan otomatis setiap kali login berhasil.
 * Laravel menemukan listener ini sendiri karena berada di folder app/Listeners
 * dan fungsi handle() menerima event Login.
 */
class CatatLogin
{
    public function handle(Login $event): void
    {
        AuditLog::create([
            'id_user'       => $event->user->getKey(),
            'aksi'          => 'Login',
            'tabel_terkait' => 'users',
            'id_record'     => $event->user->getKey(),
            'data_before'   => null,
            'data_after'    => 'Login berhasil',
            'ip_address'    => request()->ip(),
        ]);
    }
}