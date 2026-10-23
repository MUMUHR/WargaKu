<?php

namespace Database\Seeders;

use App\Enums\StatusPublikasi;
use App\Enums\StatusSurat;
use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\Pengumuman;
use App\Models\Riwayat_Perubahan;
use App\Models\Surat;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data realistis untuk pengujian sistem WargaKu RT 04 Kelurahan Belian.
     */
    public function run(): void
    {
        // Bersihkan data lama terlebih dahulu agar `db:seed` aman dijalankan berulang kali
        Schema::disableForeignKeyConstraints();
        Iuran::truncate();
        Riwayat_Perubahan::truncate();
        Surat::truncate();
        Pengumuman::truncate();
        Warga::truncate();
        Keluarga::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

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
        // 2. DAFTAR KELUARGA & WARGA RT 04
        // ═══════════════════════════════════════════
        $dataKeluarga = [
            [
                'no_kk'          => '3271048809920001',
                'status_ekonomi' => 'mampu',
                'status_tinggal' => 'tetap',
                'kepala' => [
                    'nik'                 => '3271048809920001',
                    'nama'                => 'Budi Santoso',
                    'tempat_lahir'        => 'Bandung',
                    'tanggal_lahir'       => '1992-09-08',
                    'jenis_kelamin'       => 'laki_laki',
                    'agama'               => 'islam',
                    'pendidikan_terakhir' => 'd4_s1',
                    'pekerjaan'           => 'Karyawan Swasta',
                    'alamat'              => 'Blok A No. 1, RT 04',
                    'no_telp'             => '081234567891',
                ],
                'anggota' => [
                    [
                        'nik'                 => '3271044502950002',
                        'nama'                => 'Siti Aminah',
                        'tempat_lahir'        => 'Jakarta',
                        'tanggal_lahir'       => '1995-02-05',
                        'jenis_kelamin'       => 'perempuan',
                        'agama'               => 'islam',
                        'pendidikan_terakhir' => 'd4_s1',
                        'pekerjaan'           => 'Ibu Rumah Tangga',
                        'alamat'              => 'Blok A No. 1, RT 04',
                        'status_hubungan'     => 'istri',
                    ],
                    [
                        'nik'                 => '3271041807180003',
                        'nama'                => 'Raka Santoso',
                        'tempat_lahir'        => 'Jakarta',
                        'tanggal_lahir'       => '2018-07-18',
                        'jenis_kelamin'       => 'laki_laki',
                        'agama'               => 'islam',
                        'pendidikan_terakhir' => 'tidak_belum_sekolah',
                        'pekerjaan'           => null,
                        'alamat'              => 'Blok A No. 1, RT 04',
                        'status_hubungan'     => 'anak',
                    ],
                ],
            ],
            [
                'no_kk'          => '3271050106850002',
                'status_ekonomi' => 'kurang_mampu',
                'status_tinggal' => 'kontrak',
                'kepala' => [
                    'nik'                 => '3271050106850002',
                    'nama'                => 'Dewi Rahayu',
                    'tempat_lahir'        => 'Bogor',
                    'tanggal_lahir'       => '1985-06-01',
                    'jenis_kelamin'       => 'perempuan',
                    'agama'               => 'islam',
                    'pendidikan_terakhir' => 'sma_sederajat',
                    'pekerjaan'           => 'Wiraswasta',
                    'alamat'              => 'Blok B No. 5, RT 04',
                    'no_telp'             => '082198765432',
                ],
                'anggota' => [
                    [
                        'nik'                 => '3271052005100001',
                        'nama'                => 'Laila Rahayu',
                        'tempat_lahir'        => 'Jakarta',
                        'tanggal_lahir'       => '2010-05-20',
                        'jenis_kelamin'       => 'perempuan',
                        'agama'               => 'islam',
                        'pendidikan_terakhir' => 'smp_sederajat',
                        'pekerjaan'           => 'Pelajar',
                        'alamat'              => 'Blok B No. 5, RT 04',
                        'status_hubungan'     => 'anak',
                    ],
                ],
            ],
            [
                'no_kk'          => '3276021405820001',
                'status_ekonomi' => 'mampu',
                'status_tinggal' => 'tetap',
                'kepala' => [
                    'nik'                 => '3276021405820001',
                    'nama'                => 'Hendra Setiawan',
                    'tempat_lahir'        => 'Surabaya',
                    'tanggal_lahir'       => '1982-05-14',
                    'jenis_kelamin'       => 'laki_laki',
                    'agama'               => 'islam',
                    'pendidikan_terakhir' => 'd4_s1',
                    'pekerjaan'           => 'Arsitek Konsultan',
                    'alamat'              => 'Blok A No. 4, RT 04',
                    'no_telp'             => '081345678901',
                ],
                'anggota' => [
                    [
                        'nik'                 => '3276025208850002',
                        'nama'                => 'Ratna Sari',
                        'tempat_lahir'        => 'Malang',
                        'tanggal_lahir'       => '1985-08-12',
                        'jenis_kelamin'       => 'perempuan',
                        'agama'               => 'islam',
                        'pendidikan_terakhir' => 'd4_s1',
                        'pekerjaan'           => 'Dokter Gigi',
                        'alamat'              => 'Blok A No. 4, RT 04',
                        'status_hubungan'     => 'istri',
                    ],
                    [
                        'nik'                 => '3276021203120003',
                        'nama'                => 'Dimas Setiawan',
                        'tempat_lahir'        => 'Depok',
                        'tanggal_lahir'       => '2012-03-12',
                        'jenis_kelamin'       => 'laki_laki',
                        'agama'               => 'islam',
                        'pendidikan_terakhir' => 'sd_sederajat',
                        'pekerjaan'           => 'Pelajar',
                        'alamat'              => 'Blok A No. 4, RT 04',
                        'status_hubungan'     => 'anak',
                    ],
                ],
            ],
            [
                'no_kk'          => '3276021204850001',
                'status_ekonomi' => 'mampu',
                'status_tinggal' => 'tetap',
                'kepala' => [
                    'nik'                 => '3276021204850001',
                    'nama'                => 'Agus Supriyanto',
                    
                    'tempat_lahir'        => 'Semarang',
                    'tanggal_lahir'       => '1985-04-12',
                    'jenis_kelamin'       => 'laki_laki',
                    'agama'               => 'islam',
                    'pendidikan_terakhir' => 'd4_s1',
                    'pekerjaan'           => 'Manajer Operasional',
                    'alamat'              => 'Blok C No. 4, RT 04',
                    'no_telp'             => '081356789012',
                ],
                'anggota' => [
                    [
                        'nik'                 => '3276024806880002',
                        'nama'                => 'Endang Lestari',
                        'tempat_lahir'        => 'Solo',
                        'tanggal_lahir'       => '1988-06-08',
                        'jenis_kelamin'       => 'perempuan',
                        'agama'               => 'islam',
                        'pendidikan_terakhir' => 'd4_s1',
                        'pekerjaan'           => 'Guru SMA',
                        'alamat'              => 'Blok C No. 4, RT 04',
                        'status_hubungan'     => 'istri',
                    ],
                ],
            ],
            [
                'no_kk'          => '3276021811860004',
                'status_ekonomi' => 'mampu',
                'status_tinggal' => 'tetap',
                'kepala' => [
                    'nik'                 => '3276021811860004',
                    'nama'                => 'Ahmad Fauzi',
                    'tempat_lahir'        => 'Cirebon',
                    'tanggal_lahir'       => '1986-11-18',
                    'jenis_kelamin'       => 'laki_laki',
                    'agama'               => 'islam',
                    'pendidikan_terakhir' => 'd4_s1',
                    'pekerjaan'           => 'Pegawai BUMN',
                    'alamat'              => 'Blok A No. 2, RT 04',
                    'no_telp'             => '081367890123',
                ],
                'anggota' => [
                    [
                        'nik'                 => '3276025512890005',
                        'nama'                => 'Nurul Hidayah',
                        'tempat_lahir'        => 'Bandung',
                        'tanggal_lahir'       => '1989-12-15',
                        'jenis_kelamin'       => 'perempuan',
                        'agama'               => 'islam',
                        'pendidikan_terakhir' => 'd4_s1',
                        'pekerjaan'           => 'Dosen',
                        'alamat'              => 'Blok A No. 2, RT 04',
                        'status_hubungan'     => 'istri',
                    ],
                ],
            ],
        ];

        $keluargasCreated = [];
        $wargasCreated = [];

        foreach ($dataKeluarga as $kData) {
            $keluarga = Keluarga::create([
                'no_kk'          => $kData['no_kk'],
                'status_ekonomi' => $kData['status_ekonomi'],
                'status_tinggal' => $kData['status_tinggal'],
            ]);
            // Set tanggal pendaftaran sejak 2025 agar tagihan 12 bulan 2026 ter-cover penuh
            $keluarga->created_at = Carbon::create(2025, 1, 10, 8, 0, 0);
            $keluarga->save();

            // Buat akun user untuk Kepala Keluarga (Username & Default Password = NIK)
            $userKK = User::create([
                'username' => $kData['kepala']['nik'],
                'password' => Hash::make($kData['kepala']['nik']),
                'no_telp'  => $kData['kepala']['no_telp'],
                'role'     => 'warga',
            ]);

            // Buat Warga Kepala Keluarga
            $kepalaWarga = Warga::create([
                'user_id'             => $userKK->id,
                'keluarga_id'         => $keluarga->id,
                'nik'                 => $kData['kepala']['nik'],
                'nama'                => $kData['kepala']['nama'],
                'tempat_lahir'        => $kData['kepala']['tempat_lahir'],
                'tanggal_lahir'       => $kData['kepala']['tanggal_lahir'],
                'jenis_kelamin'       => $kData['kepala']['jenis_kelamin'],
                'agama'               => $kData['kepala']['agama'],
                'pendidikan_terakhir' => $kData['kepala']['pendidikan_terakhir'],
                'pekerjaan'           => $kData['kepala']['pekerjaan'],
                'alamat'              => $kData['kepala']['alamat'],
                'status_hubungan'     => 'kepala_keluarga',
                'status_warga'        => 'hidup',
            ]);

            $wargasCreated[$kData['kepala']['nik']] = $kepalaWarga;

            // Buat Anggota Keluarga jika ada
            foreach ($kData['anggota'] as $aData) {
                $anggotaWarga = Warga::create([
                    'user_id'             => $userKK->id,
                    'keluarga_id'         => $keluarga->id,
                    'nik'                 => $aData['nik'],
                    'nama'                => $aData['nama'],
                    'tempat_lahir'        => $aData['tempat_lahir'],
                    'tanggal_lahir'       => $aData['tanggal_lahir'],
                    'jenis_kelamin'       => $aData['jenis_kelamin'],
                    'agama'               => $aData['agama'],
                    'pendidikan_terakhir' => $aData['pendidikan_terakhir'],
                    'pekerjaan'           => $aData['pekerjaan'],
                    'alamat'              => $aData['alamat'],
                    'status_hubungan'     => $aData['status_hubungan'],
                    'status_warga'        => 'hidup',
                ]);
                $wargasCreated[$aData['nik']] = $anggotaWarga;
            }

            $keluargasCreated[$kData['no_kk']] = $keluarga;
        }

        // ═══════════════════════════════════════════
        // 3. SEED DATA REAL IURAN TAHUN 2026 (BR-06)
        // ═══════════════════════════════════════════
        $tahun = 2026;
        // Tarif standar iuran bulanan
        $tarif = config('wargaku.nominal_iuran_awal', 75000);

        $catatanLunasList = [
            'Diterima tunai di Pos Ronda RT 04.',
            'Transfer via rekening Kas Paguyuban RT.',
            'Pembayaran lunas diserahkan ke Bendahara RT.',
            'Diterima tunai langsung oleh Ketua RT.',
            'Pembayaran iuran rutin bulanan warga.',
        ];

        foreach ($keluargasCreated as $noKk => $keluarga) {
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $isLunas = false;
                $tanggalBayar = null;
                $catatan = null;

                if ($bulan < 10) {
                    // Bulan 1 s.d. 9 (Januari s.d. September 2026): Lunas
                    $isLunas = true;
                    $tgl = rand(2, 9);
                    $jam = rand(8, 17);
                    $menit = rand(10, 50);
                    $tanggalBayar = Carbon::create($tahun, $bulan, $tgl, $jam, $menit, 0);
                    $catatan = $catatanLunasList[array_rand($catatanLunasList)];
                } else {
                    // Bulan 10 (Oktober 2026 - Bulan Berjalan) s.d. Bulan 12: Belum Lunas
                    $isLunas = false;
                    $catatan = 'Iuran kebersihan sampah, keamanan 24 jam, & kas sosial RT 04.';
                }

                Iuran::create([
                    'keluarga_id'   => $keluarga->id,
                    'nominal'       => $tarif,
                    'catatan'       => $catatan,
                    'bulan'         => $bulan,
                    'tahun'         => $tahun,
                    'status'        => $isLunas ? 'lunas' : 'belum_lunas',
                    'tanggal_bayar' => $tanggalBayar,
                ]);
            }
        }

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
            'judul'            => 'Jadwal Fogging Nyamuk DBD Wilayah RT 04',
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
        $wargaBudi = $wargasCreated['3271048809920001'];
        $wargaSiti = $wargasCreated['3271044502950002'];
        $wargaDewi = $wargasCreated['3271050106850002'];
        $wargaLaila = $wargasCreated['3271052005100001'];

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
            'keperluan'       => 'Pengantar pembuatan Kartu Identitas Anak (KIA) ke kantor Kelurahan Belian',
            'nomor_surat'     => null,
            'status'          => StatusSurat::MENUNGGU,
            'alasan'          => null,
            'created_at'      => now()->subHours(5),
        ]);

        Surat::create([
            'pemohon_id'      => $wargaBudi->id,
            'warga_tujuan_id' => $wargaBudi->id,
            'keperluan'       => 'Surat pengantar permohonan Surat Keterangan Usaha (SKU) toko kelontong di Jl. Melati Blok A',
            'nomor_surat'     => '001/SP/RT04/II/2026',
            'status'          => StatusSurat::DISETUJUI,
            'alasan'          => null,
            'created_at'      => now()->subDays(2),
        ]);

        Surat::create([
            'pemohon_id'      => $wargaBudi->id,
            'warga_tujuan_id' => $wargaBudi->id,
            'keperluan'       => 'Pengantar permohonan penerbitan paspor untuk perjalanan ibadah umroh',
            'nomor_surat'     => null,
            'status'          => StatusSurat::DITOLAK,
            'alasan'          => 'Dokumen identitas pendukung belum lengkap dan belum melampirkan bukti lunas iuran RT bulan berjalan.',
            'created_at'      => now()->subDays(4),
        ]);

        Surat::create([
            'pemohon_id'      => $wargaDewi->id,
            'warga_tujuan_id' => $wargaDewi->id,
            'keperluan'       => 'Pengantar izin keramaian hajatan syukuran keluarga besar di jalan lingkungan RT',
            'nomor_surat'     => null,
            'status'          => StatusSurat::DITOLAK,
            'alasan'          => 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri.',
            'created_at'      => now()->subDays(5),
        ]);

        // ═══════════════════════════════════════════
        // 6. RIWAYAT PENGAJUAN PERUBAHAN DATA WARGA (UC-11, F008, BR-08)
        // ═══════════════════════════════════════════
        $keluargaBudi = $keluargasCreated['3271048809920001'];
        $wargaRaka = $wargasCreated['3271041807180003'];

        Riwayat_Perubahan::create([
            'keluarga_id'     => $keluargaBudi->id,
            'warga_id'        => $wargaRaka->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama'       => [
                'pendidikan_terakhir' => 'tidak_belum_sekolah',
                'pekerjaan'           => null,
            ],
            'data_baru'       => [
                'pendidikan_terakhir' => 'sd_sederajat',
                'pekerjaan'           => 'Pelajar',
            ],
            'bukti_path'      => 'contoh_Ijazah.png',
            'status'          => 'menunggu',
            'alasan'          => 'Raka telah memasuki usia sekolah dasar dan resmi terdaftar di SDN 01 Batam Kota.',
            'admin_id'        => null,
            'created_at'      => now()->subDays(2),
        ]);

        Riwayat_Perubahan::create([
            'keluarga_id'     => $keluargaBudi->id,
            'warga_id'        => $wargaSiti->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama'       => [
                'pekerjaan' => 'Ibu Rumah Tangga',
            ],
            'data_baru'       => [
                'pekerjaan' => 'Wiraswasta',
            ],
            'bukti_path'      => 'contoh_buku_nikah.png',
            'status'          => 'disetujui',
            'alasan'          => 'Pembaruan data profesi karena membuka toko perlengkapan busana mandiri.',
            'admin_id'        => $adminUser->id,
            'created_at'      => now()->subMonths(2),
        ]);
    }
}
