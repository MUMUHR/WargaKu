<?php

namespace Tests\Feature;

use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WargaIuranTest extends TestCase
{
    use RefreshDatabase;

    private User $userWarga;
    private Keluarga $keluarga;
    private Warga $kepalaWarga;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userWarga = User::factory()->create([
            'role' => 'warga',
            'username' => '3271048809920001',
        ]);

        $this->keluarga = Keluarga::create([
            'no_kk' => '3271048809920001',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);
        $this->keluarga->created_at = Carbon::create(2025, 1, 1);
        $this->keluarga->save();

        $this->kepalaWarga = Warga::create([
            'user_id' => $this->userWarga->id,
            'keluarga_id' => $this->keluarga->id,
            'nik' => '3271048809920001',
            'nama' => 'Budi Santoso',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1992-09-08',
            'jenis_kelamin' => 'laki_laki',
            'agama' => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'alamat' => 'Jl. Melati Blok A No. 1, RT 04',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga' => 'hidup',
        ]);
    }

    public function test_warga_dapat_melihat_halaman_iuran_dengan_12_card_bulan(): void
    {
        $response = $this->actingAs($this->userWarga)->get(route('warga.iuran'));

        $response->assertStatus(200);
        $response->assertViewIs('warga.iuran');
        $response->assertSee('Monitoring Iuran Warga RT 04');
        $response->assertSee('Budi Santoso');
        $response->assertSee('3271048809920001');

        // Pastikan nama 12 bulan tampil
        $response->assertSee('Januari');
        $response->assertSee('Februari');
        $response->assertSee('Maret');
        $response->assertSee('April');
        $response->assertSee('Mei');
        $response->assertSee('Juni');
        $response->assertSee('Juli');
        $response->assertSee('Agustus');
        $response->assertSee('September');
        $response->assertSee('Oktober');
        $response->assertSee('November');
        $response->assertSee('Desember');

        // Pastikan lazy generation membuat data iuran untuk keluarga ini
        $now = Carbon::now('Asia/Jakarta');
        $this->assertDatabaseHas('iurans', [
            'keluarga_id' => $this->keluarga->id,
            'tahun' => $now->year,
            'bulan' => 1,
        ]);
    }

    public function test_warga_melihat_status_lunas_dan_tanggal_bayar_jika_sudah_lunas(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        // Set bulan 1 lunas dengan tanggal bayar
        Iuran::updateOrCreate(
            ['keluarga_id' => $this->keluarga->id, 'bulan' => 1, 'tahun' => $now->year],
            [
                'nominal' => 75000,
                'status' => 'lunas',
                'tanggal_bayar' => Carbon::create($now->year, 1, 5, 10, 0, 0),
                'catatan' => 'Diterima tunai di Pos Ronda RT 04.',
            ]
        );

        $response = $this->actingAs($this->userWarga)->get(route('warga.iuran'));

        $response->assertStatus(200);
        $response->assertSee('LUNAS');
        $response->assertSee('Dibayar: 05 Jan ' . $now->year);
        $response->assertSee('Diterima tunai di Pos Ronda RT 04.');
    }

    public function test_warga_melihat_strip_jika_belum_lunas(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        Iuran::updateOrCreate(
            ['keluarga_id' => $this->keluarga->id, 'bulan' => 12, 'tahun' => $now->year],
            [
                'nominal' => 75000,
                'status' => 'belum_lunas',
                'tanggal_bayar' => null,
                'catatan' => 'Iuran Wajib Bulanan RT 04',
            ]
        );

        $response = $this->actingAs($this->userWarga)->get(route('warga.iuran'));

        $response->assertStatus(200);
        // Tanggal bayar belum lunas menampilkan tanda '-'
        $response->assertSee('-');
    }

    public function test_warga_melihat_rincian_catatan_admin_di_kartu_tarif_biru_dan_footer_standar(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        Iuran::updateOrCreate(
            ['keluarga_id' => $this->keluarga->id, 'bulan' => 10, 'tahun' => $now->year],
            [
                'nominal' => 85000,
                'status' => 'belum_lunas',
                'catatan' => 'Kebersihan Rp 35.000, Keamanan Pos Ronda Rp 35.000, & Kas Sosial Rp 15.000.',
            ]
        );

        $response = $this->actingAs($this->userWarga)->get(route('warga.iuran'));

        $response->assertStatus(200);
        // Deskripsi pada kartu biru besar menampilkan catatan rincian dari admin
        $response->assertSee('Kebersihan Rp 35.000, Keamanan Pos Ronda Rp 35.000, &amp; Kas Sosial Rp 15.000.', false);
        // Footer pada 12 card bulanan menampilkan label seragam
        $response->assertSee('Kas Wajib Bulanan RT 04');
    }

    public function test_warga_dapat_memilih_tahun_periode_riwayat(): void
    {
        // Buat data untuk tahun lampau 2024
        Iuran::create([
            'keluarga_id' => $this->keluarga->id,
            'nominal' => 75000,
            'bulan' => 1,
            'tahun' => 2024,
            'status' => 'lunas',
            'tanggal_bayar' => '2024-01-05 10:00:00',
            'catatan' => 'Lunas awal tahun 2024',
        ]);

        $response = $this->actingAs($this->userWarga)->get(route('warga.iuran', ['tahun' => 2024]));

        $response->assertStatus(200);
        $response->assertSee('Tahun 2024');
        $response->assertSee('Januari 2024');
        $response->assertSee('Lunas awal tahun 2024');
    }

    public function test_tamu_tidak_dapat_mengakses_halaman_iuran_warga(): void
    {
        $response = $this->get(route('warga.iuran'));

        $response->assertRedirect(route('login'));
    }
}
