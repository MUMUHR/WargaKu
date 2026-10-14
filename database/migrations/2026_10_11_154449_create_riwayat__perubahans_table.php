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
        Schema::create('riwayat__perubahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->nullOnDelete();
            $table->foreignId('keluarga_id')->constrained('keluargas')->cascadeOnDelete();
            $table->enum('jenis_pengajuan', ['ubah_data', 'tambah_anggota']);
            $table->json('data_lama')->nullable();
            $table->json('data_baru');
            $table->string('bukti_path', 255);
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan'])->default('menunggu');
            $table->text('alasan')->nullable();
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat__perubahans');
    }
};
