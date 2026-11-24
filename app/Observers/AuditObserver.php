<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    /**
     * Kolom yang tidak perlu dicatat.
     * password_hash tidak boleh tersimpan di log demi keamanan.
     */
    private array $abaikan = ['password_hash', 'remember_token', 'created_at', 'updated_at'];

    public function created(Model $model): void
    {
        $this->catat('Tambah', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $sesudah = $this->bersihkan($model->getChanges());

        // Tidak ada perubahan berarti (misalnya hanya updated_at) → tidak dicatat
        if (empty($sesudah)) {
            return;
        }

        // Ambil nilai lama hanya untuk kolom yang berubah
        $sebelum = array_intersect_key($model->getOriginal(), $sesudah);

        $this->catat('Ubah', $model, $sebelum, $sesudah);
    }

    public function deleted(Model $model): void
    {
        // Berlaku juga untuk soft delete (kolom deleted_at terisi)
        $this->catat('Hapus', $model, $model->getOriginal(), null);
    }

    private function bersihkan(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        return array_diff_key($data, array_flip($this->abaikan));
    }

    private function keJson(?array $data): ?string
    {
        if ($data === null) {
            return null;
        }

        return json_encode($this->bersihkan($data), JSON_UNESCAPED_UNICODE);
    }

    private function catat(string $aksi, Model $model, ?array $sebelum, ?array $sesudah): void
    {
        // Seeder, tinker, dan perintah artisan lain tidak dicatat,
        // supaya audit log tidak penuh dengan data dummy.
        if (app()->runningInConsole()) {
            return;
        }

        AuditLog::create([
            'id_user'       => auth()->id(),
            'aksi'          => $aksi,
            'tabel_terkait' => $model->getTable(),
            'id_record'     => $model->getKey(),
            'data_before'   => $this->keJson($sebelum),
            'data_after'    => $this->keJson($sesudah),
            'ip_address'    => request()->ip(),
        ]);
    }
}