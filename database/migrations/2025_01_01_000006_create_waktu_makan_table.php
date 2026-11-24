<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waktu_makan', function (Blueprint $table) {
            $table->id('id_waktu');
            $table->string('nama_waktu', 30)->unique();
            $table->time('jam');
        });
    }

    public function down(): void { Schema::dropIfExists('waktu_makan'); }
};