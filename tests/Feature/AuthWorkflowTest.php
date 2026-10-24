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
    $response->assertSessionHas('success');
    $this->assertAuthenticatedAs($this->warga);

    auth()->logout();

    $follow = $this->followingRedirects()->post(route('login.post'), [
        'username' => '3271048809920001',
        'password' => '3271048809920001',
    ]);
    $follow->assertSee('Swal.fire');
    $follow->assertSee('Selamat datang kembali');
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

it('warga berstatus pindah_kk berhasil mendaftar KK baru dan datanya terupdate sesuai BR-09', function () {
    // 1. Buat keluarga awal dan anggota berstatus pindah_kk (contoh: sang anak Raka)
    $keluargaLama = \App\Models\Keluarga::create([
        'no_kk'          => '3271048809920099',
        'status_ekonomi' => 'mampu',
        'status_tinggal' => 'tetap',
        'alamat'         => 'Jl. Melati Blok A No. 1, RT 04',
    ]);

    $wargaPindah = \App\Models\Warga::create([
        'keluarga_id'     => $keluargaLama->id,
        'user_id'         => null,
        'nik'             => '3271041807180009',
        'nama'            => 'Raka Santoso',
        'tempat_lahir'    => 'Bogor',
        'tanggal_lahir'   => '2000-07-18',
        'jenis_kelamin'   => 'laki_laki',
        'status_hubungan' => 'anak',
        'status_warga'    => 'pindah_kk',
        'alamat'          => 'Jl. Melati Blok A No. 1, RT 04',
    ]);

    // 2. Mendaftar KK baru dengan NIK yang berstatus pindah_kk
    $response = $this->post(route('register.post'), [
        'no_kk'                 => '3271049999990001',
        'status_tempat_tinggal' => 'tetap',
        'no_hp'                 => '081299998888',
        'nik'                   => '3271041807180009',
        'nama'                  => 'Raka Santoso',
        'tempat_lahir'          => 'Bogor',
        'tanggal_lahir'         => '2000-07-18',
        'jenis_kelamin'         => 'L',
        'agama'                 => 'Islam',
        'pendidikan'            => 'SMA/SMK',
        'pekerjaan'             => 'Wiraswasta',
        'alamat'                => 'Jl. Melati Baru No. 10, RT 04',
    ]);

    $response->assertRedirect(route('warga.beranda'));

    // 3. Verifikasi data terupdate dan KK baru terbentuk
    $wargaPindah->refresh();
    expect($wargaPindah->status_warga)->toBe('hidup');
    expect($wargaPindah->status_hubungan)->toBe('kepala_keluarga');
    expect($wargaPindah->alamat)->toBe('Jl. Melati Baru No. 10, RT 04');
    expect($wargaPindah->keluarga->no_kk)->toBe('3271049999990001');

    // Akun user terbentuk dan terotentikasi
    $this->assertAuthenticated();
    expect(auth()->user()->username)->toBe('3271041807180009');
});

it('warga berstatus hidup ditolak mendaftar ulang sesuai BR-09', function () {
    $keluarga = \App\Models\Keluarga::create([
        'no_kk'          => '3271048809920088',
        'status_ekonomi' => 'mampu',
        'status_tinggal' => 'tetap',
    ]);

    \App\Models\Warga::create([
        'keluarga_id'     => $keluarga->id,
        'nik'             => '3271048809920088',
        'nama'            => 'Warga Aktif',
        'jenis_kelamin'   => 'laki_laki',
        'status_warga'    => 'hidup',
        'status_hubungan' => 'kepala_keluarga',
        'alamat'          => 'Jl. Melati No. 8',
    ]);

    $response = $this->post(route('register.post'), [
        'no_kk'                 => '3271048809920089',
        'status_tempat_tinggal' => 'tetap',
        'no_hp'                 => '081299998888',
        'nik'                   => '3271048809920088',
        'nama'                  => 'Warga Aktif',
        'jenis_kelamin'         => 'L',
        'alamat'                => 'Jl. Melati No. 8',
    ]);

    $response->assertSessionHasErrors('nik');
});

