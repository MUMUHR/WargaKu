@extends('layouts.admin')

@section('title', 'Edit Data Warga Kependudukan - Kelola Data Warga')
@section('meta_description', 'Pembaruan langsung data master warga oleh Admin RT.')

@section('breadcrumb')
    <a href="{{ route('admin.data-warga') }}" class="admin-topbar__breadcrumb-link">Kelola Data Warga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <a href="{{ route('admin.data-warga', ['view' => 'detail']) }}" class="admin-topbar__breadcrumb-link">Detail Kartu Keluarga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Edit Data Warga</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-warga.css') }}">
@endpush

@section('content')

@php
    // Mapping default data warga berdasarkan NIK jika ada
    $dataWarga = [
        '3275012304800001' => [
            'nik' => '3275012304800001',
            'nama' => 'Bambang Santoso, S.T.',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1980-04-23',
            'jk' => 'Laki-laki',
            'agama' => 'Islam',
            'pendidikan' => 'Sarjana (S1)',
            'pekerjaan' => 'Pegawai BUMN (PT PLN)',
            'status_kawin' => 'Kawin',
            'shdk' => 'Kepala Keluarga',
            'status_keberadaan' => 'Hidup (Aktif)',
        ],
        '3275016508910004' => [
            'nik' => '3275016508910004',
            'nama' => 'Ratna Dewi Puspita, M.Pd.',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1982-08-15',
            'jk' => 'Perempuan',
            'agama' => 'Islam',
            'pendidikan' => 'Magister (S2)',
            'pekerjaan' => 'Guru SMA Negeri 4',
            'status_kawin' => 'Kawin',
            'shdk' => 'Istri',
            'status_keberadaan' => 'Hidup (Aktif)',
        ],
        '3275015509010003' => [
            'nik' => '3275015509010003',
            'nama' => 'Siti Rahmawati, S.Ak.',
            'tempat_lahir' => 'Depok',
            'tanggal_lahir' => '2001-09-15',
            'jk' => 'Perempuan',
            'agama' => 'Islam',
            'pendidikan' => 'Sarjana (S1)',
            'pekerjaan' => 'Karyawan Swasta (Auditor)',
            'status_kawin' => 'Belum Kawin',
            'shdk' => 'Anak',
            'status_keberadaan' => 'Hidup (Aktif)',
        ],
    ];

    $curNik = $nik ?? '3275015509010003';
    $w = $dataWarga[$curNik] ?? $dataWarga['3275015509010003'];
@endphp

