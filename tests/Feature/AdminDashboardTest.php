<?php

use App\Enums\StatusPublikasi;
use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\Pengumuman;
use App\Models\User;
use App\Models\Warga;

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

it('admin dashboard menampilkan data riil pengumuman dan metrik statistik', function () {
    $pengumuman = Pengumuman::create([
        'judul' => 'Pengumuman Uji Nyata Dashboard',
        'isi' => 'Isi pengumuman uji nyata dashboard admin agar tidak dummy.',
        'status_publikasi' => StatusPublikasi::PUBLISH,
        'admin_id' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.beranda'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('Pengumuman RT Terbaru');
    $response->assertSee('Pengumuman Uji Nyata Dashboard');
    $response->assertSee('Publish');
    $response->assertSee(route('admin.pengumuman.edit', $pengumuman->id));

    $pengumuman->delete();
});

it('admin dashboard menampilkan rilis draft dengan badge draft yang benar', function () {
    $draft = Pengumuman::create([
        'judul' => 'Draf Rencana Pengumuman Rahasia',
        'isi' => 'Konten draf yang belum dipublish ke warga.',
        'status_publikasi' => StatusPublikasi::DRAFT,
        'admin_id' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.beranda'));

    $response->assertStatus(200);
    $response->assertSee('Draf Rencana Pengumuman Rahasia');
    $response->assertSee('Draft');
    $response->assertSee('Edit Draf');

    $draft->delete();
});
