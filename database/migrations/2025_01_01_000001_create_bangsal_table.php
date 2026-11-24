<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bangsal', function (Blueprint $table) {
            $table->id('id_bangsal');
            $table->string('nama_bangsal', 50)->unique();
            $table->unsignedSmallInteger('kapasitas')->default(0);
        });
    }

    public function down(): void { Schema::dropIfExists('bangsal'); }
};