<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->foreignId('id_user')->nullable()
                ->constrained('users', 'id_user')->nullOnDelete();
            $table->string('aksi', 20);            // Tambah | Ubah | Hapus | Login
            $table->string('tabel_terkait', 50);
            $table->unsignedBigInteger('id_record')->nullable();
            $table->text('data_before')->nullable();
            $table->text('data_after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
