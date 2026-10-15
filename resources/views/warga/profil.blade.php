@extends('layouts.warga')

@section('title', 'Profil & Keamanan Akun')
@section('meta_description', 'Kelola nomor kontak aktif kepala keluarga dan perbarui kata sandi portal warga RT 04 / RW 08.')

@section('topbar_section')
    <span class="admin-topbar__breadcrumb-link">Akun Warga</span>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Profil &amp; Keamanan</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-profil.css') }}">
@endpush

@section('content')

{{-- ========================================================
     HEADER & BREADCRUMB
     ======================================================== --}}
<div class="warga-profil-header">
    <div class="warga-profil-header__left">
        <h1 class="warga-profil-header__title">Profil &amp; Keamanan Akun</h1>
        <p class="warga-profil-header__sub">Kelola nomor kontak aktif kepala keluarga dan perbarui kata sandi portal warga.</p>
    </div>
    <div>
        <span class="warga-profil-badge-verified">
            <i class='bx bx-check-circle'></i> Akun Warga Terverifikasi
        </span>
    </div>
</div>

{{-- Notification Messages --}}
@if(session('success'))
<div style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px;">
    <i class='bx bx-check-circle' style="font-size:18px;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:13px;">
    <div style="font-weight:700;display:flex;align-items:center;gap:6px;margin-bottom:4px;">
        <i class='bx bx-error-circle' style="font-size:18px;"></i>
        <span>Gagal memperbarui data akun:</span>
    </div>
    <ul style="margin:0;padding-left:20px;font-size:12.5px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ========================================================
     2-COLUMN GRID: AKUN/KONTAK & UBAH PASSWORD
     ======================================================== --}}
