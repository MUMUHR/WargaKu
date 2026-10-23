<?php

namespace Tests\Feature;

use App\Enums\StatusSurat;
use App\Models\Keluarga;
use App\Models\Surat;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WargaSuratTest extends TestCase
{
    use RefreshDatabase;

    private User $userWarga;
    private Keluarga $keluarga;
    private Warga $kepalaWarga;
    private Warga $anggotaWarga;

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

        $this->anggotaWarga = Warga::create([
            'user_id' => $this->userWarga->id,
            'keluarga_id' => $this->keluarga->id,
            'nik' => '3271044502950002',
            'nama' => 'Siti Aminah',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-02-05',
            'jenis_kelamin' => 'perempuan',
            'agama' => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'alamat' => 'Jl. Melati Blok A No. 1, RT 04',
            'status_hubungan' => 'istri',
            'status_warga' => 'hidup',
        ]);
    }

    public function test_warga_dapat_melihat_halaman_surat_dengan_data_riil(): void
    {
        // Buat 1 surat menunggu, 1 disetujui, 1 ditolak
        Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->anggotaWarga->id,
            'keperluan' => 'Pengantar permohonan SKCK',
            'nomor_surat' => null,
            'status' => StatusSurat::MENUNGGU,
        ]);

        Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->kepalaWarga->id,
            'keperluan' => 'Pengantar permohonan SKU Toko Kelontong',
            'nomor_surat' => '001/SP/RT04/II/2026',
            'status' => StatusSurat::DISETUJUI,
        ]);

        Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->kepalaWarga->id,
            'keperluan' => 'Pengantar permohonan paspor umroh',
            'nomor_surat' => null,
            'status' => StatusSurat::DITOLAK,
            'alasan' => 'Dokumen berkas belum lengkap',
        ]);

        $response = $this->actingAs($this->userWarga)->get(route('warga.surat'));

        $response->assertStatus(200);
        $response->assertSee('Layanan Pengajuan Surat Pengantar RT 04');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Pengantar permohonan SKCK');
        $response->assertSee('001/SP/RT04/II/2026');
        $response->assertSee('Dokumen berkas belum lengkap');
    }

    public function test_warga_dapat_mengajukan_surat_baru(): void
    {
        $response = $this->actingAs($this->userWarga)->post(route('warga.surat.store'), [
            'warga_tujuan_id' => $this->anggotaWarga->id,
            'keperluan' => 'Pengantar permohonan pembuatan visa liburan keluarga',
        ]);

        $response->assertRedirect(route('warga.surat'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('surats', [
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->anggotaWarga->id,
            'keperluan' => 'Pengantar permohonan pembuatan visa liburan keluarga',
            'status' => StatusSurat::MENUNGGU->value,
        ]);
    }

    public function test_warga_ditolak_mengajukan_surat_untuk_warga_luar_kk(): void
    {
        $keluargaLain = Keluarga::create([
            'no_kk' => '3271050106859999',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        $wargaLain = Warga::create([
            'keluarga_id' => $keluargaLain->id,
            'nik' => '3271050106859999',
            'nama' => 'Orang Lain',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'laki_laki',
            'agama' => 'islam',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga' => 'hidup',
        ]);

        $response = $this->actingAs($this->userWarga)->post(route('warga.surat.store'), [
            'warga_tujuan_id' => $wargaLain->id,
            'keperluan' => 'Pengantar permohonan surat sembarang',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_warga_dapat_membatalkan_surat_menunggu(): void
    {
        $surat = Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->anggotaWarga->id,
            'keperluan' => 'Pengantar yang mau dibatalkan',
            'status' => StatusSurat::MENUNGGU,
        ]);

        $response = $this->actingAs($this->userWarga)->post(route('warga.surat.batal', $surat->id));

        $response->assertRedirect(route('warga.surat'));
        $response->assertSessionHas('success');

        $this->assertEquals(StatusSurat::DIBATALKAN, $surat->fresh()->status);
    }

    public function test_warga_tidak_dapat_membatalkan_surat_yang_sudah_disetujui(): void
    {
        $surat = Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->kepalaWarga->id,
            'keperluan' => 'Pengantar resmi',
            'nomor_surat' => '001/SP/RT04/II/2026',
            'status' => StatusSurat::DISETUJUI,
        ]);

        $response = $this->actingAs($this->userWarga)->post(route('warga.surat.batal', $surat->id));

        $response->assertSessionHas('error');
        $this->assertEquals(StatusSurat::DISETUJUI, $surat->fresh()->status);
    }

    public function test_warga_dapat_mencetak_surat_disetujui(): void
    {
        $surat = Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->anggotaWarga->id,
            'keperluan' => 'Pengantar permohonan SKCK resmi',
            'nomor_surat' => '005/SP/RT04/X/2026',
            'status' => StatusSurat::DISETUJUI,
        ]);

        $response = $this->actingAs($this->userWarga)->get(route('warga.surat.cetak', $surat->id));

        $response->assertStatus(200);
        $response->assertSee('SURAT PENGANTAR');
        $response->assertSee('005/SP/RT04/X/2026');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Pengantar permohonan SKCK resmi');
    }

    public function test_warga_tidak_dapat_mencetak_surat_belum_disetujui(): void
    {
        $surat = Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->anggotaWarga->id,
            'keperluan' => 'Pengantar permohonan SKCK belum diverifikasi',
            'status' => StatusSurat::MENUNGGU,
        ]);

        $response = $this->actingAs($this->userWarga)->get(route('warga.surat.cetak', $surat->id));

        $response->assertRedirect(route('warga.surat'));
        $response->assertSessionHas('error');
    }

    public function test_warga_dapat_melihat_preview_surat_disetujui(): void
    {
        $surat = Surat::create([
            'pemohon_id' => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->anggotaWarga->id,
            'keperluan' => 'Pengantar permohonan SKCK resmi',
            'nomor_surat' => '005/SP/RT04/X/2026',
            'status' => StatusSurat::DISETUJUI,
        ]);

        $response = $this->actingAs($this->userWarga)->get(route('warga.surat.preview', $surat->id));

        $response->assertStatus(200);
        $response->assertSee('SURAT PENGANTAR');
        $response->assertSee('005/SP/RT04/X/2026');
    }
}
