<?php

namespace Tests\Feature;

use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\Warga;
use App\Services\IuranService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IuranServiceTest extends TestCase
{
    use RefreshDatabase;

    private IuranService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(IuranService::class);
    }

    public function test_ambil_nominal_standar_prioritas_1_belum_lunas(): void
    {
        $keluarga = Keluarga::create([
            'no_kk'          => '3271000000000001',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        // Baris lunas nominal 50.000 (bulan 2)
        Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 2,
            'tahun'       => 2026,
            'nominal'     => 50000,
            'status'      => 'lunas',
        ]);

        // Baris belum lunas nominal 85.000 (bulan 1)
        Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 1,
            'tahun'       => 2026,
            'nominal'     => 85000,
            'status'      => 'belum_lunas',
        ]);

        $nominal = $this->service->ambilNominalStandar();

        // Prioritas 1: ambil dari baris belum_lunas
        $this->assertEquals(85000, $nominal);
    }

    public function test_ambil_nominal_standar_prioritas_2_status_apapun_jika_tidak_ada_belum_lunas(): void
    {
        $keluarga = Keluarga::create([
            'no_kk'          => '3271000000000002',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 1,
            'tahun'       => 2026,
            'nominal'     => 60000,
            'status'      => 'lunas',
        ]);

        $nominal = $this->service->ambilNominalStandar();

        $this->assertEquals(60000, $nominal);
    }

    public function test_ambil_nominal_standar_mengakui_nominal_nol_kebijakan_gratis(): void
    {
        $keluarga = Keluarga::create([
            'no_kk'          => '3271000000000099',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 1,
            'tahun'       => 2026,
            'nominal'     => 0,
            'status'      => 'belum_lunas',
        ]);

        $nominal = $this->service->ambilNominalStandar();

        // Nominal 0 (gratis/subsidi) tetap sah diakui
        $this->assertEquals(0.0, $nominal);
    }

    public function test_ambil_nominal_standar_prioritas_3_config_jika_tabel_kosong(): void
    {
        $nominal = $this->service->ambilNominalStandar();

        $this->assertEquals(75000, $nominal);
    }

    public function test_lazy_generation_membuat_tagihan_dari_bulan_awal_tagih_tanpa_duplikasi(): void
    {
        $keluarga = Keluarga::create([
            'no_kk'          => '3271000000000003',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);
        // Set tanggal dibuat di bulan Maret (bulan 3)
        $keluarga->created_at = Carbon::create(2026, 3, 10);
        $keluarga->save();

        Warga::create([
            'keluarga_id'     => $keluarga->id,
            'nik'             => '3271000000000003',
            'nama'            => 'Warga Uji',
            'jenis_kelamin'   => 'laki_laki',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga'    => 'hidup',
        ]);

        // Jalankan lazy generation pertama
        $this->service->generateLazyIuranKeluarga($keluarga, 2026);

        // Harus dibuat dari bulan 3 s.d 12 = 10 baris
        $this->assertEquals(10, Iuran::where('keluarga_id', $keluarga->id)->where('tahun', 2026)->count());
        $this->assertNull(Iuran::where('keluarga_id', $keluarga->id)->where('bulan', 1)->first());
        $this->assertNull(Iuran::where('keluarga_id', $keluarga->id)->where('bulan', 2)->first());
        $this->assertNotNull(Iuran::where('keluarga_id', $keluarga->id)->where('bulan', 3)->first());

        // Jalankan lazy generation kedua kali -> tidak boleh menduplikasi baris
        $this->service->generateLazyIuranKeluarga($keluarga, 2026);
        $this->assertEquals(10, Iuran::where('keluarga_id', $keluarga->id)->where('tahun', 2026)->count());
    }

    public function test_format_cards_12_bulan_menghasilkan_12_bulan_lengkap_dan_na(): void
    {
        $keluarga = Keluarga::create([
            'no_kk'          => '3271000000000004',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);
        $keluarga->created_at = Carbon::create(2026, 4, 1);
        $keluarga->save();

        Warga::create([
            'keluarga_id'     => $keluarga->id,
            'nik'             => '3271000000000004',
            'nama'            => 'Warga Uji 4',
            'jenis_kelamin'   => 'laki_laki',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga'    => 'hidup',
        ]);

        $this->service->generateLazyIuranKeluarga($keluarga, 2026);

        // Format 12 card dengan bulan berjalan Mei (bulan 5)
        $cards = $this->service->formatCards12Bulan($keluarga, 2026, 2026, 5);

        $this->assertCount(12, $cards);
        // Bulan 1, 2, 3 sebelum KK dibuat -> status na
        $this->assertEquals('na', $cards[0]['status']);
        $this->assertEquals('na', $cards[1]['status']);
        $this->assertEquals('na', $cards[2]['status']);

        // Bulan 4 sebelum bulan berjalan (Mei) -> menunggak
        $this->assertEquals('belum_lunas', $cards[3]['status']);
        $this->assertEquals('MENUNGGAK', $cards[3]['badge_label']);

        // Bulan 5 (Mei) -> berjalan
        $this->assertEquals('belum_lunas', $cards[4]['status']);
        $this->assertTrue($cards[4]['penanda_berjalan']);
    }

    public function test_catat_pelunasan_mengubah_status_menjadi_lunas(): void
    {
        $keluarga = Keluarga::create([
            'no_kk'          => '3271000000000005',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        $iuran = Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 6,
            'tahun'       => 2026,
            'nominal'     => 75000,
            'status'      => 'belum_lunas',
        ]);

        $updated = $this->service->catatPelunasan($iuran, '2026-06-15', 75000, 'Dibayar tunai');

        $this->assertEquals('lunas', $updated->status);
        $this->assertNotNull($updated->tanggal_bayar);
        $this->assertEquals('Dibayar tunai', $updated->catatan);
    }

    public function test_atur_standar_iuran_hanya_mengubah_belum_lunas_mulai_bulan_berjalan(): void
    {
        $keluarga = Keluarga::create([
            'no_kk'          => '3271000000000006',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        // Bulan 3 (lampau) belum lunas: nominal 50.000
        $lampau = Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 3,
            'tahun'       => 2026,
            'nominal'     => 50000,
            'status'      => 'belum_lunas',
        ]);

        // Bulan 5 (berjalan) belum lunas: nominal 50.000
        $berjalan = Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 5,
            'tahun'       => 2026,
            'nominal'     => 50000,
            'status'      => 'belum_lunas',
        ]);

        // Bulan 6 (mendatang) sudah lunas: nominal 50.000
        $mendatangLunas = Iuran::create([
            'keluarga_id' => $keluarga->id,
            'bulan'       => 6,
            'tahun'       => 2026,
            'nominal'     => 50000,
            'status'      => 'lunas',
        ]);

        // Ubah standar nominal menjadi 100.000 mulai bulan 5 (Mei)
        $affected = $this->service->aturStandarIuran(100000, 'Kenaikan kas', 2026, 5);

        $this->assertEquals(1, $affected);

        // Bulan lampau tidak boleh berubah
        $this->assertEquals(50000, $lampau->fresh()->nominal);
        // Bulan berjalan harus berubah menjadi 100.000
        $this->assertEquals(100000, $berjalan->fresh()->nominal);
        // Bulan lunas tidak boleh berubah
        $this->assertEquals(50000, $mendatangLunas->fresh()->nominal);
    }

    public function test_hitung_statistik_admin_bekerja_akurat_dan_efisien(): void
    {
        // Kondisi 1: Saat tabel iurans kosong
        $statKosong = $this->service->hitungStatistikAdmin(2026, 5);
        $this->assertEquals(0.0, $statKosong['totalPenerimaanKas']);
        $this->assertEquals(0, $statKosong['totalKkLunasBulanIni']);
        $this->assertEquals(0, $statKosong['totalKkBelumBulanIni']);
        $this->assertEquals(0, $statKosong['totalTunggakanKeseluruhan']);

        $k1 = Keluarga::create(['no_kk' => '3271000000000010', 'status_ekonomi' => 'mampu', 'status_tinggal' => 'tetap']);
        $k2 = Keluarga::create(['no_kk' => '3271000000000020', 'status_ekonomi' => 'mampu', 'status_tinggal' => 'tetap']);

        // K1: Lunas bulan 4 (75.000) & Lunas bulan 5 (100.000)
        Iuran::create(['keluarga_id' => $k1->id, 'bulan' => 4, 'tahun' => 2026, 'nominal' => 75000, 'status' => 'lunas', 'tanggal_bayar' => now()]);
        Iuran::create(['keluarga_id' => $k1->id, 'bulan' => 5, 'tahun' => 2026, 'nominal' => 100000, 'status' => 'lunas', 'tanggal_bayar' => now()]);

        // K2: Belum lunas bulan 3 (tunggakan lampau), Belum lunas bulan 5 (berjalan), Belum lunas bulan 6 (mendatang)
        Iuran::create(['keluarga_id' => $k2->id, 'bulan' => 3, 'tahun' => 2026, 'nominal' => 75000, 'status' => 'belum_lunas']);
        Iuran::create(['keluarga_id' => $k2->id, 'bulan' => 5, 'tahun' => 2026, 'nominal' => 100000, 'status' => 'belum_lunas']);
        Iuran::create(['keluarga_id' => $k2->id, 'bulan' => 6, 'tahun' => 2026, 'nominal' => 100000, 'status' => 'belum_lunas']);

        // Hitung statistik untuk periode Mei 2026 (bulan 5)
        $stat = $this->service->hitungStatistikAdmin(2026, 5);

        // Total kas = 75.000 + 100.000 = 175.000
        $this->assertEquals(175000.0, $stat['totalPenerimaanKas']);
        // KK Lunas bulan 5 = 1 KK (K1)
        $this->assertEquals(1, $stat['totalKkLunasBulanIni']);
        // KK Belum lunas bulan 5 = 1 KK (K2)
        $this->assertEquals(1, $stat['totalKkBelumBulanIni']);
        // Total tunggakan (bulan <= 5): bulan 3 dan bulan 5 = 2 baris (bulan 6 diabaikan)
        $this->assertEquals(2, $stat['totalTunggakanKeseluruhan']);
    }
}
