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
        <nav class="warga-profil-header__breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('warga.beranda') }}">Beranda</a>
            <span class="sep">/</span>
            <span>Akun Warga</span>
            <span class="sep">/</span>
            <span style="color: #007bff; font-weight: 600;">Profil &amp; Keamanan</span>
        </nav>
        <h1 class="warga-profil-header__title">Profil &amp; Keamanan Akun</h1>
        <p class="warga-profil-header__sub">Kelola nomor kontak aktif kepala keluarga dan perbarui kata sandi portal warga.</p>
    </div>
    <div>
        <span class="warga-profil-badge-verified">
            <i class='bx bx-check-circle'></i> Akun Warga Terverifikasi
        </span>
    </div>
</div>

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

        <form id="form-update-kontak" class="warga-profil-card__form" onsubmit="simpanNomorHp(event)">
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
                        <input type="text" id="profil-nik" class="warga-profil-input" value="3271041205800003" readonly disabled>
                    </div>
                </div>

                {{-- 2. Nama Kepala Keluarga --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-nama">Nama Kepala Keluarga</label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-user'></i>
                        </span>
                        <input type="text" id="profil-nama" class="warga-profil-input" value="Bpk. Bambang Pamungkas" readonly disabled>
                    </div>
                </div>

                {{-- 3. No. Kartu Keluarga (KK) --}}
                <div class="warga-profil-form-group">
                    <label class="warga-profil-label" for="profil-nokk">No. Kartu Keluarga (KK)</label>
                    <div class="warga-profil-input-group">
                        <span class="warga-profil-input-icon">
                            <i class='bx bx-group'></i>
                        </span>
                        <input type="text" id="profil-nokk" class="warga-profil-input" value="3271048809920001" readonly disabled>
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
                        <input type="text" id="profil-telepon" class="warga-profil-input" value="0812-9876-5432" required placeholder="Contoh: 0812-3456-7890">
                    </div>
                    <div class="warga-profil-help-text">
                        Digunakan untuk menerima kode verifikasi OTP dan informasi iuran RT.
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

        <form id="form-update-password" class="warga-profil-card__form" onsubmit="bukaModalGantiPassword(event)">
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
                        <input type="password" id="profil-pass-lama" class="warga-profil-input" placeholder="Masukkan kata sandi lama" required>
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
                        <input type="password" id="profil-pass-baru" class="warga-profil-input" placeholder="Minimal 8 karakter" required minlength="8">
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
                        <input type="password" id="profil-pass-konfirmasi" class="warga-profil-input" placeholder="Ulangi kata sandi baru" required minlength="8">
                    </div>
                </div>

            </div>

            <div class="warga-profil-card__footer">
                <button type="submit" class="btn-profil-action" id="btn-submit-password">
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
        <a href="{{ route('login') }}" class="btn-profil-logout-outline" id="btn-logout-card">
            <i class='bx bx-log-out'></i> Logout / Keluar
        </a>
    </div>
</div>

{{-- ========================================================
     MODAL KONFIRMASI UBAH PASSWORD (PERSIS SCREENSHOT 2)
     ======================================================== --}}
<div class="profil-modal-backdrop" id="modal-konfirmasi-password" role="dialog" aria-modal="true" aria-labelledby="modal-pass-title">
    <div class="profil-modal-content">
        <h3 class="profil-modal-title" id="modal-pass-title">Apakah anda yakin ingin Mengganti Passowrd?</h3>
        <p class="profil-modal-desc">
            Password tolong di catat Karena jika lupa anda harus menghubungi admin untuk meresetnya dari admin
        </p>
        <div class="profil-modal-actions">
            <button type="button" class="btn-profil-modal-batal" onclick="tutupModalGantiPassword()">Batal</button>
            <button type="button" class="btn-profil-modal-konfirmasi" onclick="eksekusiGantiPassword()">Konfirmasi</button>
        </div>
    </div>
</div>

{{-- Toast Alert Notifikasi --}}
<div class="profil-toast-alert" id="profil-toast">
    <i class='bx bx-check-circle' style="font-size: 20px;"></i>
    <span id="profil-toast-msg">Perubahan berhasil disimpan!</span>
</div>

@endsection

@push('scripts')
<script>
    // Simpan Nomor HP
    function simpanNomorHp(e) {
        e.preventDefault();
        var noHp = document.getElementById('profil-telepon').value.trim();
        if (!noHp) {
            alert('Silakan masukkan nomor telepon / WhatsApp aktif.');
            return;
        }

        tampilkanToast('Nomor Telepon / WhatsApp berhasil diperbarui!');
    }

    // Modal Konfirmasi Ubah Password
    function bukaModalGantiPassword(e) {
        e.preventDefault();
        var passLama = document.getElementById('profil-pass-lama').value;
        var passBaru = document.getElementById('profil-pass-baru').value;
        var passKonfirm = document.getElementById('profil-pass-konfirmasi').value;

        if (!passLama || !passBaru || !passKonfirm) {
            alert('Mohon isi semua field kata sandi.');
            return;
        }

        if (passBaru.length < 8) {
            alert('Kata sandi baru minimal 8 karakter.');
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

        // Reset input fields
        document.getElementById('profil-pass-lama').value = '';
        document.getElementById('profil-pass-baru').value = '';
        document.getElementById('profil-pass-konfirmasi').value = '';

        tampilkanToast('Kata sandi berhasil diganti! Harap catat kata sandi baru Anda.');
    }

    // Toast helper
    function tampilkanToast(pesan) {
        var toast = document.getElementById('profil-toast');
        var msg = document.getElementById('profil-toast-msg');
        msg.textContent = pesan;
        toast.style.display = 'flex';

        setTimeout(function() {
            toast.style.display = 'none';
        }, 3500);
    }

    // Tutup modal jika klik di backdrop
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('modal-konfirmasi-password');
        if (e.target === modal) {
            tutupModalGantiPassword();
        }
    });
</script>
@endpush
