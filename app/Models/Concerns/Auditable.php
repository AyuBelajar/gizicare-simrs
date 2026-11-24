<?php

namespace App\Models\Concerns;

use App\Observers\AuditObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Pasang di setiap model (kecuali AuditLog) dengan:  use Auditable;
 * Setiap tambah / ubah / hapus data lewat model otomatis tercatat di tabel audit_log.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        // Event didaftarkan satu per satu (bukan static::observe),
        // karena Laravel 13 melarang observe() dipanggil saat model sedang booting.
        static::created(fn (Model $model) => app(AuditObserver::class)->created($model));
        static::updated(fn (Model $model) => app(AuditObserver::class)->updated($model));
        static::deleted(fn (Model $model) => app(AuditObserver::class)->deleted($model));
    }
}