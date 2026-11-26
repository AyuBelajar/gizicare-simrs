<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusi_makanan', function (Blueprint $table) {
            $table->id('id_distribusi');
            $table->foreignId('id_bangsal')->constrained('bangsal', 'id_bangsal')->restrictOnDelete();
            $table->foreignId('id_waktu')->constrained('waktu_makan', 'id_waktu')->restrictOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['disiapkan', 'dikirim', 'diterima'])->default('disiapkan');

            // Satu status untuk tiap bangsal per waktu makan per hari
            $table->unique(['id_bangsal', 'id_waktu', 'tanggal']);
        });
    }

    public function down(): void { Schema::dropIfExists('distribusi_makanan'); }
};