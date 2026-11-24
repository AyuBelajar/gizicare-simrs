<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rawat_inap', function (Blueprint $table) {
            $table->id('id_rawat');
            $table->foreignId('id_pasien')->constrained('pasien', 'id_pasien')->restrictOnDelete();
            $table->foreignId('id_kamar')->constrained('kamar', 'id_kamar')->restrictOnDelete();
            $table->dateTime('tgl_masuk');
            $table->dateTime('tgl_keluar')->nullable();
            $table->string('diagnosis_medis', 255)->nullable();
            $table->enum('status', ['aktif', 'pulang'])->default('aktif');
            $table->index('status');
        });
    }

    public function down(): void { Schema::dropIfExists('rawat_inap'); }
};