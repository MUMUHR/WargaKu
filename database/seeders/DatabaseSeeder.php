<?php

namespace Database\Seeders;

use App\Enums\StatusPublikasi;
use App\Enums\StatusSurat;
use App\Models\Keluarga;
use App\Models\Pengumuman;
use App\Models\Surat;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data dummy untuk testing WargaKu.
     *
     * Akun yang tersedia:
     * ┌─────────────────────┬──────────────────┬────────────────┬────────┐
     * │ Username            │ Password         │ Nama           │ Role   │
     * ├─────────────────────┼──────────────────┼────────────────┼────────┤
     * │ adminrt             │ admin123         │ Admin RT 04    │ admin  │
     * │ 3271048809920001    │ 3271048809920001 │ Budi Santoso   │ warga  │
     * │ 3271050106850002    │ 3271050106850002 │ Dewi Rahayu    │ warga  │
     * └─────────────────────┴──────────────────┴────────────────┴────────┘
     */
    public function run(): void
    {
        // Bersihkan data lama terlebih dahulu agar `db:seed` aman dijalankan berulang kali
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        User::truncate();
        Keluarga::truncate();
        Warga::truncate();
        Pengumuman::truncate();
        Surat::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // ═══════════════════════════════════════════
        // 1. ADMIN RT
        // ═══════════════════════════════════════════
        $adminUser = User::create([
            'username' => 'adminrt',
            'password' => Hash::make('admin123'),
            'no_telp'  => '081234567890',
            'role'     => 'admin',
        ]);

        // ═══════════════════════════════════════════
        // 2. KELUARGA 1 — Budi Santoso (Kepala KK)
        // ═══════════════════════════════════════════
        $keluarga1 = Keluarga::create([
            'no_kk'          => '3271048809920001',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        $userWarga1 = User::create([
            'username' => '3271048809920001',
            'password' => Hash::make('3271048809920001'), // Default password = NIK
            'no_telp'  => '081234567891',
            'role'     => 'warga',
        ]);

        // Kepala Keluarga
        $wargaBudi = Warga::create([
            'user_id'             => $userWarga1->id,
            'keluarga_id'         => $keluarga1->id,
            'nik'                 => '3271048809920001',
            'nama'                => 'Budi Santoso',
            'tempat_lahir'        => 'Bandung',
            'tanggal_lahir'       => '1992-09-08',
            'jenis_kelamin'       => 'laki_laki',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'pekerjaan'           => 'Karyawan Swasta',
            'alamat'              => 'Jl. Melati Blok A No. 1, RT 04/RW 08',
            'status_hubungan'     => 'kepala_keluarga',
            'status_warga'        => 'hidup',
        ]);

        // Anggota KK 1 — Istri
        $wargaSiti = Warga::create([
            'user_id'             => $userWarga1->id,
            'keluarga_id'         => $keluarga1->id,
            'nik'                 => '3271044502950002',
            'nama'                => 'Siti Aminah',
            'tempat_lahir'        => 'Jakarta',
            'tanggal_lahir'       => '1995-02-05',
            'jenis_kelamin'       => 'perempuan',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'pekerjaan'           => 'Ibu Rumah Tangga',
            'alamat'              => 'Jl. Melati Blok A No. 1, RT 04/RW 08',
            'status_hubungan'     => 'istri',
            'status_warga'        => 'hidup',
        ]);

        // Anggota KK 1 — Anak
        Warga::create([
            'user_id'             => $userWarga1->id,
            'keluarga_id'         => $keluarga1->id,
            'nik'                 => '3271041807180003',
            'nama'                => 'Raka Santoso',
            'tempat_lahir'        => 'Jakarta',
            'tanggal_lahir'       => '2018-07-18',
            'jenis_kelamin'       => 'laki_laki',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'tidak_belum_sekolah',
            'pekerjaan'           => null,
            'alamat'              => 'Jl. Melati Blok A No. 1, RT 04/RW 08',
            'status_hubungan'     => 'anak',
            'status_warga'        => 'hidup',
        ]);

        // ═══════════════════════════════════════════
        // 3. KELUARGA 2 — Dewi Rahayu (Kepala KK)
        // ═══════════════════════════════════════════
        $keluarga2 = Keluarga::create([
            'no_kk'          => '3271050106850002',
            'status_ekonomi' => 'kurang_mampu',
            'status_tinggal' => 'kontrak',
        ]);

        $userWarga2 = User::create([
            'username' => '3271050106850002',
            'password' => Hash::make('3271050106850002'), // Default password = NIK
            'no_telp'  => '082198765432',
            'role'     => 'warga',
        ]);

        $wargaDewi = Warga::create([
            'user_id'             => $userWarga2->id,
            'keluarga_id'         => $keluarga2->id,
            'nik'                 => '3271050106850002',
            'nama'                => 'Dewi Rahayu',
            'tempat_lahir'        => 'Bogor',
            'tanggal_lahir'       => '1985-06-01',
            'jenis_kelamin'       => 'perempuan',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'sma_sederajat',
            'pekerjaan'           => 'Wiraswasta',
            'alamat'              => 'Jl. Mawar Blok B No. 5, RT 04/RW 08',
            'status_hubungan'     => 'kepala_keluarga',
            'status_warga'        => 'hidup',
        ]);

        // Anggota KK 2 — Anak
        $wargaLaila = Warga::create([
            'user_id'             => $userWarga2->id,
            'keluarga_id'         => $keluarga2->id,
            'nik'                 => '3271052005100001',
            'nama'                => 'Laila Rahayu',
            'tempat_lahir'        => 'Jakarta',
            'tanggal_lahir'       => '2010-05-20',
            'jenis_kelamin'       => 'perempuan',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'smp_sederajat',
            'pekerjaan'           => 'Pelajar',
            'alamat'              => 'Jl. Mawar Blok B No. 5, RT 04/RW 08',
            'status_hubungan'     => 'anak',
            'status_warga'        => 'hidup',
        ]);

        // ═══════════════════════════════════════════
        // 4. PENGUMUMAN RT
        // ═══════════════════════════════════════════
        Pengumuman::create([
            'judul'            => 'Kerja Bakti Lingkungan Serentak & PSN Menjelang Musim Hujan',
            'isi'              => "Kegiatan gotong royong membersihkan saluran air, selokan, dan pencegahan sarang nyamuk jelang musim penghujan di seluruh gang RT 04.\n\nSeluruh warga diharapkan hadir pada hari Minggu pukul 07.00 WIB berkumpul di Balai Pertemuan RT 04. Harap membawa peralatan kebersihan masing-masing seperti sapu lidi, cangkul, dan kantong sampah.",
            'gambar'           => 'images/pengumuman/kerjabakti.jpg',
            'status_publikasi' => StatusPublikasi::PUBLISH,
            'admin_id'         => $adminUser->id,
            'created_at'       => now()->subDays(2),
        ]);

        Pengumuman::create([
            'judul'            => 'Jadwal Fogging Nyamuk DBD Wilayah RT 04 / RW 08',
            'isi'              => "Penyemprotan disinfeksi dan pengasapan fogging DBD terjadwal dari Puskesmas setempat.\n\nPelaksanaan: Sabtu, pukul 08.30 WIB.\nMohon warga membuka ventilasi, menutup makanan/minuman, serta mengamankan hewan peliharaan masing-masing.",
            'gambar'           => 'images/pengumuman/fogging.jpg',
            'status_publikasi' => StatusPublikasi::PUBLISH,
            'admin_id'         => $adminUser->id,
            'created_at'       => now()->subDays(5),
        ]);

        Pengumuman::create([
            'judul'            => 'Rancangan Anggaran Pembelian Lampu Jalan Gang 3 & 4',
            'isi'              => "Draf rencana pengadaan dan penggantian lampu penerangan jalan gang yang mati di Gang Melati dan Mawar. Akan dibahas lebih lanjut pada musyawarah pengurus RT akhir bulan ini.",
            'gambar'           => null,
            'status_publikasi' => StatusPublikasi::DRAFT,
            'admin_id'         => $adminUser->id,
            'created_at'       => now()->subDay(),
        ]);

        // ═══════════════════════════════════════════
        // 5. PENGAJUAN SURAT PENGANTAR (UC-12, BR-05, BR-07, BR-10)
        // ═══════════════════════════════════════════
        Surat::create([
            'pemohon_id'      => $wargaBudi->id,
            'warga_tujuan_id' => $wargaSiti->id,
            'keperluan'       => 'Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan seleksi instansi BUMN',
            'nomor_surat'     => null,
            'status'          => StatusSurat::MENUNGGU,
            'alasan'          => null,
            'created_at'      => now()->subHours(2),
        ]);

        Surat::create([
            'pemohon_id'      => $wargaDewi->id,
            'warga_tujuan_id' => $wargaLaila->id,
            'keperluan'       => 'Pengantar pembuatan Kartu Identitas Anak (KIA) ke kantor Kelurahan Sukamaju',
            'nomor_surat'     => null,
            'status'          => StatusSurat::MENUNGGU,
            'alasan'          => null,
            'created_at'      => now()->subHours(5),
        ]);

        Surat::create([
            'pemohon_id'      => $wargaBudi->id,
            'warga_tujuan_id' => $wargaBudi->id,
            'keperluan'       => 'Surat pengantar permohonan Surat Keterangan Usaha (SKU) toko kelontong di Jl. Melati Blok A',
            'nomor_surat'     => '001/SP/RT04/RW08/II/2026',
            'status'          => StatusSurat::DISETUJUI,
            'alasan'          => null,
            'created_at'      => now()->subDays(2),
        ]);

        Surat::create([
            'pemohon_id'      => $wargaDewi->id,
            'warga_tujuan_id' => $wargaDewi->id,
            'keperluan'       => 'Pengantar izin keramaian hajatan syukuran keluarga besar di jalan lingkungan RT',
            'nomor_surat'     => null,
            'status'          => StatusSurat::DITOLAK,
            'alasan'          => 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri.',
            'created_at'      => now()->subDays(4),
        ]);
    }
}
