<?php

use App\Enums\StatusSurat;
use App\Models\Keluarga;
use App\Models\Surat;
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

    $this->keluarga = Keluarga::firstOrCreate(
        ['no_kk' => '3271048809920001'],
        [
            'status_ekonomi' => 'mampu',
            'status_tinggal' => 'tetap',
        ]
    );

    $this->pemohon = Warga::firstOrCreate(
        ['nik' => '3271048809920001'],
        [
            'keluarga_id'     => $this->keluarga->id,
            'nama'            => 'Budi Santoso',
            'jenis_kelamin'   => 'laki_laki',
            'status_hubungan' => 'kepala_keluarga',
            'status_warga'    => 'hidup',
            'alamat'          => 'Jl. Melati Blok A No. 1',
        ]
    );

    $this->wargaTujuan = Warga::firstOrCreate(
        ['nik' => '3271044502950002'],
        [
            'keluarga_id'     => $this->keluarga->id,
            'nama'            => 'Siti Aminah',
            'jenis_kelamin'   => 'perempuan',
            'status_hubungan' => 'istri',
            'status_warga'    => 'hidup',
            'alamat'          => 'Jl. Melati Blok A No. 1',
        ]
    );
});

it('admin dapat melihat halaman kelola surat dengan data statistik dan daftar pengajuan', function () {
    $surat = Surat::create([
        'pemohon_id'      => $this->pemohon->id,
        'warga_tujuan_id' => $this->wargaTujuan->id,
        'keperluan'       => 'Permohonan Pengantar SKCK untuk Uji Nyata Admin',
        'status'          => StatusSurat::MENUNGGU,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.surat'));

    $response->assertStatus(200);
    $response->assertSee('Proses Pengajuan Surat Pengantar Warga');
    $response->assertSee('MENUNGGU VERIFIKASI');
    $response->assertSee('Permohonan Pengantar SKCK untuk Uji Nyata Admin');
    $response->assertSee($this->pemohon->nama);
    $response->assertSee($this->wargaTujuan->nama);

    $surat->delete();
});

it('admin dapat membuka halaman preview surat dengan biodata lengkap warga tujuan', function () {
    $surat = Surat::create([
        'pemohon_id'      => $this->pemohon->id,
        'warga_tujuan_id' => $this->wargaTujuan->id,
        'keperluan'       => 'Surat Keterangan Domisili Tempat Tinggal Sementara',
        'status'          => StatusSurat::MENUNGGU,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.surat.preview', $surat->id));

    $response->assertStatus(200);
    $response->assertSee('SURAT PENGANTAR');
    $response->assertSee($this->wargaTujuan->nama);
    $response->assertSee($this->wargaTujuan->nik);
    $response->assertSee('Surat Keterangan Domisili Tempat Tinggal Sementara');

    $surat->delete();
});

it('admin dapat membuka preview surat seluruh data di database tanpa error format tanggal', function () {
    $surat = Surat::create([
        'pemohon_id'      => $this->pemohon->id,
        'warga_tujuan_id' => $this->wargaTujuan->id,
        'keperluan'       => 'Pengujian Preview Tanggal Lahir String',
        'status'          => StatusSurat::MENUNGGU,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.surat.preview', $surat->id));
    $response->assertStatus(200);
    $response->assertSee('SURAT PENGANTAR');

    $surat->delete();
});

it('admin dapat menyetujui surat dan menghasilkan nomor surat sesuai BR-05', function () {
    $surat = Surat::create([
        'pemohon_id'      => $this->pemohon->id,
        'warga_tujuan_id' => $this->wargaTujuan->id,
        'keperluan'       => 'Pengantar Penerbitan KTP Baru',
        'status'          => StatusSurat::MENUNGGU,
    ]);

    $response = $this->actingAs($this->admin)->post(route('admin.surat.setujui', $surat->id));

    $response->assertRedirect(route('admin.surat'));
    $response->assertSessionHas('success');

    $surat->refresh();
    expect($surat->status)->toBe(StatusSurat::DISETUJUI);
    expect($surat->nomor_surat)->not->toBeNull();
    // Cek format NNN/SP/RT04/RW08/{bulan_romawi}/{tahun}
    expect($surat->nomor_surat)->toMatch('/^\d{3}\/SP\/RT04\/RW08\/[IVXLCDM]+\/\d{4}$/');

    $surat->delete();
});

it('admin dapat menolak surat dengan menyimpan alasan penolakan', function () {
    $surat = Surat::create([
        'pemohon_id'      => $this->pemohon->id,
        'warga_tujuan_id' => $this->wargaTujuan->id,
        'keperluan'       => 'Pengantar Izin Keramaian Lingkungan',
        'status'          => StatusSurat::MENUNGGU,
    ]);

    $alasanTolak = 'Persyaratan surat pernyataan persetujuan tetangga belum dilampirkan.';

    $response = $this->actingAs($this->admin)->post(route('admin.surat.tolak', $surat->id), [
        'alasan' => $alasanTolak,
    ]);

    $response->assertRedirect(route('admin.surat'));
    $response->assertSessionHas('info');

    $surat->refresh();
    expect($surat->status)->toBe(StatusSurat::DITOLAK);
    expect($surat->alasan)->toBe($alasanTolak);

    $surat->delete();
});

it('surat yang sudah disetujui tidak dapat disetujui ulang atau ditolak', function () {
    $surat = Surat::create([
        'pemohon_id'      => $this->pemohon->id,
        'warga_tujuan_id' => $this->wargaTujuan->id,
        'keperluan'       => 'Pengantar Nikah',
        'nomor_surat'     => '999/SP/RT04/RW08/II/2026',
        'status'          => StatusSurat::DISETUJUI,
    ]);

    $responseApprove = $this->actingAs($this->admin)->post(route('admin.surat.setujui', $surat->id));
    $responseApprove->assertSessionHas('error');

    $responseReject = $this->actingAs($this->admin)->post(route('admin.surat.tolak', $surat->id), [
        'alasan' => 'Ditolak belakangan',
    ]);
    $responseReject->assertSessionHas('error');

    $surat->refresh();
    expect($surat->status)->toBe(StatusSurat::DISETUJUI);

    $surat->delete();
});
