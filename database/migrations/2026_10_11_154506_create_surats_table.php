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
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pemohon_id')
                ->nullable()
                ->constrained('wargas')
                ->nullOnDelete();

            $table->foreignId('warga_tujuan_id')
                ->nullable()
                ->constrained('wargas')
                ->nullOnDelete();

            $table->text('keperluan');

            $table->string('nomor_surat', 50)->nullable()->unique();

            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan'])
                ->default('menunggu');

            $table->text('alasan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
