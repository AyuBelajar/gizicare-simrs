<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel skrining_gizi
        Schema::create('skrining_gizi', function (Blueprint $table) {
            $table->id('id_skrining');
            $table->unsignedBigInteger('id_rawat');
            $table->unsignedBigInteger('id_user');
            $table->enum('instrumen', ['MST', 'NRS-2002', 'STRONGkids']);
            $table->integer('skor');
            $table->enum('status_risiko', ['berisiko', 'tidak_berisiko']);
            $table->dateTime('tanggal');
            $table->date('jadwal_reskrining')->nullable();

            $table->foreign('id_rawat')->references('id_rawat')->on('rawat_inap')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users');
        });

        // 2. Tabel asesmen_gizi
        Schema::create('asesmen_gizi', function (Blueprint $table) {
            $table->id('id_asesmen');
            $table->unsignedBigInteger('id_rawat');
            $table->unsignedBigInteger('id_user');
            $table->decimal('bb', 5, 2);
            $table->decimal('tb', 5, 2);
            $table->decimal('imt', 5, 2);
            $table->decimal('kebutuhan_energi', 8, 2);
            $table->decimal('kebutuhan_protein', 6, 2);
            $table->text('diagnosis_gizi');
            $table->text('intervensi');
            $table->text('rencana_monitor');
            $table->dateTime('tanggal');

            $table->foreign('id_rawat')->references('id_rawat')->on('rawat_inap')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users');
        });

        // 3. Tabel edukasi_gizi
        Schema::create('edukasi_gizi', function (Blueprint $table) {
            $table->id('id_edukasi');
            $table->unsignedBigInteger('id_rawat');
            $table->unsignedBigInteger('id_user');
            $table->text('materi');
            $table->dateTime('tanggal');

            $table->foreign('id_rawat')->references('id_rawat')->on('rawat_inap')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users');
        });

        // 4. Tabel pengukuran_bb
        Schema::create('pengukuran_bb', function (Blueprint $table) {
            $table->id('id_bb');
            $table->unsignedBigInteger('id_rawat');
            $table->unsignedBigInteger('id_user');
            $table->decimal('berat_badan', 5, 2);
            $table->date('tanggal');

            $table->foreign('id_rawat')->references('id_rawat')->on('rawat_inap')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengukuran_bb');
        Schema::dropIfExists('edukasi_gizi');
        Schema::dropIfExists('asesmen_gizi');
        Schema::dropIfExists('skrining_gizi');
    }
};