{{-- ========================================================
     HEADER HALAMAN EDIT DATA WARGA (SCREENSHOT 3)
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <nav style="font-size:12px;color:#6c757d;margin-bottom:4px;display:flex;align-items:center;gap:4px;">
            <i class='bx bx-home-alt'></i>
            <span>Beranda</span>
            <span>/</span>
            <a href="{{ route('admin.data-warga') }}" style="color:#6c757d;text-decoration:none;">Kelola Data Warga</a>
            <span>/</span>
            <a href="{{ route('admin.data-warga', ['view' => 'detail']) }}" style="color:#6c757d;text-decoration:none;">Detail Kartu Keluarga</a>
            <span>/</span>
            <span style="color:#007bff;font-weight:600;">Edit Data Warga</span>
        </nav>
        <h1 class="admin-page-header__title">
            Edit Data Warga Kependudukan
        </h1>
        <p class="admin-page-header__sub">Pembaruan langsung data master warga oleh Admin RT. Perubahan akan langsung tersimpan ke database kependudukan tanpa proses verifikasi berulang.</p>
    </div>
    <div>
        <a href="{{ route('admin.data-warga', ['view' => 'detail']) }}" class="btn-kembali-kk" style="text-decoration:none;">
            <i class='bx bx-arrow-back'></i> Kembali ke Detail KK
        </a>
    </div>
</div>

<form action="{{ route('admin.data-warga.edit-anggota.store') }}" method="POST" id="form-edit-anggota">
    @csrf

    {{-- Top Summary Card (Screenshot 3) --}}
    <div class="form-anggota-summary-card">
        <div class="form-summary-header">
            <h3 class="form-summary-title">
                <i class='bx bx-id-card' style="color:#007bff;font-size:20px;"></i>
                <span>Identitas Warga &amp; Kartu Keluarga</span>
            </h3>
            <div class="form-summary-meta">
                {{ $w['nama'] }} • NIK: <strong>{{ $w['nik'] }}</strong> • No. KK: <strong>3275010905120008</strong>
            </div>
        </div>

        <div class="form-summary-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="form-summary-box">
                <div class="form-summary-box__label">KEPALA KELUARGA</div>
                <div class="form-summary-box__val">
                    <i class='bx bx-user' style="color:#007bff;"></i>
                    <span>Bambang Santoso, S.T.</span>
                </div>
            </div>
            <div class="form-summary-box">
                <div class="form-summary-box__label">DOMISILI RUMAH</div>
                <div class="form-summary-box__val">
                    <i class='bx bx-map-pin' style="color:#28a745;"></i>
                    <span>Blok B4 No. 12 (RT 04 / RW 08)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Layout: Left Form + Right Sidebar Action --}}
    <div class="form-anggota-layout">

        {{-- Left Form Card --}}
        <div class="form-main-card">
            <div class="form-main-head">
                <h4 class="form-main-head__title">
                    <i class='bx bx-edit' style="color:#007bff;font-size:18px;"></i>
                    <span>Formulir Pembaruan Data Master</span>
                </h4>
                <span class="form-main-head__note">* Semua kolom bertanda bintang wajib diisi lengkap</span>
            </div>

            {{-- Section A --}}
            <div class="form-section-banner">
                <span class="form-section-title">
                    <i class='bx bxs-user-badge' style="color:#007bff;"></i>
                    BAGIAN A: IDENTITAS POKOK WARGA
                </span>
                <span class="form-section-sub">Nomor Identitas Kependudukan &amp; Nama</span>
            </div>
            <div class="form-section-body">
                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-nik">
                            Nomor Induk Kependudukan (NIK) <span class="req">*</span>
                        </label>
                        <div class="input-with-badge">
                            <input type="text" name="nik" class="form-control-warga" id="input-nik" value="{{ $w['nik'] }}" maxlength="16" required>
                            <span class="badge-input-right">16 Digit</span>
                        </div>
                        <span class="form-field-help">Pastikan NIK valid dan belum terdaftar pada KK lain di RT 04.</span>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-nama">
                            Nama Lengkap (Termasuk Gelar jika ada) <span class="req">*</span>
                        </label>
                        <input type="text" name="nama" class="form-control-warga" id="input-nama" value="{{ $w['nama'] }}" required>
                    </div>
                </div>
            </div>

            {{-- Section B --}}
            <div class="form-section-banner">
                <span class="form-section-title">
                    <i class='bx bx-id-card' style="color:#007bff;"></i>
                    BAGIAN B: DEMOGRAFI &amp; STATUS SIPIL
                </span>
                <span class="form-section-sub">Biodata pribadi &amp; relasi keluarga</span>
            </div>
            <div class="form-section-body">
                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-tempat">Tempat Lahir <span class="req">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control-warga" id="input-tempat" value="{{ $w['tempat_lahir'] }}" required>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-tgl">Tanggal Lahir <span class="req">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control-warga" id="input-tgl" value="{{ $w['tanggal_lahir'] }}" required>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-jk">Jenis Kelamin <span class="req">*</span></label>
                        <select name="jk" class="form-select-warga" id="input-jk" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ $w['jk'] === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $w['jk'] === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-agama">Agama <span class="req">*</span></label>
                        <select name="agama" class="form-select-warga" id="input-agama" required>
                            <option value="">-- Pilih Agama --</option>
                            <option value="Islam" {{ $w['agama'] === 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen Protestan" {{ $w['agama'] === 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                            <option value="Katolik" {{ $w['agama'] === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ $w['agama'] === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ $w['agama'] === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ $w['agama'] === 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-pendidikan">Pendidikan Terakhir <span class="req">*</span></label>
                        <select name="pendidikan" class="form-select-warga" id="input-pendidikan" required>
                            <option value="">-- Pilih Pendidikan --</option>
                            <option value="Tidak/Belum Sekolah" {{ $w['pendidikan'] === 'Tidak/Belum Sekolah' ? 'selected' : '' }}>Tidak/Belum Sekolah</option>
                            <option value="SD/Sederajat" {{ $w['pendidikan'] === 'SD/Sederajat' ? 'selected' : '' }}>SD / Sederajat</option>
                            <option value="SMP/Sederajat" {{ $w['pendidikan'] === 'SMP/Sederajat' ? 'selected' : '' }}>SMP / Sederajat</option>
                            <option value="SMA/SMK/Sederajat" {{ $w['pendidikan'] === 'SMA/SMK/Sederajat' ? 'selected' : '' }}>SMA / SMK / Sederajat</option>
                            <option value="Diploma (D3)" {{ $w['pendidikan'] === 'Diploma (D3)' ? 'selected' : '' }}>Diploma (D3)</option>
                            <option value="Sarjana (S1)" {{ $w['pendidikan'] === 'Sarjana (S1)' ? 'selected' : '' }}>Sarjana (S1)</option>
                            <option value="Magister (S2)" {{ $w['pendidikan'] === 'Magister (S2)' ? 'selected' : '' }}>Magister (S2)</option>
                            <option value="Doktor (S3)" {{ $w['pendidikan'] === 'Doktor (S3)' ? 'selected' : '' }}>Doktor (S3)</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-pekerjaan">Pekerjaan Terkini <span class="req">*</span></label>
                        <input type="text" name="pekerjaan" class="form-control-warga" id="input-pekerjaan" value="{{ $w['pekerjaan'] }}" required>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-kawin">Status Pernikahan <span class="req">*</span></label>
                        <select name="status_kawin" class="form-select-warga" id="input-kawin" required>
                            <option value="">-- Pilih Status Kawin --</option>
                            <option value="Belum Kawin" {{ $w['status_kawin'] === 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ $w['status_kawin'] === 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ $w['status_kawin'] === 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ $w['status_kawin'] === 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-shdk">Status Hubungan Dalam Keluarga (SHDK) <span class="req">*</span></label>
                        <select name="shdk" class="form-select-warga" id="input-shdk" required>
                            <option value="">-- Hubungan dengan Kepala Keluarga --</option>
                            <option value="Kepala Keluarga" {{ $w['shdk'] === 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                            <option value="Istri" {{ $w['shdk'] === 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Anak" {{ $w['shdk'] === 'Anak' ? 'selected' : '' }}>Anak</option>
                            <option value="Menantu" {{ $w['shdk'] === 'Menantu' ? 'selected' : '' }}>Menantu</option>
                            <option value="Cucu" {{ $w['shdk'] === 'Cucu' ? 'selected' : '' }}>Cucu</option>
                            <option value="Orang Tua" {{ $w['shdk'] === 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                            <option value="Mertua" {{ $w['shdk'] === 'Mertua' ? 'selected' : '' }}>Mertua</option>
                            <option value="Famili Lain" {{ $w['shdk'] === 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section C --}}
            <div class="form-section-banner">
                <span class="form-section-title">
                    <i class='bx bx-check-shield' style="color:#007bff;"></i>
                    BAGIAN C: STATUS KEAKTIFAN &amp; CATATAN PETUGAS
                </span>
                <span class="form-section-sub">Registrasi administratif lingkungan RT</span>
            </div>
            <div class="form-section-body">
                <label class="form-field-label">Status Keberadaan Kependudukan <span class="req">*</span></label>
                <div class="status-radio-grid">
                    <label class="status-radio-card active" id="radio-hidup" onclick="pilihRadio('hidup')">
                        <input type="radio" name="status_keberadaan" value="Hidup (Aktif)" checked>
                        <span>Hidup (Aktif)</span>
                    </label>
                    <label class="status-radio-card" id="radio-pindah-rumah" onclick="pilihRadio('pindah-rumah')">
                        <input type="radio" name="status_keberadaan" value="Pindah Rumah">
                        <span>Pindah Rumah</span>
                    </label>
                    <label class="status-radio-card" id="radio-pindah-kk" onclick="pilihRadio('pindah-kk')">
                        <input type="radio" name="status_keberadaan" value="Pindah KK">
                        <span>Pindah KK</span>
                    </label>
                    <label class="status-radio-card" id="radio-meninggal" onclick="pilihRadio('meninggal')">
                        <input type="radio" name="status_keberadaan" value="Meninggal">
                        <span>Meninggal</span>
                    </label>
                </div>
                <span class="form-field-help" style="margin-top:6px;">Catatan ini akan tersimpan dalam riwayat log kependudukan RT 04 untuk keperluan rekam jejak.</span>
            </div>
        </div>

        {{-- Right Sidebar Card --}}
        <div class="form-sidebar-card">
            <div class="form-sidebar-head">
                <div class="form-sidebar-title">
                    <i class='bx bx-bolt-circle'></i> AKSI ADMINISTRASI
                </div>
                <span class="badge-sidebar-status">Langsung Aktif</span>
            </div>
            <div class="form-sidebar-body">
                <p class="form-sidebar-text">
                    Konfirmasi perubahan data warga sekarang. Tanpa antrean verifikasi maupun berkas lampiran.
                </p>

                <button type="button" class="btn-sidebar-submit" onclick="konfirmasiEditAnggota()">
                    <i class='bx bx-save'></i> Simpan Perubahan Data
                </button>

                <a href="{{ route('admin.data-warga', ['view' => 'detail']) }}" class="btn-sidebar-cancel" style="text-decoration:none;">
                    <i class='bx bx-x'></i> Batal &amp; Kembali ke Detail KK
                </a>

                <div class="form-sidebar-notice">
                    <i class='bx bx-info-circle' style="color:#007bff;font-size:16px;flex-shrink:0;margin-top:1px;"></i>
                    <span>Sebagai Admin RT, Anda bertanggung jawab penuh atas keabsahan dokumen kependudukan yang ditambahkan/diperbarui.</span>
                </div>
            </div>
        </div>

    </div>
</form>

{{-- ========================================================
     MODAL POPUP KONFIRMASI EDIT DATA (SCREENSHOT USER)
     ======================================================== --}}
<div class="modal-konfirmasi-overlay" id="modal-konfirmasi-edit" onclick="if(event.target === this) tutupKonfirmasiEdit()">
    <div class="modal-konfirmasi-dialog">
        <h3 class="modal-konfirmasi-title">Apakah Kamu yakin Merubah Data Ini</h3>
        <div class="modal-konfirmasi-actions">
            <button type="button" class="btn-modal-konfirmasi-batal" onclick="tutupKonfirmasiEdit()">Batal</button>
            <button type="button" class="btn-modal-konfirmasi-submit" onclick="submitEditAnggota()">Konfirmasi</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function pilihRadio(key) {
        var map = {
            'hidup': 'radio-hidup',
            'pindah-rumah': 'radio-pindah-rumah',
            'pindah-kk': 'radio-pindah-kk',
            'meninggal': 'radio-meninggal'
        };

        for (var k in map) {
            var el = document.getElementById(map[k]);
            if (el) {
                var radio = el.querySelector('input[type="radio"]');
                if (k === key) {
                    el.classList.add('active');
                    if (radio) radio.checked = true;
                } else {
                    el.classList.remove('active');
                    if (radio) radio.checked = false;
                }
            }
        }
    }

    function konfirmasiEditAnggota() {
        var form = document.getElementById('form-edit-anggota');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        document.getElementById('modal-konfirmasi-edit').style.display = 'flex';
    }

    function tutupKonfirmasiEdit() {
        document.getElementById('modal-konfirmasi-edit').style.display = 'none';
    }

    function submitEditAnggota() {
        tutupKonfirmasiEdit();
        document.getElementById('form-edit-anggota').submit();
    }

    // Escape listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupKonfirmasiEdit();
        }
    });
</script>
@endpush
