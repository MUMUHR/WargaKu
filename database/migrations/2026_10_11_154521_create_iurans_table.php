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
        Schema::create('iurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keluarga_id')->constrained('keluargas')->cascadeOnDelete();
            $table->decimal('nominal', 10, 2)->default(50000.00);
            $table->text('catatan')->nullable();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->dateTime('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->unique(['keluarga_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iurans');
    }
};
