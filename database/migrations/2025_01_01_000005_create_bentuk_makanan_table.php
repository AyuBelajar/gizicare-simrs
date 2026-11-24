<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bentuk_makanan', function (Blueprint $table) {
            $table->id('id_bentuk');
            $table->string('nama_bentuk', 50)->unique();
        });
    }

    public function down(): void { Schema::dropIfExists('bentuk_makanan'); }
};