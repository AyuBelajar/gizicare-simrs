<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id('id_kamar');
            $table->foreignId('id_bangsal')->constrained('bangsal', 'id_bangsal')->restrictOnDelete();
            $table->string('no_kamar', 20);
            $table->string('kelas', 10);
            $table->unique(['id_bangsal', 'no_kamar']);
        });
    }

    public function down(): void { Schema::dropIfExists('kamar'); }
};