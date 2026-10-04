@extends('layouts.admin')

@section('title', 'Tambah Anggota Keluarga - Kelola Data Warga')
@section('meta_description', 'Penambahan data anggota keluarga baru langsung ke database kependudukan RT 04 oleh Admin RT.')

@section('breadcrumb')
    <a href="{{ route('admin.data-warga') }}" class="admin-topbar__breadcrumb-link">Kelola Data Warga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <a href="{{ route('admin.data-warga', ['view' => 'detail']) }}" class="admin-topbar__breadcrumb-link">Detail Kartu Keluarga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Tambah Anggota Keluarga</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-warga.css') }}">
@endpush

@section('content')

{{-- ========================================================
     HEADER HALAMAN TAMBAH ANGGOTA KELUARGA (SCREENSHOT 2)
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
            <span style="color:#007bff;font-weight:600;">Tambah Anggota Keluarga</span>
        </nav>
        <h1 class="admin-page-header__title">
            Tambah Anggota Keluarga Baru
            <span class="badge-admin-direct" style="background:#16a34a;color:#fff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:4px;vertical-align:middle;margin-left:8px;">MODE ADMIN LANGSUNG</span>
        </h1>
        <p class="admin-page-header__sub">Penambahan data anggota keluarga baru langsung ke database kependudukan RT 04 oleh Admin RT tanpa proses verifikasi bersilang.</p>
    </div>
    <div>
        <a href="{{ route('admin.data-warga', ['view' => 'detail']) }}" class="btn-kembali-kk" style="text-decoration:none;">
            <i class='bx bx-arrow-back'></i> Kembali ke Detail KK
        </a>
    </div>
</div>

<form action="{{ route('admin.data-warga.tambah-anggota.store') }}" method="POST" id="form-tambah-anggota">
    @csrf

    {{-- Top Summary Card --}}
    <div class="form-anggota-summary-card">
        <div class="form-summary-header">
            <h3 class="form-summary-title">
                <i class='bx bx-id-card' style="color:#007bff;font-size:20px;"></i>
                <span>Formulir Anggota Keluarga Baru</span>
            </h3>
            <div class="form-summary-meta">
                No. KK: <strong>3275010905120008</strong> • Kepala Keluarga: <strong>Bambang Santoso, S.T.</strong>
            </div>
        </div>

        <div class="form-summary-grid">
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
            <div class="form-summary-box">
                <div class="form-summary-box__label">STATUS KK / ANGGOTA</div>
                <div class="form-summary-box__val">
                    <span style="color:#28a745;">●</span> <span>4 Jiwa Terdaftar</span>
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
                    <i class='bx bx-user-plus' style="color:#007bff;font-size:18px;"></i>
                    <span>Formulir Penambahan Data Warga Baru</span>
                </h4>
                <span class="form-main-head__note">* Semua kolom bertanda bintang wajib diisi lengkap</span>
            </div>

            {{-- Section A --}}
            <div class="form-section-banner">
                <span class="form-section-title">
                    <i class='bx bxs-user-badge' style="color:#007bff;"></i>
                    IDENTITAS POKOK WARGA
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
                            <input type="text" name="nik" class="form-control-warga" id="input-nik" placeholder="16 digit NIK baru" maxlength="16" required autofocus>
                            <span class="badge-input-right">16 Digit</span>
                        </div>
                        <span class="form-field-help">Pastikan NIK valid dan belum terdaftar pada KK lain di RT 04.</span>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-nama">
                            Nama Lengkap (Termasuk Gelar jika ada) <span class="req">*</span>
                        </label>
                        <input type="text" name="nama" class="form-control-warga" id="input-nama" placeholder="Contoh: M. Rayhan Pratama, S.Kom atau Siti Aisyah." required>
                    </div>
                </div>
            </div>

            {{-- Section B --}}
            <div class="form-section-banner">
                <span class="form-section-title">
                    <i class='bx bx-id-card' style="color:#007bff;"></i>
                    DEMOGRAFI &amp; STATUS SIPIL
                </span>
                <span class="form-section-sub">Biodata pribadi &amp; relasi keluarga</span>
            </div>
            <div class="form-section-body">
                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-tempat">Tempat Lahir <span class="req">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control-warga" id="input-tempat" placeholder="Kota / Kabupaten tempat lahir" required>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-tgl">Tanggal Lahir <span class="req">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control-warga" id="input-tgl" required>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-jk">Jenis Kelamin <span class="req">*</span></label>
                        <select name="jk" class="form-select-warga" id="input-jk" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-agama">Agama <span class="req">*</span></label>
                        <select name="agama" class="form-select-warga" id="input-agama" required>
                            <option value="">-- Pilih Agama --</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen Protestan">Kristen Protestan</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-pendidikan">Pendidikan Terakhir <span class="req">*</span></label>
                        <select name="pendidikan" class="form-select-warga" id="input-pendidikan" required>
                            <option value="">-- Pilih Pendidikan --</option>
                            <option value="Tidak/Belum Sekolah">Tidak/Belum Sekolah</option>
                            <option value="SD/Sederajat">SD / Sederajat</option>
                            <option value="SMP/Sederajat">SMP / Sederajat</option>
                            <option value="SMA/SMK/Sederajat">SMA / SMK / Sederajat</option>
                            <option value="Diploma (D3)">Diploma (D3)</option>
                            <option value="Sarjana (S1)">Sarjana (S1)</option>
                            <option value="Magister (S2)">Magister (S2)</option>
                            <option value="Doktor (S3)">Doktor (S3)</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-pekerjaan">Pekerjaan Terkini <span class="req">*</span></label>
                        <input type="text" name="pekerjaan" class="form-control-warga" id="input-pekerjaan" placeholder="Karyawan Swasta, Wiraswasta, Pelajar, dll." required>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-kawin">Status Pernikahan <span class="req">*</span></label>
                        <select name="status_kawin" class="form-select-warga" id="input-kawin" required>
                            <option value="">-- Pilih Status Kawin --</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-shdk">Status Hubungan Dalam Keluarga (SHDK) <span class="req">*</span></label>
                        <select name="shdk" class="form-select-warga" id="input-shdk" required>
                            <option value="">-- Hubungan dengan Kepala Keluarga --</option>
                            <option value="Kepala Keluarga">Kepala Keluarga</option>
                            <option value="Istri">Istri</option>
                            <option value="Anak" selected>Anak</option>
                            <option value="Menantu">Menantu</option>
                            <option value="Cucu">Cucu</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Mertua">Mertua</option>
                            <option value="Famili Lain">Famili Lain</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section C --}}
            <div class="form-section-banner">
                <span class="form-section-title">
                    <i class='bx bx-check-shield' style="color:#007bff;"></i>
                    STATUS KEAKTIFAN WARGA
                </span>
                <span class="form-section-sub">Status Default Sistem RT</span>
            </div>
            <div class="form-section-body">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;">
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:#1e293b;margin-bottom:2px;">Status Keberadaan Kependudukan</div>
                        <div style="font-size:11.5px;color:#64748b;">Anggota keluarga baru otomatis terdaftar dengan status kependudukan aktif di database RT 04.</div>
                    </div>
                    <span class="badge-warga-hidup">● Hidup (Aktif)</span>
                </div>
            </div>
        </div>

        {{-- Right Sidebar Card --}}
        <div class="form-sidebar-card">
            <div class="form-sidebar-head">
                <div class="form-sidebar-title">
                    <i class='bx bx-bolt-circle'></i> AKSI ADMINISTRASI
                </div>
                <span class="badge-sidebar-status">RT 04 LANGSUNG AKTIF</span>
            </div>
            <div class="form-sidebar-body">
                <p class="form-sidebar-text">
                    Konfirmasi Penambahan: Anda akan menambahkan 1 anggota keluarga baru ke dalam Kartu Keluarga milik <strong>Bambang Santoso, S.T.</strong> (KK #3275010905120008).
                </p>

                <button type="button" class="btn-sidebar-submit" onclick="konfirmasiTambahAnggota()">
                    <i class='bx bx-save'></i> Simpan &amp; Tambahkan Anggota
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
     MODAL POPUP KONFIRMASI TAMBAH DATA (SCREENSHOT USER)
     ======================================================== --}}
<div class="modal-konfirmasi-overlay" id="modal-konfirmasi-tambah" onclick="if(event.target === this) tutupKonfirmasiTambah()">
    <div class="modal-konfirmasi-dialog">
        <h3 class="modal-konfirmasi-title">Apakah Kamu yakin Menambah Data Ini</h3>
        <div class="modal-konfirmasi-actions">
            <button type="button" class="btn-modal-konfirmasi-batal" onclick="tutupKonfirmasiTambah()">Batal</button>
            <button type="button" class="btn-modal-konfirmasi-submit" onclick="submitTambahAnggota()">Konfirmasi</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function konfirmasiTambahAnggota() {
        var form = document.getElementById('form-tambah-anggota');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        document.getElementById('modal-konfirmasi-tambah').style.display = 'flex';
    }

    function tutupKonfirmasiTambah() {
        document.getElementById('modal-konfirmasi-tambah').style.display = 'none';
    }

    function submitTambahAnggota() {
        tutupKonfirmasiTambah();
        document.getElementById('form-tambah-anggota').submit();
    }

    // Escape listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupKonfirmasiTambah();
        }
    });
</script>
@endpush

