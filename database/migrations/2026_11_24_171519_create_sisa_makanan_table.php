<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sisa_makanan', function (Blueprint $table) {
            $table->id('id_sisa');
            $table->foreignId('id_rawat')->constrained('rawat_inap', 'id_rawat')->cascadeOnDelete();
            $table->foreignId('id_waktu')->constrained('waktu_makan', 'id_waktu')->restrictOnDelete();
            $table->foreignId('id_user')->constrained('users', 'id_user')->restrictOnDelete();
            $table->date('tanggal');
            $table->smallInteger('skor_sisa');   // persen sisa metode Comstock
            $table->timestamp('created_at')->useCurrent();

            // Satu skor per pasien per waktu makan per hari
            $table->unique(['id_rawat', 'id_waktu', 'tanggal']);
        });

        // Hanya boleh 0, 25, 50, 75, atau 100
        DB::statement('ALTER TABLE sisa_makanan ADD CONSTRAINT cek_skor_sisa CHECK (skor_sisa IN (0, 25, 50, 75, 100))');
    }

    public function down(): void { Schema::dropIfExists('sisa_makanan'); }
};