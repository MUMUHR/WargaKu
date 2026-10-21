@extends('layouts.admin')

@section('title', 'Edit Data Warga Kependudukan - Kelola Data Warga')
@section('meta_description', 'Pembaruan langsung data master warga oleh Admin RT.')

@section('breadcrumb')
    <a href="{{ route('admin.data-warga') }}" class="admin-topbar__breadcrumb-link">Kelola Data Warga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <a href="{{ isset($keluarga) ? route('admin.data-warga.detail', $keluarga->no_kk) : route('admin.data-warga') }}" class="admin-topbar__breadcrumb-link">Detail Kartu Keluarga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Edit Data Warga</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-warga.css') }}">
@endpush

@section('content')

@php
    $curNik = $warga->nik ?? ($nik ?? '3275015509010003');
    $w = [
        'nik' => $warga->nik ?? $curNik,
        'nama' => $warga->nama ?? 'Nama Warga',
        'tempat_lahir' => $warga->tempat_lahir ?? '',
        'tanggal_lahir' => $warga?->tanggal_lahir ? $warga->tanggal_lahir->format('Y-m-d') : '',
        'jk' => (($warga->jenis_kelamin ?? 'laki_laki') === 'perempuan') ? 'Perempuan' : 'Laki-laki',
        'agama' => ucfirst($warga->agama ?? 'Islam'),
        'pendidikan' => match($warga->pendidikan_terakhir ?? '') {
            'tidak_belum_sekolah' => 'Tidak/Belum Sekolah',
            'belum_tamat_sd' => 'Belum Tamat SD',
            'sd_sederajat' => 'SD/Sederajat',
            'smp_sederajat' => 'SMP/Sederajat',
            'sma_sederajat' => 'SMA/SMK/Sederajat',
            'd1_d2' => 'Diploma I/II (D1/D2)',
            'd3' => 'Diploma (D3)',
            'd4_s1' => 'Sarjana (S1/D4)',
            's2' => 'Magister (S2)',
            's3' => 'Doktor (S3)',
            default => 'Sarjana (S1/D4)'
        },
        'pekerjaan' => $warga->pekerjaan ?? '',
        'alamat' => $warga->alamat ?? ($alamatKeluarga ?? ''),
        'shdk' => match($warga->status_hubungan ?? '') {
            'kepala_keluarga' => 'Kepala Keluarga',
            'suami' => 'Suami',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orang_tua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
            'lainnya' => 'Lainnya',
            default => 'Anak'
        },
        'status_keberadaan' => match($warga->status_warga ?? 'hidup') {
            'pindah_rumah' => 'Pindah Rumah',
            'pindah_kk' => 'Pindah KK',
            'meninggal' => 'Meninggal',
            default => 'Hidup (Aktif)'
        },
    ];

    $noKkDisplay = $keluarga->no_kk ?? '3275010905120008';
    $kepalaNamaDisplay = $kepalaKeluarga->nama ?? 'Kepala Keluarga';
    $alamatDisplay = $alamatKeluarga ?? 'RT 04 / RW 08';
@endphp

