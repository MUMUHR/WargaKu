<?php

namespace Tests\Feature;

use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminIuranTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $userWarga;
    private Keluarga $keluarga;
    private Warga $kepala;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'adminrt',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->userWarga = User::factory()->create([
            'username' => '3275012304800001',
            'role' => 'warga',
            'no_telp' => '081234567890',
            'password' => Hash::make('secretpass'),
        ]);

        $this->keluarga = Keluarga::create([
            'no_kk' => '3275010905120008',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);
        $this->keluarga->created_at = Carbon::create(2025, 1, 1);
        $this->keluarga->save();

        $this->kepala = Warga::create([
            'keluarga_id' => $this->keluarga->id,
            'user_id' => $this->userWarga->id,
            'nik' => '3275012304800001',
            'nama' => 'Bambang Santoso, S.T.',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1980-04-23',
            'jenis_kelamin' => 'laki_laki',
            'agama' => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'pekerjaan' => 'Pegawai BUMN',
            'alamat' => 'Blok B4 No. 12, RT 04',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga' => 'hidup',
        ]);
    }

    public function test_admin_dapat_melihat_halaman_kelola_iuran_dan_memicu_lazy_generation_12_bulan(): void
    {
        // Awalnya tabel iurans masih kosong
        $this->assertEquals(0, Iuran::count());

        $response = $this->actingAs($this->admin)->get(route('admin.iuran'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.iuran');
        $response->assertSee('Kelola &amp; Rekap Iuran Warga', false);
        $response->assertSee('3275010905120008');
        $response->assertSee('Bambang Santoso, S.T.');

        // Sesuai BR-06: Lazy generation membuat 12 bulan (Januari - Desember) untuk KK yang terdaftar sejak 2025
        $this->assertEquals(12, Iuran::where('keluarga_id', $this->keluarga->id)->count());

        // Default nominal dari config wargaku (75000)
        $this->assertEquals(75000, Iuran::where('keluarga_id', $this->keluarga->id)->first()->nominal);
    }

    public function test_admin_dapat_mencatat_pelunasan_iuran_sesuai_br06(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $iuran = Iuran::create([
            'keluarga_id' => $this->keluarga->id,
            'nominal' => 75000,
            'catatan' => null,
            'bulan' => $now->month,
            'tahun' => $now->year,
            'status' => 'belum_lunas',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.iuran.pelunasan', $iuran->id), [
            'tanggal_bayar' => $now->format('Y-m-d'),
            'catatan' => 'Lunas tunai di Pos Satpam RT 04',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $iuran->refresh();
        $this->assertEquals('lunas', $iuran->status);
        $this->assertNotNull($iuran->tanggal_bayar);
        $this->assertEquals('Lunas tunai di Pos Satpam RT 04', $iuran->catatan);
    }

    public function test_admin_dapat_mengatur_standar_iuran_bulanan_br06(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $tahun = $now->year;

        // Buat baris bulan lampau (bulan 1), bulan berjalan, dan bulan depan
        $bulanLampau = 1;
        $bulanBerjalan = $now->month;
        $bulanDepan = min(12, $now->month + 1);

        $iuranLampau = Iuran::create([
            'keluarga_id' => $this->keluarga->id,
            'nominal' => 50000,
            'bulan' => $bulanLampau,
            'tahun' => $tahun,
            'status' => 'belum_lunas',
        ]);

        $iuranBerjalan = Iuran::create([
            'keluarga_id' => $this->keluarga->id,
            'nominal' => 50000,
            'bulan' => $bulanBerjalan,
            'tahun' => $tahun,
            'status' => 'belum_lunas',
        ]);

        $iuranLunas = Iuran::create([
            'keluarga_id' => $this->keluarga->id,
            'nominal' => 50000,
            'bulan' => $bulanDepan,
            'tahun' => $tahun,
            'status' => 'lunas',
            'tanggal_bayar' => $now,
        ]);

        // Admin mengubah standar iuran menjadi 100000
        $response = $this->actingAs($this->admin)->post(route('admin.iuran.atur-standar'), [
            'nominal' => 100000,
            'catatan' => 'Penyesuaian tarif iuran kebersihan baru RT 04',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // BR-06: Baris belum_lunas mulai bulan berjalan berubah ke 100000
        $iuranBerjalan->refresh();
        $this->assertEquals(100000, (int) $iuranBerjalan->nominal);

        // BR-06: Baris lunas TIDAK berubah
        $iuranLunas->refresh();
        $this->assertEquals(50000, (int) $iuranLunas->nominal);

        // Jika bulan berjalan > 1, baris lampau TIDAK berubah
        if ($bulanBerjalan > 1) {
            $iuranLampau->refresh();
            $this->assertEquals(50000, (int) $iuranLampau->nominal);
        }
    }

    public function test_lazy_generation_sesuai_bulan_pendaftaran_dan_abaikan_kk_tanpa_warga_hidup(): void
    {
        $tahun = Carbon::now()->year;

        // KK baru terdaftar di bulan Juni tahun berjalan
        $keluargaBaru = Keluarga::create([
            'no_kk' => '3275010905129999',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);
        $keluargaBaru->created_at = Carbon::create($tahun, 6, 15);
        $keluargaBaru->save();

        Warga::create([
            'keluarga_id' => $keluargaBaru->id,
            'nik' => '3275012304809999',
            'nama' => 'Warga Baru Juni',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'jenis_kelamin' => 'laki_laki',
            'agama' => 'islam',
            'pendidikan_terakhir' => 'sma_sederajat',
            'pekerjaan' => 'Karyawan Swasta',
            'alamat' => 'Blok C1 No. 5',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga' => 'hidup',
        ]);

        // KK tanpa warga hidup (semua meninggal/pindah)
        $keluargaKosong = Keluarga::create([
            'no_kk' => '3275010905128888',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);
        $keluargaKosong->created_at = Carbon::create(2024, 1, 1);
        $keluargaKosong->save();

        Warga::create([
            'keluarga_id' => $keluargaKosong->id,
            'nik' => '3275012304808888',
            'nama' => 'Alm. Warga Kosong',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1950-01-01',
            'jenis_kelamin' => 'laki_laki',
            'agama' => 'islam',
            'pendidikan_terakhir' => 'sma_sederajat',
            'pekerjaan' => 'Pensiunan',
            'alamat' => 'Blok C1 No. 6',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga' => 'meninggal',
        ]);

        $this->actingAs($this->admin)->get(route('admin.iuran'));

        // Keluarga baru Juni seharusnya memiliki 7 bulan tagihan (bulan 6 s.d. 12 = 12 - 6 + 1 = 7)
        $this->assertEquals(7, Iuran::where('keluarga_id', $keluargaBaru->id)->count());
        $this->assertFalse(Iuran::where('keluarga_id', $keluargaBaru->id)->where('bulan', 5)->exists());
        $this->assertTrue(Iuran::where('keluarga_id', $keluargaBaru->id)->where('bulan', 6)->exists());
        $this->assertTrue(Iuran::where('keluarga_id', $keluargaBaru->id)->where('bulan', 12)->exists());

        // Keluarga kosong (tidak ada yang hidup) TIDAK dibuatkan tagihan iuran sama sekali
        $this->assertEquals(0, Iuran::where('keluarga_id', $keluargaKosong->id)->count());
    }

    public function test_warga_biasa_tidak_dapat_mengakses_admin_iuran(): void
    {
        $response = $this->actingAs($this->userWarga)->get(route('admin.iuran'));
        $response->assertRedirect(route('warga.beranda'));
        $response->assertSessionHas('error');
    }

    public function test_riwayat_iuran_memiliki_filter_default_bulan_berjalan_dan_tahun_berjalan(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $bulanBerjalan = $now->month;
        $tahunBerjalan = $now->year;

        $response = $this->actingAs($this->admin)->get(route('admin.iuran'));

        $response->assertStatus(200);

        // Verifikasi bahwa dropdown Filter Bulan memiliki option bulan berjalan yang terpilih (selected)
        $response->assertSee('<option value="' . $bulanBerjalan . '" selected>', false);

        // Verifikasi bahwa dropdown Filter Tahun memiliki option tahun berjalan yang terpilih (selected)
        $response->assertSee('<option value="' . $tahunBerjalan . '" selected>' . $tahunBerjalan . '</option>', false);

        // Verifikasi banner judul riwayat kas dan baris empty state
        $response->assertSee('Catatan Pembayaran &amp; Riwayat Kas', false);
        $response->assertSee('id="empty-row-riwayat"', false);
    }
}
