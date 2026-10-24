<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\Surat;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WargaDashboardDanKeluargaTest extends TestCase
{
    use RefreshDatabase;

    private User $userWarga;
    private Keluarga $keluarga;
    private Warga $kepalaWarga;
    private Warga $istriWarga;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userWarga = User::factory()->create([
            'username' => '3271048809920001',
            'role'     => 'warga',
            'no_telp'  => '081234567891',
        ]);

        $this->keluarga = Keluarga::create([
            'no_kk'          => '3271048809920001',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        $this->kepalaWarga = Warga::create([
            'user_id'             => $this->userWarga->id,
            'keluarga_id'         => $this->keluarga->id,
            'nik'                 => '3271048809920001',
            'nama'                => 'Budi Santoso',
            'tempat_lahir'        => 'Bandung',
            'tanggal_lahir'       => '1992-09-08',
            'jenis_kelamin'       => 'laki_laki',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'pekerjaan'           => 'Karyawan Swasta',
            'status_perkawinan'   => 'kawin',
            'status_hubungan'     => 'kepala_keluarga',
            'status_warga'        => 'hidup',
            'alamat'              => 'Jl. Melati Blok A No. 1, RT 04',
        ]);

        $this->istriWarga = Warga::create([
            'keluarga_id'         => $this->keluarga->id,
            'nik'                 => '3271044502950002',
            'nama'                => 'Siti Aminah',
            'tempat_lahir'        => 'Jakarta',
            'tanggal_lahir'       => '1995-02-05',
            'jenis_kelamin'       => 'perempuan',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'pekerjaan'           => 'Ibu Rumah Tangga',
            'status_perkawinan'   => 'kawin',
            'status_hubungan'     => 'istri',
            'status_warga'        => 'hidup',
            'alamat'              => 'Jl. Melati Blok A No. 1, RT 04',
        ]);
    }

    public function test_warga_dapat_melihat_dashboard_beranda_dengan_data_riil(): void
    {
        Surat::create([
            'pemohon_id'      => $this->kepalaWarga->id,
            'warga_tujuan_id' => $this->istriWarga->id,
            'keperluan'       => 'Pengantar SKCK BUMN',
            'status'          => 'menunggu',
        ]);

        $response = $this->actingAs($this->userWarga)->get(route('warga.beranda'));

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('3271048809920001');
        $response->assertSee('Jl. Melati Blok A No. 1');
        $response->assertSee('Pengantar SKCK BUMN');
        $response->assertSee('Diproses RT');
    }

    public function test_warga_dapat_melihat_halaman_data_keluarga_dengan_data_riil(): void
    {
        Riwayat_Perubahan::create([
            'keluarga_id'     => $this->keluarga->id,
            'warga_id'        => $this->istriWarga->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama'       => ['pekerjaan' => 'Ibu Rumah Tangga'],
            'data_baru'       => ['pekerjaan' => 'Wiraswasta'],
            'bukti_path'      => 'contoh_buku_nikah.png',
            'status'          => 'menunggu',
            'alasan'          => 'Buka usaha mandiri',
        ]);

        $response = $this->actingAs($this->userWarga)->get(route('warga.keluarga'));

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Aminah');
        $response->assertSee('3271048809920001');
        $response->assertSee('Total Jiwa Terdaftar');
        $response->assertSee('Menunggu Verifikasi RT');
        $response->assertSee('Buka usaha mandiri');
        $response->assertSee('Wiraswasta');
    }

    public function test_warga_dapat_mengajukan_perubahan_data_dan_tersimpan_di_database(): void
    {
        $response = $this->actingAs($this->userWarga)->post(route('warga.keluarga.edit.store'), [
            'target_anggota' => $this->istriWarga->nik,
            'new_nik' => $this->istriWarga->nik,
            'new_nama' => 'Siti Aminah, S.Kom',
            'new_tempat_lahir' => 'Jakarta',
            'new_tgl_lahir' => '1995-02-05',
            'new_jk' => 'Perempuan',
            'new_agama' => 'Islam',
            'new_pendidikan' => 'Diploma IV / Strata 1 (S1)',
            'new_pekerjaan' => 'Software Engineer',
            'new_alamat' => 'Jl. Melati Blok A No. 1, RT 04',
            'new_shdk' => 'Istri',
            'new_status_warga' => 'Hidup (Aktif)',
            'alasan_perubahan' => 'Pembaruan gelar sarjana dan profesi baru',
        ]);

        $response->assertRedirect(route('warga.keluarga'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('riwayat__perubahans', [
            'keluarga_id'     => $this->keluarga->id,
            'warga_id'        => $this->istriWarga->id,
            'jenis_pengajuan' => 'ubah_data',
            'status'          => 'menunggu',
            'alasan'          => 'Pembaruan gelar sarjana dan profesi baru',
        ]);
    }

    public function test_warga_dapat_mendaftarkan_anggota_baru_dan_tersimpan_di_database(): void
    {
        $response = $this->actingAs($this->userWarga)->post(route('warga.keluarga.tambah.store'), [
            'nik' => '3271041908230005',
            'nama_lengkap' => 'Muhammad Rayhan Santoso',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2023-08-19',
            'jenis_kelamin' => 'laki_laki',
            'hubungan_keluarga' => 'anak',
            'agama' => 'islam',
            'pendidikan_terakhir' => 'tidak_belum_sekolah',
            'pekerjaan' => 'Belum / Tidak Bekerja',
        ]);

        $response->assertRedirect(route('warga.keluarga'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('riwayat__perubahans', [
            'keluarga_id'     => $this->keluarga->id,
            'warga_id'        => null,
            'jenis_pengajuan' => 'tambah_anggota',
            'status'          => 'menunggu',
        ]);
    }

    public function test_tamu_tidak_dapat_mengakses_halaman_warga(): void
    {
        $res1 = $this->get(route('warga.beranda'));
        $res1->assertRedirect(route('login'));

        $res2 = $this->get(route('warga.keluarga'));
        $res2->assertRedirect(route('login'));
    }

    public function test_pengajuan_perubahan_hanya_mencatat_atribut_yang_diubah(): void
    {
        // Ubah HANYA field pekerjaan saja, biarkan field lainnya sama persis dengan database
        $response = $this->actingAs($this->userWarga)->post(route('warga.keluarga.edit.store'), [
            'target_anggota' => $this->kepalaWarga->nik,
            'new_nama' => $this->kepalaWarga->nama,
            'new_tempat_lahir' => $this->kepalaWarga->tempat_lahir,
            'new_tgl_lahir' => \Carbon\Carbon::parse($this->kepalaWarga->tanggal_lahir)->format('Y-m-d'),
            'new_jk' => $this->kepalaWarga->jenis_kelamin,
            'new_agama' => $this->kepalaWarga->agama,
            'new_pendidikan' => $this->kepalaWarga->pendidikan_terakhir,
            'new_pekerjaan' => 'PNS', // <--- HANYA INI YANG DIUBAH
            'new_alamat' => $this->kepalaWarga->alamat,
            'new_shdk' => $this->kepalaWarga->status_hubungan,
            'new_status_warga' => $this->kepalaWarga->status_warga,
            'alasan_perubahan' => 'Peralihan profesi menjadi PNS',
        ]);

        $response->assertRedirect(route('warga.keluarga'));

        $riwayat = \App\Models\Riwayat_Perubahan::where('warga_id', $this->kepalaWarga->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($riwayat);
        $this->assertEquals(['pekerjaan' => 'PNS'], $riwayat->data_baru);
        $this->assertEquals(['pekerjaan' => 'Karyawan Swasta'], $riwayat->data_lama);
    }

    public function test_warga_dapat_mengunggah_multiple_dokumen_bukti(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $file1 = \Illuminate\Http\UploadedFile::fake()->create('ktp_asli.pdf', 100, 'application/pdf');
        $file2 = \Illuminate\Http\UploadedFile::fake()->image('ijazah_legalisir.png');

        $response = $this->actingAs($this->userWarga)->post(route('warga.keluarga.edit.store'), [
            'target_anggota' => $this->kepalaWarga->nik,
            'new_nama' => $this->kepalaWarga->nama,
            'new_tempat_lahir' => $this->kepalaWarga->tempat_lahir,
            'new_tgl_lahir' => \Carbon\Carbon::parse($this->kepalaWarga->tanggal_lahir)->format('Y-m-d'),
            'new_jk' => $this->kepalaWarga->jenis_kelamin,
            'new_agama' => $this->kepalaWarga->agama,
            'new_pendidikan' => $this->kepalaWarga->pendidikan_terakhir,
            'new_pekerjaan' => 'Dosen Tetap',
            'new_alamat' => $this->kepalaWarga->alamat,
            'new_shdk' => $this->kepalaWarga->status_hubungan,
            'new_status_warga' => $this->kepalaWarga->status_warga,
            'alasan_perubahan' => 'Pembaruan data dosen',
            'bukti' => [$file1, $file2],
        ]);

        $response->assertRedirect(route('warga.keluarga'));

        $riwayat = \App\Models\Riwayat_Perubahan::where('warga_id', $this->kepalaWarga->id)
            ->where('alasan', 'Pembaruan data dosen')
            ->latest('id')
            ->first();

        $this->assertNotNull($riwayat);
        $this->assertCount(2, $riwayat->bukti_list);
    }

    public function test_warga_dapat_membatalkan_pengajuan_yang_masih_menunggu(): void
    {
        $riwayat = \App\Models\Riwayat_Perubahan::create([
            'warga_id' => $this->kepalaWarga->id,
            'keluarga_id' => $this->keluarga->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama' => ['pekerjaan' => 'Karyawan Swasta'],
            'data_baru' => ['pekerjaan' => 'Wiraswasta'],
            'bukti_path' => 'uploads/bukti/test.png',
            'status' => 'menunggu',
            'alasan' => 'Mau ganti pekerjaan',
        ]);

        $response = $this->actingAs($this->userWarga)->post(route('warga.keluarga.pengajuan.batal', $riwayat->id));
        $response->assertRedirect(route('warga.keluarga'));
        $response->assertSessionHas('success');

        $this->assertEquals('dibatalkan', $riwayat->fresh()->status);

        // Pastikan di halaman verifikasi admin pengajuan ini hilang/tidak muncul
        $adminUser = \App\Models\User::where('role', 'admin')->first() ?? \App\Models\User::factory()->create(['role' => 'admin']);
        $adminRes = $this->actingAs($adminUser)->get(route('admin.data-warga'));
        $adminRes->assertOk();
        $adminRes->assertDontSee('Mau ganti pekerjaan');
    }

    public function test_warga_tidak_dapat_membatalkan_pengajuan_yang_sudah_disetujui(): void
    {
        $riwayat = \App\Models\Riwayat_Perubahan::create([
            'warga_id' => $this->kepalaWarga->id,
            'keluarga_id' => $this->keluarga->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama' => ['pekerjaan' => 'Karyawan Swasta'],
            'data_baru' => ['pekerjaan' => 'Wiraswasta'],
            'bukti_path' => 'uploads/bukti/test.png',
            'status' => 'disetujui',
            'alasan' => 'Sudah diverifikasi',
        ]);

        $response = $this->actingAs($this->userWarga)->post(route('warga.keluarga.pengajuan.batal', $riwayat->id));
        $response->assertSessionHas('error');
        $this->assertEquals('disetujui', $riwayat->fresh()->status);
    }

    public function test_warga_tidak_dapat_mengubah_nik_dan_halaman_edit_menampilkan_nik_terkunci(): void
    {
        $response = $this->actingAs($this->userWarga)->get(route('warga.keluarga.edit'));
        $response->assertOk();
        $response->assertSee('Hanya Admin RT');
        $response->assertSee('Terkunci');

        // Pastikan jika warga mencoba mengirim new_nik yang berbeda, sistem backend mengabaikannya
        $postRes = $this->actingAs($this->userWarga)->post(route('warga.keluarga.edit.store'), [
            'target_anggota' => $this->kepalaWarga->nik,
            'new_nik' => '9999999999999999',
            'new_nama' => $this->kepalaWarga->nama,
            'new_tempat_lahir' => $this->kepalaWarga->tempat_lahir,
            'new_tgl_lahir' => \Carbon\Carbon::parse($this->kepalaWarga->tanggal_lahir)->format('Y-m-d'),
            'new_pekerjaan' => 'Konsultan IT',
            'alasan_perubahan' => 'Coba ubah NIK dan pekerjaan',
        ]);

        $postRes->assertRedirect(route('warga.keluarga'));

        $riwayat = \App\Models\Riwayat_Perubahan::where('warga_id', $this->kepalaWarga->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($riwayat);
        $this->assertArrayNotHasKey('nik', $riwayat->data_baru);
        $this->assertEquals($this->kepalaWarga->nik, $this->kepalaWarga->fresh()->nik);
    }

    public function test_warga_melihat_anggota_keluarga_meninggal_dengan_badge_khusus_dan_aksi_terkunci(): void
    {
        $this->istriWarga->update(['status_warga' => 'meninggal']);

        $response = $this->actingAs($this->userWarga)->get(route('warga.keluarga'));

        $response->assertStatus(200);
        $response->assertSee('Meninggal (Alm.)');
        $response->assertSee('(Alm.)');
        $response->assertSee('Terkunci');
        $response->assertDontSee(route('warga.keluarga.edit', ['nik' => $this->istriWarga->nik]));
    }
}