{{-- ========================================================
     HEADER HALAMAN EDIT DATA WARGA (SCREENSHOT 3)
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-header__title">
            Edit Data Warga Kependudukan
        </h1>
    </div>
</div>

@if(session('success'))
<div style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px;display:flex;align-items:center;gap:8px;">
    <i class='bx bx-check-circle' style="font-size:18px;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px;display:flex;align-items:center;gap:8px;">
    <i class='bx bx-error-circle' style="font-size:18px;"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<form action="{{ route('admin.data-warga.edit-anggota.store', $w['nik']) }}" method="POST" id="form-edit-anggota">
    @csrf

    {{-- Top Summary Card (Screenshot 3) --}}
    <div class="form-anggota-summary-card">
        <div class="form-summary-header">
            <h3 class="form-summary-title">
                <i class='bx bx-id-card' style="color:#007bff;font-size:20px;"></i>
                <span>Identitas Warga &amp; Kartu Keluarga</span>
            </h3>
            <div class="form-summary-meta">
                {{ $w['nama'] }} • NIK: <strong>{{ $w['nik'] }}</strong> • No. KK: <strong>{{ $noKkDisplay }}</strong>
            </div>
        </div>

        <div class="form-summary-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="form-summary-box">
                <div class="form-summary-box__label">KEPALA KELUARGA</div>
                <div class="form-summary-box__val">
                    <i class='bx bx-user' style="color:#007bff;"></i>
                    <span>{{ $kepalaNamaDisplay }}</span>
                </div>
            </div>
            <div class="form-summary-box">
                <div class="form-summary-box__label">DOMISILI RUMAH</div>
                <div class="form-summary-box__val">
                    <i class='bx bx-map-pin' style="color:#28a745;"></i>
                    <span>{{ $alamatDisplay }}</span>
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
                            <input type="text" name="nik" class="form-control-warga" id="input-nik"
                                   value="{{ old('nik', $w['nik']) }}"
                                   maxlength="16" inputmode="numeric" pattern="[0-9]{16}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   required>
                            <span class="badge-input-right">16 Digit</span>
                        </div>
                        <span class="form-field-help">16 digit angka sesuai e-KTP. Hanya angka tanpa huruf atau simbol.</span>
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
                            <option value="Belum Tamat SD" {{ $w['pendidikan'] === 'Belum Tamat SD' ? 'selected' : '' }}>Belum Tamat SD</option>
                            <option value="SD/Sederajat" {{ $w['pendidikan'] === 'SD/Sederajat' ? 'selected' : '' }}>SD / Sederajat</option>
                            <option value="SMP/Sederajat" {{ $w['pendidikan'] === 'SMP/Sederajat' ? 'selected' : '' }}>SMP / Sederajat</option>
                            <option value="SMA/SMK/Sederajat" {{ $w['pendidikan'] === 'SMA/SMK/Sederajat' ? 'selected' : '' }}>SMA / SMK / Sederajat</option>
                            <option value="Diploma I/II (D1/D2)" {{ $w['pendidikan'] === 'Diploma I/II (D1/D2)' ? 'selected' : '' }}>Diploma I / II (D1/D2)</option>
                            <option value="Diploma (D3)" {{ $w['pendidikan'] === 'Diploma (D3)' ? 'selected' : '' }}>Diploma (D3)</option>
                            <option value="Sarjana (S1/D4)" {{ $w['pendidikan'] === 'Sarjana (S1/D4)' ? 'selected' : '' }}>Sarjana / Diploma IV (S1/D4)</option>
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
                        <label class="form-field-label" for="input-shdk">Status Hubungan Dalam Keluarga (SHDK) <span class="req">*</span></label>
                        <select name="shdk" class="form-select-warga" id="input-shdk" required>
                            <option value="">-- Hubungan dengan Kepala Keluarga --</option>
                            <option value="Kepala Keluarga" {{ $w['shdk'] === 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                            <option value="Suami" {{ $w['shdk'] === 'Suami' ? 'selected' : '' }}>Suami</option>
                            <option value="Istri" {{ $w['shdk'] === 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Anak" {{ $w['shdk'] === 'Anak' ? 'selected' : '' }}>Anak</option>
                            <option value="Menantu" {{ $w['shdk'] === 'Menantu' ? 'selected' : '' }}>Menantu</option>
                            <option value="Cucu" {{ $w['shdk'] === 'Cucu' ? 'selected' : '' }}>Cucu</option>
                            <option value="Orang Tua" {{ $w['shdk'] === 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                            <option value="Mertua" {{ $w['shdk'] === 'Mertua' ? 'selected' : '' }}>Mertua</option>
                            <option value="Famili Lain" {{ $w['shdk'] === 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                            <option value="Lainnya" {{ $w['shdk'] === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <span class="form-field-help">Hubungan kependudukan terhadap Kepala Keluarga saat ini.</span>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-alamat">Alamat Domisili <span class="req">*</span></label>
                        <input type="text" name="alamat" class="form-control-warga" id="input-alamat" value="{{ old('alamat', $w['alamat']) }}" placeholder="Contoh: RT 04 / RW 08" required>
                        <span class="form-field-help">Alamat domisili saat ini di lingkungan RT 04.</span>
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
                    <label class="status-radio-card {{ $w['status_keberadaan'] === 'Hidup (Aktif)' ? 'active' : '' }}" id="radio-hidup" onclick="pilihRadio('hidup')">
                        <input type="radio" name="status_keberadaan" value="Hidup (Aktif)" {{ $w['status_keberadaan'] === 'Hidup (Aktif)' ? 'checked' : '' }}>
                        <span>Hidup (Aktif)</span>
                    </label>
                    <label class="status-radio-card {{ $w['status_keberadaan'] === 'Pindah Rumah' ? 'active' : '' }}" id="radio-pindah-rumah" onclick="pilihRadio('pindah-rumah')">
                        <input type="radio" name="status_keberadaan" value="Pindah Rumah" {{ $w['status_keberadaan'] === 'Pindah Rumah' ? 'checked' : '' }}>
                        <span>Pindah Rumah</span>
                    </label>
                    <label class="status-radio-card {{ $w['status_keberadaan'] === 'Pindah KK' ? 'active' : '' }}" id="radio-pindah-kk" onclick="pilihRadio('pindah-kk')">
                        <input type="radio" name="status_keberadaan" value="Pindah KK" {{ $w['status_keberadaan'] === 'Pindah KK' ? 'checked' : '' }}>
                        <span>Pindah KK</span>
                    </label>
                    <label class="status-radio-card {{ $w['status_keberadaan'] === 'Meninggal' ? 'active' : '' }}" id="radio-meninggal" onclick="pilihRadio('meninggal')">
                        <input type="radio" name="status_keberadaan" value="Meninggal" {{ $w['status_keberadaan'] === 'Meninggal' ? 'checked' : '' }}>
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

                <a href="{{ route('admin.data-warga.detail', $noKkDisplay) }}" class="btn-sidebar-cancel" style="text-decoration:none;">
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
