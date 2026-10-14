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
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('keluarga_id')
                ->constrained('keluargas')
                ->cascadeOnDelete();

            $table->string('nik', 16)->unique();
            $table->string('nama', 100);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['laki_laki', 'perempuan']);
            $table->enum('agama', ['islam', 'katolik', 'protestan', 'buddha', 'hindu', 'konghucu'])->nullable();

            $table->enum('pendidikan_terakhir', [
                'tidak_belum_sekolah',
                'belum_tamat_sd',
                'sd_sederajat',
                'smp_sederajat',
                'sma_sederajat',
                'd1_d2',
                'd3',
                'd4_s1',
                's2',
                's3',
            ])->nullable();

            $table->string('pekerjaan', 100)->nullable();
            $table->text('alamat')->nullable();

            $table->enum('status_hubungan', [
                'kepala_keluarga',
                'suami',
                'istri',
                'anak',
                'menantu',
                'cucu',
                'orang_tua',
                'mertua',
                'famili_lain',
                'lainnya',
            ]);

            $table->enum('status_warga', ['hidup', 'meninggal', 'pindah_rumah', 'pindah_kk'])
                ->default('hidup');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};
