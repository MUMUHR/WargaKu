@extends('layouts.admin')

@section('title', 'Tambah Anggota Keluarga - Kelola Data Warga')
@section('meta_description', 'Penambahan data anggota keluarga baru langsung ke database kependudukan RT 04 oleh Admin RT.')

@section('breadcrumb')
    <a href="{{ route('admin.data-warga') }}" class="admin-topbar__breadcrumb-link">Kelola Data Warga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <a href="{{ route('admin.data-warga.detail', $keluarga->no_kk) }}" class="admin-topbar__breadcrumb-link">Detail Kartu Keluarga</a>
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
        <h1 class="admin-page-header__title">
            Tambah Anggota Keluarga Baru
            <span class="badge-admin-direct" style="background:#16a34a;color:#fff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:4px;vertical-align:middle;margin-left:8px;">MODE ADMIN LANGSUNG</span>
        </h1>
    </div>
</div>

@if(session('error'))
<div style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px;display:flex;align-items:center;gap:8px;">
    <i class='bx bx-error-circle' style="font-size:18px;"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<form action="{{ route('admin.data-warga.tambah-anggota.store') }}" method="POST" id="form-tambah-anggota">
    @csrf
    <input type="hidden" name="no_kk" value="{{ $keluarga->no_kk }}">

    {{-- Top Summary Card --}}
    <div class="form-anggota-summary-card">
        <div class="form-summary-header">
            <h3 class="form-summary-title">
                <i class='bx bx-id-card' style="color:#007bff;font-size:20px;"></i>
                <span>Formulir Anggota Keluarga Baru</span>
            </h3>
            <div class="form-summary-meta">
                No. KK: <strong>{{ $keluarga->no_kk }}</strong> • Kepala Keluarga: <strong>{{ $kepalaKeluarga->nama ?? '-' }}</strong>
            </div>
        </div>

        <div class="form-summary-grid">
            <div class="form-summary-box">
                <div class="form-summary-box__label">KEPALA KELUARGA</div>
                <div class="form-summary-box__val">
                    <i class='bx bx-user' style="color:#007bff;"></i>
                    <span>{{ $kepalaKeluarga->nama ?? '-' }}</span>
                </div>
            </div>
            <div class="form-summary-box">
                <div class="form-summary-box__label">DOMISILI RUMAH</div>
                <div class="form-summary-box__val">
                    <i class='bx bx-map-pin' style="color:#28a745;"></i>
                    <span>{{ $alamatKeluarga }}</span>
                </div>
            </div>
            <div class="form-summary-box">
                <div class="form-summary-box__label">STATUS KK / ANGGOTA</div>
                <div class="form-summary-box__val">
                    <span style="color:#28a745;">●</span> <span>{{ $totalJiwa ?? 0 }} Jiwa Terdaftar</span>
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
                            <input type="text" name="nik" class="form-control-warga" id="input-nik"
                                   placeholder="16 digit NIK baru (angka saja)"
                                   value="{{ old('nik') }}"
                                   maxlength="16" inputmode="numeric" pattern="[0-9]{16}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   required autofocus>
                            <span class="badge-input-right">16 Digit</span>
                        </div>
                        <span class="form-field-help">16 digit angka sesuai e-KTP. Hanya angka tanpa huruf atau simbol.</span>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-nama">
                            Nama Lengkap (Termasuk Gelar jika ada) <span class="req">*</span>
                        </label>
                        <input type="text" name="nama" class="form-control-warga" id="input-nama" placeholder="Contoh: M. Rayhan Pratama, S.Kom atau Siti Aisyah." value="{{ old('nama') }}" required>
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
                        <input type="text" name="tempat_lahir" class="form-control-warga" id="input-tempat" placeholder="Kota / Kabupaten tempat lahir" value="{{ old('tempat_lahir') }}" required>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-tgl">Tanggal Lahir <span class="req">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control-warga" id="input-tgl" value="{{ old('tanggal_lahir') }}" required>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-jk">Jenis Kelamin <span class="req">*</span></label>
                        <select name="jk" class="form-select-warga" id="input-jk" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('jk') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jk') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-agama">Agama <span class="req">*</span></label>
                        <select name="agama" class="form-select-warga" id="input-agama" required>
                            <option value="">-- Pilih Agama --</option>
                            <option value="Islam" {{ old('agama') === 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen Protestan" {{ old('agama') === 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                            <option value="Katolik" {{ old('agama') === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') === 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-pendidikan">Pendidikan Terakhir <span class="req">*</span></label>
                        <select name="pendidikan" class="form-select-warga" id="input-pendidikan" required>
                            <option value="">-- Pilih Pendidikan --</option>
                            <option value="Tidak / Belum Sekolah" {{ old('pendidikan') === 'Tidak / Belum Sekolah' ? 'selected' : '' }}>Tidak / Belum Sekolah</option>
                            <option value="Belum Tamat SD / Sederajat" {{ old('pendidikan') === 'Belum Tamat SD / Sederajat' ? 'selected' : '' }}>Belum Tamat SD / Sederajat</option>
                            <option value="SD / Sederajat" {{ old('pendidikan') === 'SD / Sederajat' ? 'selected' : '' }}>SD / Sederajat</option>
                            <option value="SMP / Sederajat" {{ old('pendidikan') === 'SMP / Sederajat' ? 'selected' : '' }}>SMP / Sederajat</option>
                            <option value="SLTA / SMA / SMK / Sederajat" {{ old('pendidikan') === 'SLTA / SMA / SMK / Sederajat' ? 'selected' : '' }}>SLTA / SMA / SMK / Sederajat</option>
                            <option value="Diploma I / II" {{ old('pendidikan') === 'Diploma I / II' ? 'selected' : '' }}>Diploma I / II</option>
                            <option value="Diploma III" {{ old('pendidikan') === 'Diploma III' ? 'selected' : '' }}>Diploma III</option>
                            <option value="Diploma IV / Strata 1 (S1)" {{ old('pendidikan') === 'Diploma IV / Strata 1 (S1)' ? 'selected' : '' }}>Diploma IV / Strata 1 (S1)</option>
                            <option value="Strata 2 (S2)" {{ old('pendidikan') === 'Strata 2 (S2)' ? 'selected' : '' }}>Strata 2 (S2)</option>
                            <option value="Strata 3 (S3)" {{ old('pendidikan') === 'Strata 3 (S3)' ? 'selected' : '' }}>Strata 3 (S3)</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-pekerjaan">Pekerjaan Terkini <span class="req">*</span></label>
                        <input type="text" name="pekerjaan" class="form-control-warga" id="input-pekerjaan" placeholder="Karyawan Swasta, Wiraswasta, Pelajar, dll." value="{{ old('pekerjaan') }}" required>
                    </div>
                </div>

                <div class="form-grid-2col">
                    <div class="form-field">
                        <label class="form-field-label" for="input-shdk">Status Hubungan Dalam Keluarga (SHDK) <span class="req">*</span></label>
                        <select name="shdk" class="form-select-warga" id="input-shdk" required>
                            <option value="">-- Hubungan dengan Kepala Keluarga --</option>
                            <option value="Kepala Keluarga" {{ old('shdk') === 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                            <option value="Suami" {{ old('shdk') === 'Suami' ? 'selected' : '' }}>Suami</option>
                            <option value="Istri" {{ old('shdk') === 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Anak" {{ old('shdk', 'Anak') === 'Anak' ? 'selected' : '' }}>Anak</option>
                            <option value="Menantu" {{ old('shdk') === 'Menantu' ? 'selected' : '' }}>Menantu</option>
                            <option value="Cucu" {{ old('shdk') === 'Cucu' ? 'selected' : '' }}>Cucu</option>
                            <option value="Orang Tua" {{ old('shdk') === 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                            <option value="Mertua" {{ old('shdk') === 'Mertua' ? 'selected' : '' }}>Mertua</option>
                            <option value="Famili Lain" {{ old('shdk') === 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                            <option value="Lainnya" {{ old('shdk') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <span class="form-field-help">Hubungan kependudukan terhadap Kepala Keluarga saat ini.</span>
                    </div>
                    <div class="form-field">
                        <label class="form-field-label" for="input-alamat">Alamat Domisili <span class="req">*</span></label>
                        <input type="text" name="alamat" class="form-control-warga" id="input-alamat" value="{{ old('alamat', $alamatKeluarga) }}" placeholder="Contoh: Blok B4 No. 12" required>
                        <span class="form-field-help">Contoh: Blok A No. 05 atau Blok B4 No. 12 (hanya blok &amp; nomor rumah di RT 04).</span>
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
                    Konfirmasi Penambahan: Anda akan menambahkan 1 anggota keluarga baru ke dalam Kartu Keluarga milik <strong>{{ $kepalaKeluarga->nama ?? 'Kepala Keluarga' }}</strong> (KK #{{ $keluarga->no_kk }}).
                </p>

                <button type="button" class="btn-sidebar-submit" onclick="konfirmasiTambahAnggota()">
                    <i class='bx bx-save'></i> Simpan &amp; Tambahkan Anggota
                </button>

                <a href="{{ route('admin.data-warga.detail', $keluarga->no_kk) }}" class="btn-sidebar-cancel" style="text-decoration:none;">
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

