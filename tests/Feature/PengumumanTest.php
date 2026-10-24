<?php

use App\Enums\StatusPublikasi;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['username' => 'adminrt'],
        [
            'password' => bcrypt('admin123'),
            'role'     => 'admin',
            'no_telp'  => '081234567890',
        ]
    );
});

it('menampilkan pengumuman yang dipublish di halaman landing page', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Pengumuman Terkini');
});

it('menampilkan daftar pengumuman di halaman publik', function () {
    $response = $this->get(route('pengumuman.index'));

    $response->assertStatus(200);
    $response->assertSee('Pengumuman RT');
});

it('menampilkan detail pengumuman yang berstatus publish', function () {
    $pengumuman = Pengumuman::firstOrCreate(
        ['judul' => 'Pengumuman Detail Testing'],
        [
            'isi' => 'Konten detail untuk testing.',
            'status_publikasi' => StatusPublikasi::PUBLISH,
            'admin_id' => $this->admin->id,
        ]
    );

    $response = $this->get(route('pengumuman.show', $pengumuman->id));
    $response->assertStatus(200);
    $response->assertSee($pengumuman->judul);
});

it('admin dapat melihat halaman kelola pengumuman', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.pengumuman'));

    $response->assertStatus(200);
    $response->assertSee('Kelola Pengumuman');
});

it('admin dapat membuka form tambah pengumuman', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.pengumuman.create'));

    $response->assertStatus(200);
    $response->assertSee('Form Tambah Pengumuman RT');
});

it('admin dapat menyimpan pengumuman baru', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('banner.jpg');

    $data = [
        'judul'            => 'Tes Pengumuman Baru ' . uniqid(),
        'isi'              => 'Konten pengumuman untuk keperluan testing integrasi sistem.',
        'gambar'           => $file,
        'status_publikasi' => StatusPublikasi::PUBLISH->value,
    ];

    $response = $this->actingAs($this->admin)
        ->post(route('admin.pengumuman.store'), $data);

    $response->assertRedirect(route('admin.pengumuman'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('pengumumen', [
        'judul' => $data['judul'],
    ]);
});

it('admin dapat mengedit dan mengupdate pengumuman', function () {
    $pengumuman = Pengumuman::create([
        'judul'            => 'Pengumuman Edit Awal ' . uniqid(),
        'isi'              => 'Isi awal sebelum edit.',
        'status_publikasi' => StatusPublikasi::PUBLISH,
        'admin_id'         => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.pengumuman.edit', $pengumuman->id));

    $response->assertStatus(200);
    $response->assertSee('Form Edit Pengumuman RT');

    $updateData = [
        'judul'            => 'Judul Diperbarui ' . uniqid(),
        'isi'              => 'Konten diperbarui dengan sukses.',
        'status_publikasi' => StatusPublikasi::PUBLISH->value,
    ];

    $updateResponse = $this->actingAs($this->admin)
        ->post(route('admin.pengumuman.update', $pengumuman->id), $updateData);

    $updateResponse->assertRedirect(route('admin.pengumuman'));
    $this->assertDatabaseHas('pengumumen', [
        'id'    => $pengumuman->id,
        'judul' => $updateData['judul'],
    ]);
});

it('admin dapat menghapus pengumuman', function () {
    $toDelete = Pengumuman::create([
        'judul'            => 'Pengumuman Dihapus ' . uniqid(),
        'isi'              => 'Akan segera dihapus.',
        'status_publikasi' => StatusPublikasi::DRAFT,
        'admin_id'         => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.pengumuman.destroy', $toDelete->id));

    $response->assertRedirect(route('admin.pengumuman'));
    $this->assertDatabaseMissing('pengumumen', [
        'id' => $toDelete->id,
    ]);
});

it('dashboard admin tidak menampilkan thumbnail jika pengumuman dibuat tanpa thumbnail', function () {
    Pengumuman::truncate();

    $tanpaFoto = Pengumuman::create([
        'judul'            => 'Pengumuman Tanpa Gambar ' . uniqid(),
        'isi'              => 'Konten tanpa berkas gambar thumbnail.',
        'gambar'           => null,
        'status_publikasi' => StatusPublikasi::PUBLISH,
        'admin_id'         => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.beranda'));
    $response->assertStatus(200);
    $response->assertSee($tanpaFoto->judul);
    $response->assertDontSee('dash-pengumuman-thumb');
    $response->assertDontSee('bg_hero.png');
});

