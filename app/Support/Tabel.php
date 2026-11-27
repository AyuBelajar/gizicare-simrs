<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/** Mengecek apakah tabel milik anggota lain sudah ada di database. */
class Tabel
{
    protected static array $cache = [];

    public static function ada(string $nama): bool
    {
        return static::$cache[$nama] ??= Schema::hasTable($nama);
    }
}