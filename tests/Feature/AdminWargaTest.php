<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminWargaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $userWarga;
    private Keluarga $keluarga;
    private Warga $kepala;
    private Warga $istri;
    private Warga $anak;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'admin_rt04',
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

        $this->istri = Warga::create([
            'keluarga_id' => $this->keluarga->id,
            'nik' => '3275016508910004',
            'nama' => 'Ratna Dewi Puspita',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1982-08-15',
            'jenis_kelamin' => 'perempuan',
            'agama' => 'islam',
            'pendidikan_terakhir' => 's2',
            'pekerjaan' => 'Guru',
            'alamat' => 'Blok B4 No. 12, RT 04',
            'status_hubungan' => 'istri',
            'status_warga' => 'hidup',
        ]);

        $this->anak = Warga::create([
            'keluarga_id' => $this->keluarga->id,
            'nik' => '3275015509010003',
            'nama' => 'Siti Rahmawati',
            'tempat_lahir' => 'Depok',
            'tanggal_lahir' => '2001-09-15',
            'jenis_kelamin' => 'perempuan',
            'agama' => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'pekerjaan' => 'Karyawan Swasta',
            'alamat' => 'Blok B4 No. 12, RT 04',
            'status_hubungan' => 'anak',
            'status_warga' => 'hidup',
        ]);
    }

    public function test_admin_dapat_melihat_daftar_warga_dan_kartu_keluarga(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.data-warga'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.data-warga');
        $response->assertSee('Kelola Data Warga');
        $response->assertSee('3275010905120008');
        $response->assertSee('Bambang Santoso, S.T.');
        $response->assertSee(route('admin.data-warga.detail', '3275010905120008'));
    }

    public function test_admin_dapat_melihat_detail_kartu_keluarga_di_halaman_terpisah(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.data-warga.detail', $this->keluarga->no_kk));

        $response->assertStatus(200);
        $response->assertViewIs('admin.detail-keluarga');
        $response->assertSee('Detail Kartu Keluarga (KK):');
        $response->assertSee($this->keluarga->no_kk);
        $response->assertSee('Bambang Santoso, S.T.');
        $response->assertSee('Ratna Dewi Puspita');
        $response->assertSee('Siti Rahmawati');
        $response->assertSee('Kembali ke Daftar KK');
    }

    public function test_admin_dapat_mengubah_status_keluarga(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.status', $this->keluarga->no_kk), [
            'status_tinggal' => 'kontrak',
            'status_ekonomi' => 'kurang_mampu',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->keluarga->refresh();
        $this->assertEquals('kontrak', $this->keluarga->status_tinggal);
        $this->assertEquals('kurang_mampu', $this->keluarga->status_ekonomi);
    }

    public function test_admin_dapat_mereset_password_akun_keluarga(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.reset-password', $this->keluarga->no_kk));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->userWarga->refresh();
        $this->assertTrue(Hash::check($this->userWarga->username, $this->userWarga->password));
    }

    public function test_admin_dapat_menghapus_anggota_keluarga(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.data-warga.hapus-anggota', $this->anak->nik));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('wargas', [
            'nik' => $this->anak->nik,
        ]);
    }

    public function test_admin_dapat_memverifikasi_pengajuan_perubahan_data(): void
    {
        $pengajuan = Riwayat_Perubahan::create([
            'keluarga_id' => $this->keluarga->id,
            'warga_id' => $this->anak->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama' => ['pekerjaan' => 'Pelajar/Mahasiswa'],
            'data_baru' => ['pekerjaan' => 'Akuntan Publik'],
            'bukti_path' => 'dokumen/bukti.jpg',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.verifikasi', $pengajuan->id), [
            'tindakan' => 'setujui',
            'alasan' => 'Data sesuai bukti ijazah/surat kerja.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status);
        $this->assertEquals($this->admin->id, $pengajuan->admin_id);

        $this->anak->refresh();
        $this->assertEquals('Akuntan Publik', $this->anak->pekerjaan);
    }

    public function test_warga_biasa_tidak_dapat_mengakses_admin_data_warga(): void
    {
        $response = $this->actingAs($this->userWarga)->get(route('admin.data-warga'));
        $response->assertRedirect(route('warga.beranda'));
        $response->assertSessionHas('error');

        $responseDetail = $this->actingAs($this->userWarga)->get(route('admin.data-warga.detail', $this->keluarga->no_kk));
        $responseDetail->assertRedirect(route('warga.beranda'));
        $responseDetail->assertSessionHas('error');
    }

    public function test_admin_dapat_melihat_form_tambah_anggota_keluarga(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.data-warga.tambah-anggota', ['no_kk' => $this->keluarga->no_kk]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.warga-tambah-anggota');
        $response->assertSee($this->keluarga->no_kk);
        $response->assertSee($this->kepala->nama);
    }

    public function test_admin_dapat_menambah_anggota_keluarga_baru_sesuai_br14_rule1(): void
    {
        $nikBaru = '3275010101990009';

        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.tambah-anggota.store'), [
            'no_kk' => $this->keluarga->no_kk,
            'nik' => $nikBaru,
            'nama' => 'Adik Baru Santoso',
            'tempat_lahir' => 'Depok',
            'tanggal_lahir' => '2010-01-01',
            'jk' => 'Laki-laki',
            'agama' => 'Islam',
            'pendidikan' => 'SD/Sederajat',
            'pekerjaan' => 'Pelajar',
            'shdk' => 'Anak',
        ]);

        $response->assertRedirect(route('admin.data-warga.detail', $this->keluarga->no_kk));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wargas', [
            'nik' => $nikBaru,
            'keluarga_id' => $this->keluarga->id,
            'status_warga' => 'hidup',
            'nama' => 'Adik Baru Santoso',
        ]);
    }

    public function test_admin_dapat_mendaftarkan_warga_pindah_kk_ke_kk_tujuan_br14_rule2(): void
    {
        $kkLain = Keluarga::create([
            'no_kk' => '3275019999990001',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        $wargaPindah = Warga::create([
            'keluarga_id' => $kkLain->id,
            'nik' => '3275017777770002',
            'nama' => 'Hendra Pratama',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-05-05',
            'jenis_kelamin' => 'laki_laki',
            'status_hubungan' => 'famili_lain',
            'status_warga' => 'pindah_kk',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.tambah-anggota.store'), [
            'no_kk' => $this->keluarga->no_kk,
            'nik' => $wargaPindah->nik,
            'nama' => 'Hendra Pratama',
            'shdk' => 'Famili Lain',
        ]);

        $response->assertRedirect(route('admin.data-warga.detail', $this->keluarga->no_kk));
        $response->assertSessionHas('success');

        $wargaPindah->refresh();
        $this->assertEquals($this->keluarga->id, $wargaPindah->keluarga_id);
        $this->assertEquals('hidup', $wargaPindah->status_warga);
    }

    public function test_admin_ditolak_menambah_anggota_jika_nik_sudah_ada_di_kk_lain_br14_rule3(): void
    {
        $kkLain = Keluarga::create([
            'no_kk' => '3275018888880003',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        $wargaLain = Warga::create([
            'keluarga_id' => $kkLain->id,
            'nik' => '3275016666660004',
            'nama' => 'Budi Sudarsono',
            'jenis_kelamin' => 'laki_laki',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga' => 'hidup',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.tambah-anggota.store'), [
            'no_kk' => $this->keluarga->no_kk,
            'nik' => $wargaLain->nik,
            'nama' => 'Budi Sudarsono',
            'shdk' => 'Famili Lain',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'NIK sudah terdaftar dalam Kartu Keluarga lain.');
    }

    public function test_admin_ditolak_menambah_anggota_jika_nik_sudah_ada_di_kk_sama_br14_rule4(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.tambah-anggota.store'), [
            'no_kk' => $this->keluarga->no_kk,
            'nik' => $this->istri->nik,
            'nama' => 'Ratna Dewi Puspita',
            'shdk' => 'Istri',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'NIK sudah terdaftar pada Kartu Keluarga ini.');
    }

    public function test_admin_dapat_melihat_form_edit_anggota(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.data-warga.edit-anggota', $this->kepala->nik));

        $response->assertStatus(200);
        $response->assertViewIs('admin.warga-edit-anggota');
        $response->assertSee($this->kepala->nama);
        $response->assertSee($this->keluarga->no_kk);
    }

    public function test_admin_dapat_mengubah_data_master_warga(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.edit-anggota.store', $this->anak->nik), [
            'nama' => 'Siti Rahmawati, S.Kom., M.T.',
            'pekerjaan' => 'Dosen Informatika',
            'jk' => 'Perempuan',
            'agama' => 'Islam',
            'pendidikan' => 'Magister (S2)',
            'shdk' => 'Anak',
            'status_keberadaan' => 'Hidup (Aktif)',
        ]);

        $response->assertRedirect(route('admin.data-warga.detail', $this->keluarga->no_kk));
        $response->assertSessionHas('success');

        $this->anak->refresh();
        $this->assertEquals('Siti Rahmawati, S.Kom., M.T.', $this->anak->nama);
        $this->assertEquals('Dosen Informatika', $this->anak->pekerjaan);
        $this->assertEquals('s2', $this->anak->pendidikan_terakhir);
    }

    public function test_transfer_akun_otomatis_br02_saat_pemilik_akun_pindah_atau_meninggal(): void
    {
        // Kondisi awal: Bambang Santoso adalah pemilik akun ($this->userWarga->username == $this->kepala->nik)
        $this->assertEquals($this->kepala->nik, $this->userWarga->username);

        // Bambang meninggal dunia atau pindah rumah
        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.edit-anggota.store', $this->kepala->nik), [
            'nama' => $this->kepala->nama,
            'shdk' => 'Kepala Keluarga',
            'status_keberadaan' => 'Meninggal',
        ]);

        $response->assertRedirect();

        // BR-02: Akun ditransfer ke Istri (Ratna Dewi)
        $this->userWarga->refresh();
        $this->assertEquals($this->istri->nik, $this->userWarga->username);

        $this->kepala->refresh();
        $this->assertNull($this->kepala->user_id);
        $this->assertEquals('meninggal', $this->kepala->status_warga);
    }

    public function test_admin_dapat_memverifikasi_pengajuan_tambah_anggota_br08(): void
    {
        $nikBaru = '3275019908080007';

        $pengajuan = Riwayat_Perubahan::create([
            'keluarga_id' => $this->keluarga->id,
            'warga_id' => null,
            'jenis_pengajuan' => 'tambah_anggota',
            'data_lama' => null,
            'data_baru' => [
                'nik' => $nikBaru,
                'nama' => 'Bayi Baru Lahir',
                'tempat_lahir' => 'Batam',
                'tanggal_lahir' => '2026-01-01',
                'jenis_kelamin' => 'laki_laki',
                'agama' => 'islam',
                'status_hubungan' => 'anak',
            ],
            'bukti_path' => 'dokumen/akta.jpg',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.verifikasi', $pengajuan->id), [
            'tindakan' => 'setujui',
            'alasan' => 'Akta kelahiran terverifikasi valid.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status);

        $this->assertDatabaseHas('wargas', [
            'nik' => $nikBaru,
            'keluarga_id' => $this->keluarga->id,
            'nama' => 'Bayi Baru Lahir',
            'status_warga' => 'hidup',
        ]);
    }

    public function test_admin_ditolak_menambah_anggota_jika_nik_mengandung_huruf_atau_bukan_16_digit(): void
    {
        // 1. NIK mengandung huruf
        $response1 = $this->actingAs($this->admin)->post(route('admin.data-warga.tambah-anggota.store'), [
            'no_kk' => $this->keluarga->no_kk,
            'nik' => '4r23f43343f3fwfw',
            'nama' => 'Warga Huruf',
            'shdk' => 'Anak',
        ]);
        $response1->assertSessionHasErrors('nik');

        // 2. NIK kurang dari 16 digit
        $response2 = $this->actingAs($this->admin)->post(route('admin.data-warga.tambah-anggota.store'), [
            'no_kk' => $this->keluarga->no_kk,
            'nik' => '12345',
            'nama' => 'Warga Pendek',
            'shdk' => 'Anak',
        ]);
        $response2->assertSessionHasErrors('nik');
    }

    public function test_admin_ditolak_mengubah_nik_jika_mengandung_huruf_atau_bukan_16_digit(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.data-warga.edit-anggota.store', $this->anak->nik), [
            'nik' => '4r23f43343f3fwfw',
            'nama' => 'Update Nama',
            'shdk' => 'Anak',
        ]);
        $response->assertSessionHasErrors('nik');
    }
}

