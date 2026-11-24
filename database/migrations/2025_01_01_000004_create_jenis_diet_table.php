<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_diet', function (Blueprint $table) {
            $table->id('id_diet');
            $table->string('kode', 10)->unique();
            $table->string('nama_diet', 100);
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void { Schema::dropIfExists('jenis_diet'); }
};