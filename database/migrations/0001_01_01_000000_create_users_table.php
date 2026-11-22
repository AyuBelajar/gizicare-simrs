<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->enum('role', ['ahli_gizi', 'petugas_dapur', 'admin', 'kepala_instalasi']);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();      // created_at, updated_at
            $table->softDeletes();     // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