it('warga berstatus meninggal ditolak mendaftar akun', function () {
    $keluarga = \App\Models\Keluarga::create([
        'no_kk'          => '3271048809920077',
        'status_ekonomi' => 'mampu',
        'status_tinggal' => 'tetap',
    ]);

    \App\Models\Warga::create([
        'keluarga_id'     => $keluarga->id,
        'nik'             => '3271048809920077',
        'nama'            => 'Almarhum',
        'jenis_kelamin'   => 'laki_laki',
        'status_warga'    => 'meninggal',
        'status_hubungan' => 'kepala_keluarga',
        'alamat'          => 'Jl. Melati No. 7',
    ]);

    $response = $this->post(route('register.post'), [
        'no_kk'                 => '3271048809920078',
        'status_tempat_tinggal' => 'tetap',
        'no_hp'                 => '081299998888',
        'nik'                   => '3271048809920077',
        'nama'                  => 'Almarhum',
        'jenis_kelamin'         => 'L',
        'alamat'                => 'Jl. Melati No. 7',
    ]);

    $response->assertSessionHasErrors('nik');
});

it('warga berstatus selain hidup ditolak saat login', function () {
    $userMeninggal = \App\Models\User::create([
        'username' => '3271048809920066',
        'password' => bcrypt('password123'),
        'role'     => 'warga',
        'no_telp'  => '081234567899',
    ]);

    $keluarga = \App\Models\Keluarga::create([
        'no_kk'          => '3271048809920066',
        'status_ekonomi' => 'mampu',
        'status_tinggal' => 'tetap',
    ]);

    \App\Models\Warga::create([
        'user_id'         => $userMeninggal->id,
        'keluarga_id'     => $keluarga->id,
        'nik'             => '3271048809920066',
        'nama'            => 'Warga Meninggal',
        'jenis_kelamin'   => 'laki_laki',
        'status_warga'    => 'meninggal',
        'status_hubungan' => 'kepala_keluarga',
        'alamat'          => 'Jl. Melati No. 6',
    ]);

    $response = $this->post(route('login.post'), [
        'username' => '3271048809920066',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

it('warga dapat memilih status hubungan dalam keluarga saat mendaftar akun', function () {
    $response = $this->get(route('register'));
    $response->assertStatus(200);
    $response->assertSee('status_hubungan');
    $response->assertSee('Status dalam Keluarga (SHDK)');

    $postRes = $this->post(route('register.post'), [
        'no_kk'                 => '3271047777770001',
        'status_tempat_tinggal' => 'tetap',
        'no_hp'                 => '081233334444',
        'nik'                   => '3271045555550002',
        'nama'                  => 'Dewi Lestari',
        'status_hubungan'       => 'istri',
        'jenis_kelamin'         => 'P',
        'agama'                 => 'Islam',
        'pendidikan'            => 'S1',
        'pekerjaan'             => 'Guru',
        'alamat'                => 'Jl. Anggrek Blok C No. 5, RT 04',
    ]);

    $postRes->assertRedirect(route('warga.beranda'));

    $warga = \App\Models\Warga::where('nik', '3271045555550002')->first();
    expect($warga)->not->toBeNull();
    expect($warga->status_hubungan)->toBe('istri');
    expect($warga->keluarga->no_kk)->toBe('3271047777770001');

    $this->assertAuthenticated();
    expect(auth()->user()->username)->toBe('3271045555550002');
});

it('dropdown pendidikan terakhir di register dan form konsisten dengan 10 opsi standar', function () {
    $response = $this->get(route('register'));
    $response->assertStatus(200);

    foreach (\App\Models\Warga::DAFTAR_PENDIDIKAN as $enumKey => $label) {
        $response->assertSee($label);
    }

    $postRes = $this->post(route('register.post'), [
        'no_kk'                 => '3271047777770003',
        'status_tempat_tinggal' => 'tetap',
        'no_hp'                 => '081233334499',
        'nik'                   => '3271045555550003',
        'nama'                  => 'Budi Santoso',
        'status_hubungan'       => 'kepala_keluarga',
        'jenis_kelamin'         => 'L',
        'agama'                 => 'Islam',
        'pendidikan'            => 'Diploma I / II',
        'pekerjaan'             => 'Teknisi',
        'alamat'                => 'Jl. Anggrek Blok C No. 6, RT 04',
    ]);

    $postRes->assertRedirect(route('warga.beranda'));

    $warga = \App\Models\Warga::where('nik', '3271045555550003')->first();
    expect($warga)->not->toBeNull();
    expect($warga->pendidikan_terakhir)->toBe('d1_d2');
});