<div class="warga-profil-grid">

    {{-- ═══ CARD 1: AKUN & KONTAK KELUARGA ═══ --}}
    <div class="warga-profil-card warga-profil-card--blue">
        <div class="warga-profil-card__header">
            <h2 class="warga-profil-card__title">
                <i class='bx bx-id-card' style="color: #007bff;"></i>
                Akun &amp; Kontak Keluarga
            </h2>
            <span class="warga-profil-card__badge-tag">Data Warga</span>
        </div>

        <form id="form-update-kontak" class="warga-profil-card__form" method="POST" action="{{ route('warga.profil.kontak') }}">
            @csrf
            <div class="warga-profil-card__body">

                {{-- 1. Username (NIK) --}}
                <div class="warga-profil-form-group">
                    <div class="warga-profil-label-row">
                        <label class="warga-profil-label" for="profil-nik">Username (NIK)</label>
                        <span class="warga-profil-badge-nik">
                            <i class='bx bx-check'></i> NIK Terdaftar
                        </span>
                    </div>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-id-card'></i>
                        </span>
                        <input type="text" id="profil-nik" class="warga-profil-input" value="{{ $user->username }}" readonly disabled>
                    </div>
                </div>

                {{-- 2. Nama Kepala Keluarga --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-nama">Nama Kepala Keluarga</label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-user'></i>
                        </span>
                        <input type="text" id="profil-nama" class="warga-profil-input" value="{{ $kepala?->nama ?? 'Kepala Keluarga' }}" readonly disabled>
                    </div>
                </div>

                {{-- 3. No. Kartu Keluarga (KK) --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-nokk">No. Kartu Keluarga (KK)</label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-group'></i>
                        </span>
                        <input type="text" id="profil-nokk" class="warga-profil-input" value="{{ $keluarga?->no_kk ?? '-' }}" readonly disabled>
                    </div>
                    <div class="warga-profil-help-text">
                        Data identitas pokok sesuai arsip kependudukan RT 04 / RW 08. Perubahan identitas dilakukan melalui menu <a href="{{ route('warga.keluarga') }}">Pengajuan Surat &amp; Data Warga</a>.
                    </div>
                </div>

                {{-- 4. Nomor Telepon / WhatsApp --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-telepon">
                        Nomor Telepon / WhatsApp <span class="req">*</span>
                    </label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-phone'></i>
                        </span>
                        <input type="text" name="no_telp" id="profil-telepon" class="warga-profil-input" value="{{ old('no_telp', $user->no_telp ?? '') }}" required placeholder="Contoh: 0812-3456-7890">
                    </div>
                    <div class="warga-profil-help-text">
                        Digunakan untuk menerima notifikasi sistem dan informasi iuran RT.
                    </div>
                </div>

            </div>

            <div class="warga-profil-card__footer">
                <button type="submit" class="btn-profil-action" id="btn-simpan-kontak">
                    <i class='bx bx-save'></i> Simpan No. HP
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ CARD 2: UBAH PASSWORD AKUN ═══ --}}
    <div class="warga-profil-card warga-profil-card--green">
        <div class="warga-profil-card__header">
            <h2 class="warga-profil-card__title">
                <i class='bx bx-history' style="color: #28a745;"></i>
                Ubah Password Akun
            </h2>
            <span class="warga-profil-card__badge-tag">Keamanan</span>
        </div>

        <form id="form-update-password" class="warga-profil-card__form" method="POST" action="{{ route('warga.profil.password') }}">
            @csrf
            <div class="warga-profil-card__body">
                <p class="warga-profil-card__subtitle">
                    Pastikan menggunakan kata sandi yang aman untuk melindungi akses akun portal Anda.
                </p>

                {{-- 1. Password Saat Ini --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-pass-lama">
                        Password Saat Ini <span class="req">*</span>
                    </label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-lock-alt'></i>
                        </span>
                        <input type="password" name="password_saat_ini" id="profil-pass-lama" class="warga-profil-input" placeholder="Masukkan kata sandi lama" required>
                    </div>
                </div>

                {{-- 2. Password Baru --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-pass-baru">
                        Password Baru <span class="req">*</span>
                    </label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-dots-horizontal-rounded'></i>
                        </span>
                        <input type="password" name="password_baru" id="profil-pass-baru" class="warga-profil-input" placeholder="Minimal 5 karakter" required minlength="5">
                    </div>
                </div>

                {{-- 3. Konfirmasi Password Baru --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-pass-konfirmasi">
                        Konfirmasi Password Baru <span class="req">*</span>
                    </label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-check-circle'></i>
                        </span>
                        <input type="password" name="konfirmasi_password" id="profil-pass-konfirmasi" class="warga-profil-input" placeholder="Ulangi kata sandi baru" required minlength="5">
                    </div>
                </div>

            </div>

            <div class="warga-profil-card__footer">
                <button type="button" class="btn-profil-action" id="btn-submit-password" onclick="bukaModalGantiPassword()">
                    <i class='bx bx-shield-quarter'></i> Ubah Password
                </button>
            </div>
        </form>
    </div>

</div>

{{-- ========================================================
     CARD 3: KELUAR DARI SESI PORTAL (FULL WIDTH)
     ======================================================== --}}
<div class="warga-profil-logout-card">
    <div class="warga-profil-logout-left">
        <div class="warga-profil-logout-iconbox">
            <i class='bx bx-log-out-circle'></i>
        </div>
        <div>
            <h3 class="warga-profil-logout-title">Keluar dari Sesi Portal</h3>
            <p class="warga-profil-logout-sub">Anda akan keluar dari sesi portal WargaKu pada perangkat ini.</p>
        </div>
    </div>
    <div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;"
              onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sesi kerja Portal Warga?')">
            @csrf
            <button type="submit" class="btn-profil-logout-outline" id="btn-logout-card">
                <i class='bx bx-log-out'></i> Logout / Keluar
            </button>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL KONFIRMASI UBAH PASSWORD (PERSIS SCREENSHOT 2)
     ======================================================== --}}
<div class="profil-modal-backdrop" id="modal-konfirmasi-password" role="dialog" aria-modal="true" aria-labelledby="modal-pass-title">
    <div class="profil-modal-content">
        <h3 class="profil-modal-title" id="modal-pass-title">Apakah Anda yakin ingin mengganti kata sandi?</h3>
        <p class="profil-modal-desc">
            Harap catat kata sandi baru Anda dengan baik. Jika lupa, Anda harus menghubungi Pengurus RT untuk mereset kata sandi akun KK.
        </p>
        <div class="profil-modal-actions">
            <button type="button" class="btn-profil-modal-batal" onclick="tutupModalGantiPassword()">Batal</button>
            <button type="button" class="btn-profil-modal-konfirmasi" onclick="eksekusiGantiPassword()">Konfirmasi</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Modal Konfirmasi Ubah Password Warga
    function bukaModalGantiPassword() {
        var passLama = document.getElementById('profil-pass-lama').value;
        var passBaru = document.getElementById('profil-pass-baru').value;
        var passKonfirm = document.getElementById('profil-pass-konfirmasi').value;

        if (!passLama || !passBaru || !passKonfirm) {
            alert('Mohon isi semua field kata sandi.');
            return;
        }

        if (passBaru.length < 5) {
            alert('Kata sandi baru minimal 5 karakter.');
            return;
        }

        if (passBaru === passLama) {
            alert('Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
            return;
        }

        if (passBaru !== passKonfirm) {
            alert('Konfirmasi kata sandi baru tidak cocok dengan kata sandi baru.');
            return;
        }

        document.getElementById('modal-konfirmasi-password').style.display = 'flex';
    }

    function tutupModalGantiPassword() {
        document.getElementById('modal-konfirmasi-password').style.display = 'none';
    }

    function eksekusiGantiPassword() {
        tutupModalGantiPassword();
        document.getElementById('form-update-password').submit();
    }

    // Tutup modal jika klik di backdrop
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('modal-konfirmasi-password');
        if (e.target === modal) {
            tutupModalGantiPassword();
        }
    });

    // Keyboard ESC listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalGantiPassword();
        }
    });
</script>
@endpush
