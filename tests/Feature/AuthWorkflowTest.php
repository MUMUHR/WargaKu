<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['username' => 'adminrt'],
        [
            'password' => 'admin123',
            'role'     => 'admin',
            'no_telp'  => '081234567890',
        ]
    );

    $this->warga = User::firstOrCreate(
        ['username' => '3271048809920001'],
        [
            'password' => '3271048809920001',
            'role'     => 'warga',
            'no_telp'  => '081234567891',
        ]
    );
});

it('tamu dapat melihat form login', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertSee('Masuk ke Akun Warga');
});

it('tamu diarahkan ke login jika membuka area admin atau warga', function () {
    $responseAdmin = $this->get(route('admin.beranda'));
    $responseAdmin->assertRedirect(route('login'));

    $responseWarga = $this->get(route('warga.beranda'));
    $responseWarga->assertRedirect(route('login'));
});

it('admin berhasil login dan diarahkan ke beranda admin', function () {
    $response = $this->post(route('login.post'), [
        'username' => 'adminrt',
        'password' => 'admin123',
    ]);

    $response->assertRedirect(route('admin.beranda'));
    $this->assertAuthenticatedAs($this->admin);
});

it('warga berhasil login dan diarahkan ke beranda warga', function () {
    $response = $this->post(route('login.post'), [
        'username' => '3271048809920001',
        'password' => '3271048809920001',
    ]);

    $response->assertRedirect(route('warga.beranda'));
    $this->assertAuthenticatedAs($this->warga);
});

it('pengguna yang sudah login diarahkan ke dashboard masing-masing jika mengakses login', function () {
    $responseAdmin = $this->actingAs($this->admin)->get(route('login'));
    $responseAdmin->assertRedirect(route('admin.beranda'));

    $responseWarga = $this->actingAs($this->warga)->get(route('login'));
    $responseWarga->assertRedirect(route('warga.beranda'));
});

it('halaman landing page menampilkan menu portal dan tombol logout saat login', function () {
    $response = $this->actingAs($this->admin)->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Portal Admin RT');
    $response->assertSee('Keluar');
    $response->assertDontSee('btn--hero-login">Masuk<');
});

it('pengguna dapat melakukan logout', function () {
    $response = $this->actingAs($this->admin)->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});
