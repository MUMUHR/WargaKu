<?php

namespace App\Data;

/**
 * Data dummy untuk tahap Frontend WargaKu.
 * Bentuk array mengikuti kolom tabel DB_WAR.dbml persis.
 * Hapus file ini pada tahap Backend.
 */
class DummyData
{
    public static function pengumuman(): array
    {
        return [
            [
                'id'               => 1,
                'judul'            => 'Kerja Bakti Lingkungan Serentak & PSN Menjelang Musim Hujan',
                'sub'              => 'Target: Seluruh Kepala Keluarga RT 04 / RW 08 • Fokus saluran air & selokan',
                'isi'              => "Target: Seluruh Kepala Keluarga RT 04 / RW 08 • Fokus saluran air & selokan\n\nKegiatan gotong royong membersihkan saluran air, selokan, dan pencegahan sarang nyamuk jelang musim penghujan di seluruh gang RT 04. Seluruh warga diharapkan hadir pada hari Minggu pukul 07.00 WIB. Harap membawa peralatan kebersihan masing-masing seperti sapu, cangkul, dan kantong sampah.",
                'gambar'           => 'images/pengumuman/kerjabakti.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-05-16 08:00:00',
            ],
            [
                'id'               => 2,
                'judul'            => 'Jadwal Fogging Nyamuk DBD Wilayah RT 04 / RW 08',
                'sub'              => 'Kerjasama Puskesmas Kelurahan & Kader Jumantik • Harap tutup makanan',
                'isi'              => "Kerjasama Puskesmas Kelurahan & Kader Jumantik • Harap tutup makanan\n\nPenyemprotan disinfeksi dan pengasapan fogging DBD terjadwal dari Puskesmas. Mohon warga menutup makanan dan mengamankan hewan peliharaan. Fogging dilaksanakan pada hari kerja pukul 09.00-12.00 WIB.",
                'gambar'           => 'images/pengumuman/fogging.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-05-12 07:30:00',
            ],
            [
                'id'               => 3,
                'judul'            => 'Draf Tata Tertib Parkir Kendaraan & Pos Ronda Malam',
                'sub'              => 'Menunggu review bersama warga saat rapat bulanan • Portal jam 23.00',
                'isi'              => "Menunggu review bersama warga saat rapat bulanan • Portal jam 23.00\n\nRancangan aturan bersama jam malam portal gang, penitipan kendaraan tamu, serta sistem jadwal ronda warga untuk disetujui bersama. Draf ini masih dalam tahap pembahasan dan belum berlaku efektif.",
                'gambar'           => 'images/pengumuman/posronda.jpg',
                'status_publikasi' => 'draft',
                'admin_id'         => 1,
                'created_at'       => '2025-05-10 10:00:00',
            ],
            [
                'id'               => 4,
                'judul'            => 'Edaran Pembayaran Iuran Kebersihan & Kas RT Periode Mei 2025',
                'sub'              => 'Melalui transfer QRIS RT atau tunai ke koordinator gang masing-masing',
                'isi'              => "Melalui transfer QRIS RT atau tunai ke koordinator gang masing-masing\n\nPemberitahuan penarikan iuran rutin warga untuk kebersihan lingkungan, sampah, dan uang kas RT periode Mei 2025. Pembayaran dapat disalurkan sebelum tanggal 10 setiap bulannya.",
                'gambar'           => 'images/pengumuman/sembako.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-05-01 08:00:00',
            ],
            [
                'id'               => 5,
                'judul'            => 'Undangan Pertemuan Rutin Warga & Rembuk Rencana Paving Jalan',
                'sub'              => 'Tempat: Balai Warga RT 04 / RW 08 pukul 19.30 WIB (Snack & Kopi disediakan)',
                'isi'              => "Tempat: Balai Warga RT 04 / RW 08 pukul 19.30 WIB (Snack & Kopi disediakan)\n\nUndangan rapat musyawarah warga RT 04 mengenai usulan perbaikan dan pavingisasi jalan gang bersama seluruh perwakilan kepala keluarga.",
                'gambar'           => 'images/pengumuman/kerjabakti.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-04-24 19:30:00',
            ],
            [
                'id'               => 6,
                'judul'            => 'Persiapan Lomba Semarak Kemerdekaan 17 Agustus 2025',
                'sub'              => 'Rencana anggaran & kepanitiaan bersama Karang Taruna dan pemudi RT',
                'isi'              => "Rencana anggaran & kepanitiaan bersama Karang Taruna dan pemudi RT\n\nDiskusi pembentukan panitia pelaksana peringatan HUT RI ke-80, susunan cabang perlombaan anak-anak dan dewasa serta estimasi penggalangan dana.",
                'gambar'           => 'images/pengumuman/lomba17.jpg',
                'status_publikasi' => 'draft',
                'admin_id'         => 1,
                'created_at'       => '2025-04-18 16:00:00',
            ],
            [
                'id'               => 7,
                'judul'            => 'Penyaluran Paket Sembako Warga Lansia & Pra-Sejahtera',
                'sub'              => 'Distribusi di Posko Sekretariat RT 04 mulai pukul 08.00 WIB',
                'isi'              => "Distribusi di Posko Sekretariat RT 04 mulai pukul 08.00 WIB\n\nPenyaluran bantuan beras, minyak goreng, dan gula bagi warga lansia serta keluarga pra-sejahtera terdaftar.",
                'gambar'           => 'images/pengumuman/sembako.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-04-10 08:30:00',
            ],
            [
                'id'               => 8,
                'judul'            => 'Layanan Posyandu Balita & Imunisasi Rutin Bulan April 2025',
                'sub'              => 'Pemeriksaan berkala balita & konsultasi gizi di Balai RW 08',
                'isi'              => "Pemeriksaan berkala balita & konsultasi gizi di Balai RW 08\n\nJadwal posyandu balita bulanan meliputi penimbangan berat badan, imunisasi wajib, dan pemberian vitamin.",
                'gambar'           => 'images/pengumuman/kerjabakti.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-04-05 09:00:00',
            ],
            [
                'id'               => 9,
                'judul'            => 'Draf Pengadaan Lampu Penerangan Jalan Gang RT 04',
                'sub'              => 'Survei titik pemasangan tiang lampu LED & estimasi biaya swadaya',
                'isi'              => "Survei titik pemasangan tiang lampu LED & estimasi biaya swadaya\n\nDraf usulan penambahan titik penerangan jalan gang demi keamanan dan kenyamanan lingkungan warga.",
                'gambar'           => 'images/pengumuman/posronda.jpg',
                'status_publikasi' => 'draft',
                'admin_id'         => 1,
                'created_at'       => '2025-03-28 14:00:00',
            ],
            [
                'id'               => 10,
                'judul'            => 'Sosialisasi Pencegahan Kebakaran & Penggunaan APAR Rumah Tangga',
                'sub'              => 'Bekerjasama dengan Dinas Pemadam Kebakaran Kota',
                'isi'              => "Bekerjasama dengan Dinas Pemadam Kebakaran Kota\n\nSimulasi penanganan kebocoran gas LPG dan penggunaan alat pemadam api ringan bagi warga RT 04.",
                'gambar'           => 'images/pengumuman/fogging.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-03-20 10:00:00',
            ],
            [
                'id'               => 11,
                'judul'            => 'Pemasangan CCTV Keamanan di 4 Sudut Strategis Wilayah RT 04',
                'sub'              => 'Integrasi monitor pengawasan langsung di Pos Ronda Malam',
                'isi'              => "Integrasi monitor pengawasan langsung di Pos Ronda Malam\n\nPengumuman realisasi pemasangan kamera CCTV untuk memantau keamanan akses keluar masuk gang 24 jam.",
                'gambar'           => 'images/pengumuman/posronda.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-03-12 16:30:00',
            ],
            [
                'id'               => 12,
                'judul'            => 'Rekapitulasi Laporan Keuangan Kas & Iuran Warga Triwulan I 2025',
                'sub'              => 'Transparansi pembukuan kas RT periode Januari – Maret 2025',
                'isi'              => "Transparansi pembukuan kas RT periode Januari – Maret 2025\n\nLaporan pertanggungjawaban penerimaan dan pengeluaran dana kas RT periode triwulan pertama tahun 2025.",
                'gambar'           => 'images/pengumuman/sembako.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2025-03-01 08:00:00',
            ],
        ];
    }

