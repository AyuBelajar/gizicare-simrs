<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasien_alergi', function (Blueprint $table) {
            $table->id('id_pa');
            $table->foreignId('id_pasien')->constrained('pasien', 'id_pasien')->cascadeOnDelete();
            $table->foreignId('id_alergen')->constrained('alergen', 'id_alergen')->restrictOnDelete();
            $table->string('reaksi', 150)->nullable();
            $table->unique(['id_pasien', 'id_alergen']);
        });
    }

    public function down(): void { Schema::dropIfExists('pasien_alergi'); }
};