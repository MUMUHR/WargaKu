<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $userWarga;
    private Keluarga $keluarga;
    private Warga $kepalaWarga;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Buat User Admin
        $this->admin = User::factory()->create([
            'username' => 'adminrt',
            'role'     => 'admin',
            'no_telp'  => '0812-3456-7890',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat User Warga & Keluarga
        $this->keluarga = Keluarga::create([
            'no_kk'          => '3276020101850001',
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]);

        $this->userWarga = User::factory()->create([
            'username' => '3276021204850001',
            'role'     => 'warga',
            'no_telp'  => '0813-9876-5432',
            'password' => Hash::make('passwordWarga123'),
        ]);

        $this->kepalaWarga = Warga::create([
            'user_id'             => $this->userWarga->id,
            'keluarga_id'         => $this->keluarga->id,
            'nik'                 => '3276021204850001',
            'nama'                => 'Bpk. Agus Supriyanto',
            'tempat_lahir'        => 'Jakarta',
            'tanggal_lahir'       => '1985-04-12',
            'jenis_kelamin'       => 'laki_laki',
            'agama'               => 'islam',
            'pendidikan_terakhir' => 'd4_s1',
            'pekerjaan'           => 'Wiraswasta',
            'alamat'              => 'Jl. Melati Blok B1 No. 5',
            'status_hubungan'     => 'kepala_keluarga',
            'status_warga'        => 'hidup',
        ]);
    }

    public function test_admin_dapat_melihat_halaman_profil_dan_keamanan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pengaturan'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pengaturan');
        $response->assertSee('Profil &amp; Keamanan Akun', false);
        $response->assertSee('Akun Admin Terverifikasi');
        $response->assertSee('adminrt');
        $response->assertSee('Admin');
        $response->assertSee('0812-3456-7890');
        $response->assertSee('Ubah Password Akun');
        $response->assertSee('Keluar dari Sesi Portal Admin');
        $response->assertSee('Apakah anda yakin ingin Mengganti Passowrd?');
    }

    public function test_admin_dapat_memperbarui_nomor_telepon_kontak(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan.kontak'), [
            'no_telp' => '0812-9999-8888',
        ]);

        $response->assertRedirect(route('admin.pengaturan'));
        $response->assertSessionHas('success');

        $this->assertEquals('0812-9999-8888', $this->admin->fresh()->no_telp);
    }

    public function test_admin_gagal_memperbarui_kontak_jika_format_tidak_valid(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan.kontak'), [
            'no_telp' => 'nomor_tidak_valid_abc',
        ]);

        $response->assertSessionHasErrors(['no_telp']);
        $this->assertEquals('0812-3456-7890', $this->admin->fresh()->no_telp);
    }

    public function test_admin_dapat_memperbarui_password_dengan_verifikasi_password_lama(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan.password'), [
            'password_saat_ini'   => 'password123',
            'password_baru'       => 'newpasswordadmin123',
            'konfirmasi_password' => 'newpasswordadmin123',
        ]);

        $response->assertRedirect(route('admin.pengaturan'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpasswordadmin123', $this->admin->fresh()->password));
    }

    public function test_admin_gagal_ubah_password_jika_password_lama_salah(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan.password'), [
            'password_saat_ini'   => 'passwordsalah',
            'password_baru'       => 'newpasswordadmin123',
            'konfirmasi_password' => 'newpasswordadmin123',
        ]);

        $response->assertSessionHasErrors(['password_saat_ini']);
        $this->assertTrue(Hash::check('password123', $this->admin->fresh()->password));
    }

    public function test_admin_gagal_ubah_password_jika_konfirmasi_tidak_cocok(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan.password'), [
            'password_saat_ini'   => 'password123',
            'password_baru'       => 'newpasswordadmin123',
            'konfirmasi_password' => 'berbedapassword123',
        ]);

        $response->assertSessionHasErrors(['konfirmasi_password']);
        $this->assertTrue(Hash::check('password123', $this->admin->fresh()->password));
    }

    public function test_warga_dapat_melihat_halaman_profil_dan_keamanan_dengan_data_riil(): void
    {
        $response = $this->actingAs($this->userWarga)->get(route('warga.profil'));

        $response->assertStatus(200);
        $response->assertViewIs('warga.profil');
        $response->assertSee('Profil &amp; Keamanan Akun', false);
        $response->assertSee('3276021204850001'); // NIK
        $response->assertSee('Bpk. Agus Supriyanto'); // Nama Kepala Keluarga
        $response->assertSee('3276020101850001'); // No KK
        $response->assertSee('0813-9876-5432'); // No Telepon
    }

    public function test_warga_dapat_memperbarui_nomor_telepon_dan_password(): void
    {
        // Update Kontak
        $respKontak = $this->actingAs($this->userWarga)->post(route('warga.profil.kontak'), [
            'no_telp' => '0857-1111-2222',
        ]);
        $respKontak->assertRedirect(route('warga.profil'));
        $respKontak->assertSessionHas('success');
        $this->assertEquals('0857-1111-2222', $this->userWarga->fresh()->no_telp);

        // Update Password
        $respPass = $this->actingAs($this->userWarga)->post(route('warga.profil.password'), [
            'password_saat_ini'   => 'passwordWarga123',
            'password_baru'       => 'wargabaru12345',
            'konfirmasi_password' => 'wargabaru12345',
        ]);
        $respPass->assertRedirect(route('warga.profil'));
        $respPass->assertSessionHas('success');
        $this->assertTrue(Hash::check('wargabaru12345', $this->userWarga->fresh()->password));
    }

    public function test_tamu_tidak_dapat_mengakses_profil_admin_maupun_warga(): void
    {
        $respAdmin = $this->get(route('admin.pengaturan'));
        $respAdmin->assertRedirect(route('login'));

        $respWarga = $this->get(route('warga.profil'));
        $respWarga->assertRedirect(route('login'));
    }
}
