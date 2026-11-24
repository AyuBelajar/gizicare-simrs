<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siklus_menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->smallInteger('hari_ke');   // hari ke-1 s.d. ke-10 dalam satu siklus
            $table->foreignId('id_waktu')->constrained('waktu_makan', 'id_waktu')->restrictOnDelete();
            $table->foreignId('id_diet')->constrained('jenis_diet', 'id_diet')->restrictOnDelete();
            $table->text('deskripsi_menu');

            // Satu menu untuk tiap kombinasi hari × waktu makan × jenis diet
            $table->unique(['hari_ke', 'id_waktu', 'id_diet']);
        });
    }

    public function down(): void { Schema::dropIfExists('siklus_menu'); }
};