    public static function pengumumanPublish(): array
    {
        return array_values(array_filter(
            self::pengumuman(),
            fn($p) => $p['status_publikasi'] === 'publish'
        ));
    }

    public static function findPengumuman(int $id): ?array
    {
        foreach (self::pengumuman() as $p) {
            if ($p['id'] === $id) return $p;
        }
        return null;
    }

    public static function formatTanggal(string $date, bool $singkat = false): string
    {
        $bulanPanjang = ['','Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];
        $bulanPendek = ['','Jan','Feb','Mar','Apr','Mei','Jun',
                  'Jul','Agu','Sep','Okt','Nov','Des'];
        $ts = strtotime($date);
        $b = $singkat ? $bulanPendek[(int)date('n', $ts)] : $bulanPanjang[(int)date('n', $ts)];
        $d = $singkat ? date('d', $ts) : date('j', $ts);
        return $d . ' ' . $b . ' ' . date('Y', $ts);
    }

    public static function namaBulan(int $bulan): string
    {
        return ['','Januari','Februari','Maret','April','Mei','Juni',
                'Juli','Agustus','September','Oktober','November','Desember'][$bulan];
    }

    public static function keluarga(): array
    {
        return [
            ['no_kk'=>'3273010908840001','status_ekonomi'=>'mampu','status_tinggal'=>'tetap','created_at'=>'2024-01-15'],
            ['no_kk'=>'3273010908840002','status_ekonomi'=>'kurang_mampu','status_tinggal'=>'kontrak','created_at'=>'2024-03-01'],
            ['no_kk'=>'3273010908840003','status_ekonomi'=>'mampu','status_tinggal'=>'tetap','created_at'=>'2025-06-10'],
            ['no_kk'=>'3273010908840004','status_ekonomi'=>'kurang_mampu','status_tinggal'=>'kos','created_at'=>'2025-09-20'],
            ['no_kk'=>'3273010908840005','status_ekonomi'=>'mampu','status_tinggal'=>'menumpang','created_at'=>'2026-04-01'],
            ['no_kk'=>'3273010908840006','status_ekonomi'=>'mampu','status_tinggal'=>'tetap','created_at'=>'2026-07-15'],
        ];
    }

    public static function warga(): array
    {
        return [
            ['id'=>1,'user_id'=>2,'no_kk'=>'3273010908840001','nik'=>'3273010908840011','nama'=>'Bambang Pamungkas','tempat_lahir'=>'Batam','tanggal_lahir'=>'1975-08-09','jenis_kelamin'=>'laki_laki','agama'=>'islam','pendidikan_terakhir'=>'d4_s1','pekerjaan'=>'Wiraswasta','alamat'=>'Blok B No. 14, RT 04 / RW 08','status_hubungan'=>'kepala_keluarga','status_warga'=>'hidup'],
            ['id'=>2,'user_id'=>2,'no_kk'=>'3273010908840001','nik'=>'3273010908840012','nama'=>'Siti Rahayu','tempat_lahir'=>'Batam','tanggal_lahir'=>'1978-03-15','jenis_kelamin'=>'perempuan','agama'=>'islam','pendidikan_terakhir'=>'sma_sederajat','pekerjaan'=>'Ibu Rumah Tangga','alamat'=>'Blok B No. 14, RT 04 / RW 08','status_hubungan'=>'istri','status_warga'=>'hidup'],
            ['id'=>3,'user_id'=>2,'no_kk'=>'3273010908840001','nik'=>'3273010908840013','nama'=>'Rina Pamungkas','tempat_lahir'=>'Batam','tanggal_lahir'=>'2002-11-20','jenis_kelamin'=>'perempuan','agama'=>'islam','pendidikan_terakhir'=>'d4_s1','pekerjaan'=>'Mahasiswa','alamat'=>'Blok B No. 14, RT 04 / RW 08','status_hubungan'=>'anak','status_warga'=>'hidup'],
            ['id'=>4,'user_id'=>null,'no_kk'=>'3273010908840001','nik'=>'3273010908840014','nama'=>'Budi Pamungkas','tempat_lahir'=>'Batam','tanggal_lahir'=>'1945-02-10','jenis_kelamin'=>'laki_laki','agama'=>'islam','pendidikan_terakhir'=>'sd_sederajat','pekerjaan'=>null,'alamat'=>'Blok B No. 14, RT 04 / RW 08','status_hubungan'=>'orang_tua','status_warga'=>'meninggal'],
            ['id'=>5,'user_id'=>3,'no_kk'=>'3273010908840002','nik'=>'3273010908840021','nama'=>'Ahmad Fauzi','tempat_lahir'=>'Tanjungpinang','tanggal_lahir'=>'1980-06-25','jenis_kelamin'=>'laki_laki','agama'=>'islam','pendidikan_terakhir'=>'smp_sederajat','pekerjaan'=>'Buruh Harian','alamat'=>'Gang Mawar No. 3, RT 04 / RW 08','status_hubungan'=>'kepala_keluarga','status_warga'=>'hidup'],
        ];
    }

    public static function pengajuanSurat(): array
    {
        return [
            ['id'=>1,'pemohon_id'=>1,'warga_tujuan_id'=>3,'nama_lengkap'=>'Rina Pamungkas','keperluan'=>'Melamar Pekerjaan BUMN','nomor_surat'=>'002/SP/RT04/RW08/V/2026','status'=>'disetujui','alasan'=>null,'admin_id'=>1,'created_at'=>'2026-05-14 10:00:00'],
            ['id'=>2,'pemohon_id'=>1,'warga_tujuan_id'=>2,'nama_lengkap'=>'Siti Rahayu','keperluan'=>'Pembukaan Rekening Giro Bank','nomor_surat'=>'001/SP/RT04/RW08/IV/2026','status'=>'disetujui','alasan'=>null,'admin_id'=>1,'created_at'=>'2026-04-28 09:00:00'],
            ['id'=>3,'pemohon_id'=>1,'warga_tujuan_id'=>1,'nama_lengkap'=>'Bambang Pamungkas','keperluan'=>'Penggantian KTP Rusak','nomor_surat'=>null,'status'=>'menunggu','alasan'=>null,'admin_id'=>null,'created_at'=>'2026-05-20 11:00:00'],
            ['id'=>4,'pemohon_id'=>1,'warga_tujuan_id'=>1,'nama_lengkap'=>'Bambang Pamungkas','keperluan'=>'SKCK untuk melamar kerja','nomor_surat'=>null,'status'=>'ditolak','alasan'=>'Data identitas tidak lengkap, harap lengkapi data pekerjaan terlebih dahulu.','admin_id'=>1,'created_at'=>'2026-03-10 08:00:00'],
        ];
    }

    public static function iuran(): array
    {
        $data = [];
        $tahun = 2026;
        $no_kk = '3273010908840001';
        for ($b = 1; $b <= 4; $b++) {
            $bPad = str_pad($b, 2, '0', STR_PAD_LEFT);
            $data[] = ['id'=>$b,'no_kk'=>$no_kk,'nominal'=>75000,'catatan'=>null,'bulan'=>$b,'tahun'=>$tahun,'status'=>'lunas','tanggal_bayar'=>"2026-{$bPad}-05 10:00:00"];
        }
        $data[] = ['id'=>5,'no_kk'=>$no_kk,'nominal'=>75000,'catatan'=>null,'bulan'=>5,'tahun'=>$tahun,'status'=>'belum_lunas','tanggal_bayar'=>null];
        $data[] = ['id'=>6,'no_kk'=>$no_kk,'nominal'=>75000,'catatan'=>'Bayar di muka','bulan'=>6,'tahun'=>$tahun,'status'=>'lunas','tanggal_bayar'=>'2026-05-20 09:00:00'];
        for ($b = 7; $b <= 12; $b++) {
            $data[] = ['id'=>$b,'no_kk'=>$no_kk,'nominal'=>75000,'catatan'=>null,'bulan'=>$b,'tahun'=>$tahun,'status'=>'belum_lunas','tanggal_bayar'=>null];
        }
        $no_kk2 = '3273010908840006';
        for ($b = 7; $b <= 12; $b++) {
            $data[] = ['id'=>20+$b,'no_kk'=>$no_kk2,'nominal'=>75000,'catatan'=>null,'bulan'=>$b,'tahun'=>$tahun,'status'=>'belum_lunas','tanggal_bayar'=>null];
        }
        return $data;
    }

    public static function pengajuanPerubahanData(): array
    {
        return [
            ['id'=>1,'warga_id'=>3,'no_kk'=>'3273010908840001','jenis_pengajuan'=>'ubah_data','data_lama'=>'{"pekerjaan":"Mahasiswa"}','data_baru'=>'{"pekerjaan":"Pegawai Swasta"}','bukti_path'=>'dummy/bukti1.jpg','status'=>'menunggu','alasan'=>null,'admin_id'=>null,'created_at'=>'2026-05-22 09:00:00'],
            ['id'=>2,'warga_id'=>2,'no_kk'=>'3273010908840001','jenis_pengajuan'=>'ubah_data','data_lama'=>'{"status_warga":"hidup"}','data_baru'=>'{"status_warga":"pindah_kk"}','bukti_path'=>'dummy/bukti2.jpg','status'=>'disetujui','alasan'=>null,'admin_id'=>1,'created_at'=>'2026-04-10 08:00:00'],
            ['id'=>3,'warga_id'=>1,'no_kk'=>'3273010908840001','jenis_pengajuan'=>'ubah_data','data_lama'=>'{"alamat":"Blok A"}','data_baru'=>'{"alamat":"Blok B No. 14"}','bukti_path'=>'dummy/bukti3.jpg','status'=>'ditolak','alasan'=>'Bukti dokumen tidak terbaca dengan jelas.','admin_id'=>1,'created_at'=>'2026-03-15 10:00:00'],
            ['id'=>4,'warga_id'=>null,'no_kk'=>'3273010908840001','jenis_pengajuan'=>'tambah_anggota','data_lama'=>null,'data_baru'=>'{"nik":"3273010908840099","nama":"Bayi Pamungkas"}','bukti_path'=>'dummy/bukti4.jpg','status'=>'dibatalkan','alasan'=>null,'admin_id'=>null,'created_at'=>'2026-02-01 11:00:00'],
        ];
    }

    public static function listKartuKeluarga(): array
    {
        return [
            [
                'no_kk' => '3275010905120008',
                'nama_kepala' => 'Bambang Santoso, S.T.',
                'nik_kepala' => '3275012304800001',
                'alamat' => 'Blok B4 No. 12, RT 04 / RW 08',
                'blok' => 'Blok B',
                'jumlah_anggota' => 3,
                'laki_laki' => 1,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Kurang Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0812-8901-2345',
            ],
            [
                'no_kk' => '3275012010150012',
                'nama_kepala' => 'Hendro Gunawan',
                'nik_kepala' => '3275011005820002',
                'alamat' => 'Blok A1 No. 05, RT 04 / RW 08',
                'blok' => 'Blok A',
                'jumlah_anggota' => 4,
                'laki_laki' => 2,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0813-5678-9012',
            ],
            [
                'no_kk' => '3275011802110003',
                'nama_kepala' => 'Achmad Fauzi, S.Pd.',
                'nik_kepala' => '3275011802790003',
                'alamat' => 'Blok C2 No. 08, RT 04 / RW 08',
                'blok' => 'Blok C',
                'jumlah_anggota' => 5,
                'laki_laki' => 2,
                'perempuan' => 3,
                'status_tinggal' => 'Kontrak / Sewa',
                'status_tinggal_badge' => 'Kontrak / Sewa',
                'status_ekonomi' => 'Kurang Mampu',
                'ekonomi_kategori' => 'Kurang Mampu',
                'kontak' => '0856-7788-9901',
            ],
            [
                'no_kk' => '3275012408190021',
                'nama_kepala' => 'Ibu Sumarti',
                'nik_kepala' => '3275016408650005',
                'alamat' => 'Blok D3 No. 02, RT 04 / RW 08',
                'blok' => 'Blok D',
                'jumlah_anggota' => 2,
                'laki_laki' => 0,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0878-1122-3344',
            ],
            [
                'no_kk' => '3275010503220035',
                'nama_kepala' => 'Raden Dimas Arya',
                'nik_kepala' => '3275010503900004',
                'alamat' => 'Blok B1 No. 17, RT 04 / RW 08',
                'blok' => 'Blok B',
                'jumlah_anggota' => 3,
                'laki_laki' => 2,
                'perempuan' => 1,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0811-2233-4455',
            ],
            [
                'no_kk' => '3275011704150041',
                'nama_kepala' => 'Faisal Akbar',
                'nik_kepala' => '3275012204840003',
                'alamat' => 'Blok B1 No. 09, RT 04 / RW 08',
                'blok' => 'Blok B',
                'jumlah_anggota' => 4,
                'laki_laki' => 2,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0812-3344-5566',
            ],
            [
                'no_kk' => '3275010809180052',
                'nama_kepala' => 'Nurul Huda',
                'nik_kepala' => '3275011109780005',
                'alamat' => 'Blok C4 No. 02, RT 04 / RW 08',
                'blok' => 'Blok C',
                'jumlah_anggota' => 5,
                'laki_laki' => 3,
                'perempuan' => 2,
                'status_tinggal' => 'Kontrak / Sewa',
                'status_tinggal_badge' => 'Kontrak / Sewa',
                'status_ekonomi' => 'Kurang Mampu',
                'ekonomi_kategori' => 'Kurang Mampu',
                'kontak' => '0813-7788-9900',
            ],
            [
                'no_kk' => '3275012912190063',
                'nama_kepala' => 'Dian Permata',
                'nik_kepala' => '3275011812890014',
                'alamat' => 'Blok A2 No. 07, RT 04 / RW 08',
                'blok' => 'Blok A',
                'jumlah_anggota' => 3,
                'laki_laki' => 1,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0857-1122-3344',
            ],
            [
                'no_kk' => '3275010301200074',
                'nama_kepala' => 'Wahyu Hidayat',
                'nik_kepala' => '3275010501830022',
                'alamat' => 'Blok D1 No. 05, RT 04 / RW 08',
                'blok' => 'Blok D',
                'jumlah_anggota' => 4,
                'laki_laki' => 2,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Kurang Mampu',
                'ekonomi_kategori' => 'Kurang Mampu',
                'kontak' => '0877-6655-4433',
            ],
            [
                'no_kk' => '3275011506210085',
                'nama_kepala' => 'Surya Darmawan',
                'nik_kepala' => '3275020703810017',
                'alamat' => 'Blok C2 No. 07, RT 04 / RW 08',
                'blok' => 'Blok C',
                'jumlah_anggota' => 4,
                'laki_laki' => 2,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0819-8877-6655',
            ],
        ];
    }

    public static function anggotaKeluarga(string $noKk): array
    {
        return [
            [
                'no' => 1,
                'nama' => 'Bambang Santoso, S.T.',
                'nik' => '3275012304800001',
                'hubungan' => 'Kepala Keluarga',
                'hubungan_badge' => 'primary',
                'jk' => 'Laki-laki',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'D4/S1',
                'pekerjaan' => 'Pegawai BUMN (PT PLN)',
                'status_warga' => 'Hidup (Aktif)',
            ],
            [
                'no' => 2,
                'nama' => 'Ratna Dewi Puspita, M.Pd.',
                'nik' => '3275016508910004',
                'hubungan' => 'Istri',
                'hubungan_badge' => 'teal',
                'jk' => 'Perempuan',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'S2',
                'pekerjaan' => 'Guru SMA Negeri 4',
                'status_warga' => 'Hidup (Aktif)',
            ],
            [
                'no' => 3,
                'nama' => 'Siti Rahmawati, S.Ak.',
                'nik' => '3275015509010003',
                'hubungan' => 'Anak (ke-1)',
                'hubungan_badge' => 'secondary',
                'jk' => 'Perempuan',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'D4/S1',
                'pekerjaan' => 'Karyawan Swasta (Auditor)',
                'status_warga' => 'Hidup (Aktif)',
            ],
        ];
    }

    public static function listVerifikasiPengajuan(): array
    {
        return [
            [
                'id' => 1,
                'pemohon' => 'Bambang Santoso',
                'no_kk' => '3275010905120008',
                'waktu' => '18 Feb 2025 • 09:15 WIB',
                'aksi_warga' => 'Ubah Data',
                'aksi_badge' => 'info',
                'warga_terdampak' => 'Siti Rahmawati',
                'bukti' => '2 Berkas',
                'bukti_icon' => 'bx bxs-file-pdf',
                'bukti_color' => '#dc3545',
                'status_validasi' => 'Menunggu Verifikasi RT',
            ],
            [
                'id' => 2,
                'pemohon' => 'Dedi Kusnadi',
                'no_kk' => '3275011406180002',
                'waktu' => '17 Feb 2025 • 14:20 WIB',
                'aksi_warga' => 'Tambah Anggota',
                'aksi_badge' => 'success',
                'warga_terdampak' => 'Ahmad Rayyan Kusnadi',
                'bukti' => 'Surat Lahir RS',
                'bukti_icon' => 'bx bxs-file-doc',
                'bukti_color' => '#20c997',
                'status_validasi' => 'Menunggu Verifikasi RT',
            ],
            [
                'id' => 3,
                'pemohon' => 'Agus Wicaksono',
                'no_kk' => '3275010203100010',
                'waktu' => '15 Feb 2025 • 11:04 WIB',
                'aksi_warga' => 'Pindah',
                'aksi_badge' => 'warning',
                'warga_terdampak' => 'Agus Wicaksono',
                'bukti' => 'SKPWNI Kelurahan',
                'bukti_icon' => 'bx bxs-file-pdf',
                'bukti_color' => '#dc3545',
                'status_validasi' => 'Menunggu Verifikasi RT',
            ],
        ];
    }

    public static function listPengajuanSuratAdmin(): array
    {
        return [
            [
                'id' => 1,
                'tanggal' => '14 Feb 2026',
                'jam' => '09:30 WIB',
                'pemohon_nama' => 'Bambang Santoso, S.T.',
                'pemohon_nik' => '3275012304780001',
                'pemohon_alamat' => 'Blok B4 No. 12',
                'warga_tujuan' => 'Siti Rahmawati, S.Ak.',
                'keperluan' => 'Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan rekrutmen BUMN',
                'nomor_surat' => null,
                'status' => 'Menunggu',
            ],
            [
                'id' => 2,
                'tanggal' => '14 Feb 2026',
                'jam' => '08:15 WIB',
                'pemohon_nama' => 'Hendra Wijaya',
                'pemohon_nik' => '3275011906850004',
                'pemohon_alamat' => 'Blok A2 No. 05',
                'warga_tujuan' => 'Hendra Wijaya',
                'keperluan' => 'Surat pengantar keterangan domisili tempat tinggal sementara untuk keperluan perbankan dan pembukaan rekening usaha',
                'nomor_surat' => null,
                'status' => 'Menunggu',
            ],
            [
                'id' => 3,
                'tanggal' => '12 Feb 2026',
                'jam' => '14:10 WIB',
                'pemohon_nama' => 'Agus Supriyanto',
                'pemohon_nik' => '3275010302790002',
                'pemohon_alamat' => 'Blok C1 No. 08',
                'warga_tujuan' => 'Dimas Bagus Saputra',
                'keperluan' => 'Pengantar pembuatan Kartu Tanda Penduduk (KTP-el) pemula ke kantor Kelurahan',
                'nomor_surat' => '04/SP/RT04/II/2026',
                'status' => 'Disetujui',
            ],
            [
                'id' => 4,
                'tanggal' => '10 Feb 2026',
                'jam' => '11:20 WIB',
                'pemohon_nama' => 'Ratna Juwita',
                'pemohon_nik' => '3275014611880005',
                'pemohon_alamat' => 'Blok B2 No. 23',
                'warga_tujuan' => 'Ratna Juwita',
                'keperluan' => 'Pengantar surat keterangan belum memiliki rumah untuk pengajuan KPR Subsidi bank BTN',
                'nomor_surat' => '03/SP/RT04/II/2026',
                'status' => 'Disetujui',
            ],
            [
                'id' => 5,
                'tanggal' => '08 Feb 2026',
                'jam' => '16:45 WIB',
                'pemohon_nama' => 'Dedi Suryana',
                'pemohon_nik' => '3275012507810009',
                'pemohon_alamat' => 'Blok D3 No. 02',
                'warga_tujuan' => 'Dedi Suryana',
                'keperluan' => 'Pengantar izin keramaian hajatan pernikahan keluarga besar',
                'nomor_surat' => 'Dibatalkan',
                'status' => 'Ditolak',
            ],
            [
                'id' => 6,
                'tanggal' => '05 Feb 2026',
                'jam' => '10:00 WIB',
                'pemohon_nama' => 'Faisal Akbar',
                'pemohon_nik' => '3275012204840003',
                'pemohon_alamat' => 'Blok B1 No. 09',
                'warga_tujuan' => 'Faisal Akbar',
                'keperluan' => 'Surat Pengantar Pengurusan Akta Kelahiran Anak ke Dinas Kependudukan & Pencatatan Sipil',
                'nomor_surat' => '02/SP/RT04/II/2026',
                'status' => 'Disetujui',
            ],
            [
                'id' => 7,
                'tanggal' => '03 Feb 2026',
                'jam' => '13:30 WIB',
                'pemohon_nama' => 'Nurul Huda',
                'pemohon_nik' => '3275011109780005',
                'pemohon_alamat' => 'Blok C4 No. 02',
                'warga_tujuan' => 'Nurul Huda',
                'keperluan' => 'Surat Keterangan Kematian dan Pengurusan Santunan Duka',
                'nomor_surat' => '01/SP/RT04/II/2026',
                'status' => 'Disetujui',
            ],
            [
                'id' => 8,
                'tanggal' => '01 Feb 2026',
                'jam' => '15:15 WIB',
                'pemohon_nama' => 'Dian Permata',
                'pemohon_nik' => '3275011812890014',
                'pemohon_alamat' => 'Blok A2 No. 07',
                'warga_tujuan' => 'Dian Permata',
                'keperluan' => 'Pengantar Permohonan Surat Keterangan Usaha (SKU) Mikro UMKM',
                'nomor_surat' => null,
                'status' => 'Menunggu',
            ],
        ];
    }

    public static function listTunggakanIuranAdmin(): array
    {
        return [
            [
                'id' => 1,
                'no_kk' => '3276021204850001',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Agus Supriyanto',
                'blok' => 'Blok C1 No. 04',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 2,
                'no_kk' => '3276022808920005',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Dewi Anggraeni',
                'blok' => 'Blok C3 No. 01',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 3,
                'no_kk' => '3276020509890014',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Rahmat Hidayat',
                'blok' => 'Blok A1 No. 02',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 4,
                'no_kk' => '3276021102830002',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Bambang Santoso',
                'blok' => 'Blok B4 No. 12',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 5,
                'no_kk' => '3276021908840003',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Bambang Wicaksono',
                'blok' => 'Blok B3 No. 12',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 6,
                'no_kk' => '3276020703810017',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Surya Darmawan',
                'blok' => 'Blok C2 No. 07',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 7,
                'no_kk' => '3276021610750009',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Eko Prasetyo',
                'blok' => 'Blok A3 No. 11',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 8,
                'no_kk' => '3276022204840003',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Faisal Akbar',
                'blok' => 'Blok B1 No. 09',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 9,
                'no_kk' => '3276021109780005',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Nurul Huda',
                'blok' => 'Blok C4 No. 02',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 10,
                'no_kk' => '3276021812890014',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Dian Permata',
                'blok' => 'Blok A2 No. 07',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
            [
                'id' => 11,
                'no_kk' => '3276020501830022',
                'status_ekonomi' => 'EKONOMI: MAMPU',
                'nama_kepala' => 'Wahyu Hidayat',
                'blok' => 'Blok D1 No. 05',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'status' => 'Belum Lunas',
            ],
        ];
    }

    public static function listRiwayatIuranAdmin(): array
    {
        return [
            [
                'id' => 101,
                'no_kk' => '3275012010150012',
                'nama_kepala' => 'Hendro Gunawan',
                'blok' => 'Blok A1 No. 05',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '02 Feb 2026 10:15 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
            [
                'id' => 102,
                'no_kk' => '3275011802110003',
                'nama_kepala' => 'Achmad Fauzi, S.Pd.',
                'blok' => 'Blok C2 No. 01',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '03 Feb 2026 14:20 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
            [
                'id' => 103,
                'no_kk' => '3275012408190021',
                'nama_kepala' => 'Ibu Sumarti',
                'blok' => 'Blok D3 No. 02',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '04 Feb 2026 09:00 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
            [
                'id' => 104,
                'no_kk' => '3275010503220035',
                'nama_kepala' => 'Raden Dimas Arya',
                'blok' => 'Blok B1 No. 17',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '05 Feb 2026 11:30 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
            [
                'id' => 105,
                'no_kk' => '3275010912180044',
                'nama_kepala' => 'Rahmat Kurniawan',
                'blok' => 'Blok B2 No. 08',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '05 Feb 2026 15:45 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
            [
                'id' => 106,
                'no_kk' => '3275011406200055',
                'nama_kepala' => 'Siti Aisyah',
                'blok' => 'Blok C1 No. 12',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '06 Feb 2026 08:30 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
            [
                'id' => 107,
                'no_kk' => '3275012209170066',
                'nama_kepala' => 'Budi Santoso',
                'blok' => 'Blok A4 No. 03',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '06 Feb 2026 13:10 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
            [
                'id' => 108,
                'no_kk' => '3275013001190077',
                'nama_kepala' => 'H. Muhammad Yusuf',
                'blok' => 'Blok D2 No. 15',
                'periode' => 'Feb 2026',
                'nominal' => 'Rp 50.000',
                'tanggal_bayar' => '07 Feb 2026 16:00 WIB',
                'metode' => 'Cash',
                'status' => 'Lunas',
            ],
        ];
    }
}

