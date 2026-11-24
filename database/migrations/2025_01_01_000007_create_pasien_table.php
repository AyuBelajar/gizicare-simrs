<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id('id_pasien');
            $table->string('no_rm', 20)->unique();
            $table->string('nama_pasien', 100);
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('kategori', ['dewasa', 'anak']);
            $table->string('no_hp', 20)->nullable();
        });
    }

    public function down(): void { Schema::dropIfExists('pasien'); }
};