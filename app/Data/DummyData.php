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
                'judul'            => 'Kerja Bakti Lingkungan Serentak & PSN',
                'isi'              => "Kegiatan gotong royong membersihkan saluran air, selokan, dan pencegahan sarang nyamuk jelang musim penghujan di seluruh gang RT 04.\n\nSeluruh warga diharapkan hadir pada hari Minggu pukul 07.00 WIB. Harap membawa peralatan kebersihan masing-masing seperti sapu, cangkul, dan kantong sampah. Konsumsi akan disediakan oleh panitia RT.",
                'gambar'           => 'images/pengumuman/kerjabakti.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2026-05-16 08:00:00',
            ],
            [
                'id'               => 2,
                'judul'            => 'Jadwal Fogging Nyamuk DBD Wilayah RT 04',
                'isi'              => "Penyemprotan disinfeksi dan pengasapan fogging DBD terjadwal dari Puskesmas. Mohon warga menutup makanan dan mengamankan hewan peliharaan.\n\nFogging dilaksanakan pada Rabu, 12 Mei 2026 pukul 09.00-12.00 WIB. Seluruh warga diminta membuka jendela dan pintu rumah agar fogging lebih efektif.",
                'gambar'           => 'images/pengumuman/fogging.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2026-05-12 07:30:00',
            ],
            [
                'id'               => 3,
                'judul'            => 'Draf Tata Tertib Parkir & Keamanan Malam',
                'isi'              => "Rancangan aturan bersama jam malam portal gang, penitipan kendaraan tamu, serta sistem jadwal ronda warga untuk disetujui bersama.\n\nDraf ini masih dalam tahap pembahasan dan belum berlaku.",
                'gambar'           => null,
                'status_publikasi' => 'draft',
                'admin_id'         => 1,
                'created_at'       => '2026-05-10 10:00:00',
            ],
            [
                'id'               => 4,
                'judul'            => 'Penyaluran Bantuan Sembako Warga Lansia Tahap II',
                'isi'              => "Pendaftaran dan verifikasi penerima paket sembako tahap II bagi warga usia lanjut di sekretariat RT. Pengambilan dapat diwakilkan oleh anggota keluarga dalam satu Kartu Keluarga dengan membawa fotokopi KTP dan KK asli.\n\nBatas pendaftaran: 30 April 2026. Penyaluran dilaksanakan 5 Mei 2026.",
                'gambar'           => 'images/pengumuman/sembako.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2026-04-12 09:00:00',
            ],
            [
                'id'               => 5,
                'judul'            => 'Posyandu Balita Bulan Mei 2026',
                'isi'              => "Posyandu rutin bulan Mei dilaksanakan pada Jumat, 17 Mei 2026 pukul 08.00-11.00 WIB di Balai RW 08. Bawa buku KIA dan KMS anak.\n\nLayanan: penimbangan berat badan, pengukuran tinggi badan, imunisasi, dan konsultasi gizi.",
                'gambar'           => 'images/pengumuman/sembako.jpg',
                'status_publikasi' => 'publish',
                'admin_id'         => 1,
                'created_at'       => '2026-05-08 08:00:00',
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

    public static function formatTanggal(string $date): string
    {
        $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];
        $ts = strtotime($date);
        return date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
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
}
