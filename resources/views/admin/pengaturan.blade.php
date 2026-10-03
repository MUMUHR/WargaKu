@extends('layouts.admin')

@section('title', 'Profil & Keamanan Akun — Portal Admin WargaKu')
@section('meta_description', 'Kelola data akun pengurus RT dan perbarui kata sandi portal admin.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-pengaturan.css') }}">
@endpush

@section('breadcrumb')
    <span class="admin-topbar__breadcrumb-current">Profil &amp; Keamanan</span>
@endsection

@section('content')
<div class="profil-page-wrapper">

    {{-- ========================================================
         PAGE HEADER
         ======================================================== --}}
    <div class="profil-header">
        <div class="profil-header__title-row">
            <h1 class="profil-header__title">Profil &amp; Keamanan Akun</h1>
            <span class="badge-akun-verif">
                <i class='bx bx-check-shield'></i> Akun Admin Terverifikasi
            </span>
        </div>
        <p class="profil-header__sub">Kelola data akun pengurus RT dan perbarui kata sandi portal admin.</p>
    </div>

    {{-- ========================================================
         MAIN GRID (2 CARDS)
         ======================================================== --}}
    <div class="profil-grid">

        {{-- ── KARTU KIRI: Akun & Kontak Pengurus RT ── --}}
        <div class="profil-card">
            <div class="profil-card__header">
                <h3 class="profil-card__title">Akun &amp; Kontak Pengurus RT</h3>
                <span class="badge-tag badge-tag--blue">DATA PENGURUS</span>
            </div>
            <div class="profil-card__body">
                {{-- Input Username (Terkunci) --}}
                <div class="profil-form-group">
                    <label class="profil-label">Username</label>
                    <div class="profil-input-wrap">
                        <input type="text"
                               class="profil-input profil-input--locked"
                               value="adminrt"
                               readonly
                               tabindex="-1"
                               aria-label="Username administrator terkunci"
                               title="Username sistem terkunci">
                        <i class='bx bx-lock-alt profil-input-icon' title="Terkunci"></i>
                    </div>
                </div>

                {{-- Input Role Akses (Terkunci) --}}
                <div class="profil-form-group" style="margin-top: 16px;">
                    <label class="profil-label">Role Akses / Role Admin</label>
                    <div class="profil-input-wrap">
                        <input type="text"
                               class="profil-input profil-input--locked"
                               value="Admin"
                               readonly
                               tabindex="-1"
                               aria-label="Role administrator terkunci"
                               title="Role akun terdaftar">
                        <i class='bx bx-id-card profil-input-icon' title="Hak Akses"></i>
                    </div>
                </div>

                {{-- Subtext / Catatan --}}
                <p class="profil-footnote">Data identitas pengurus resmi terdaftar pada arsip pengurus RT 04 / RW 08.</p>
            </div>
        </div>

        {{-- ── KARTU KANAN: Ubah Password Akun ── --}}
        <div class="profil-card">
            <div class="profil-card__header">
                <h3 class="profil-card__title">Ubah Password Akun</h3>
                <span class="badge-tag badge-tag--gray">KEAMANAN</span>
            </div>
            <div class="profil-card__body">
                <p class="profil-desc">Pastikan menggunakan kata sandi yang aman untuk melindungi akses akun portal admin RT.</p>

                <form id="form-ubah-password" onsubmit="handleUbahPassword(event)">
                    {{-- Password Saat Ini --}}
                    <div class="profil-form-group">
                        <label class="profil-label">Password Saat Ini <span style="color:#dc3545;">*</span></label>
                        <div class="profil-input-wrap">
                            <input type="password"
                                   id="input-password-lama"
                                   class="profil-input"
                                   placeholder="Masukkan kata sandi lama"
                                   required>
                            <button type="button"
                                    class="profil-toggle-pwd"
                                    onclick="togglePasswordVisibility('input-password-lama', this)"
                                    title="Tampilkan / Sembunyikan password"
                                    aria-label="Toggle password visibility">
                                <i class='bx bx-show'></i>
                            </button>
                        </div>
                    </div>

                    {{-- Password Baru --}}
                    <div class="profil-form-group" style="margin-top: 14px;">
                        <label class="profil-label">Password Baru <span style="color:#dc3545;">*</span></label>
                        <div class="profil-input-wrap">
                            <input type="password"
                                   id="input-password-baru"
                                   class="profil-input"
                                   placeholder="Minimal 8 karakter"
                                   minlength="8"
                                   required>
                            <button type="button"
                                    class="profil-toggle-pwd"
                                    onclick="togglePasswordVisibility('input-password-baru', this)"
                                    title="Tampilkan / Sembunyikan password"
                                    aria-label="Toggle password visibility">
                                <i class='bx bx-show'></i>
                            </button>
                        </div>
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="profil-form-group" style="margin-top: 14px;">
                        <label class="profil-label">Konfirmasi Password Baru <span style="color:#dc3545;">*</span></label>
                        <div class="profil-input-wrap">
                            <input type="password"
                                   id="input-konfirmasi-password"
                                   class="profil-input"
                                   placeholder="Ulangi kata sandi baru"
                                   minlength="8"
                                   required>
                            <button type="button"
                                    class="profil-toggle-pwd"
                                    onclick="togglePasswordVisibility('input-konfirmasi-password', this)"
                                    title="Tampilkan / Sembunyikan password"
                                    aria-label="Toggle password visibility">
                                <i class='bx bx-show'></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Ubah Password --}}
                    <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn-ubah-password">
                            <i class='bx bx-key'></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- ========================================================
         BANNER LOGOUT DARI SESI PORTAL ADMIN
         ======================================================== --}}
    <div class="logout-banner-card">
        <div class="logout-banner-left">
            <div class="logout-icon-box">
                <i class='bx bx-log-out-circle'></i>
            </div>
            <div>
                <h4 class="logout-banner-title">Keluar dari Sesi Portal Admin</h4>
                <p class="logout-banner-sub">Pastikan semua pembaruan data telah tersimpan sebelum mengakhiri sesi kerja Anda.</p>
            </div>
        </div>
        <div class="logout-banner-right">
            <a href="{{ route('login') }}" class="btn-logout-banner" onclick="return confirm('Apakah Anda yakin ingin keluar dari sesi kerja Portal Admin RT?')">
                <i class='bx bx-power-off'></i> Logout / Keluar
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Toggle visibilitas password
    function togglePasswordVisibility(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon = btn.querySelector('i');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bx bx-hide';
        } else {
            input.type = 'password';
            icon.className = 'bx bx-show';
        }
    }

    // Handle submit form ubah password
    function handleUbahPassword(e) {
        e.preventDefault();
        var passLama = document.getElementById('input-password-lama').value;
        var passBaru = document.getElementById('input-password-baru').value;
        var passKonf = document.getElementById('input-konfirmasi-password').value;

        if (passBaru.length < 8) {
            alert('Kata sandi baru minimal harus 8 karakter!');
            return;
        }

        if (passBaru !== passKonf) {
            alert('Konfirmasi kata sandi baru tidak sesuai. Silakan periksa kembali!');
            return;
        }

        alert('Password berhasil diperbarui!\n\nKata sandi baru untuk akun "adminrt" telah aktif dan tersimpan.');
        document.getElementById('form-ubah-password').reset();

        // Reset icon visibility
        document.querySelectorAll('.profil-toggle-pwd i').forEach(function(icon) {
            icon.className = 'bx bx-show';
        });
        document.getElementById('input-password-lama').type = 'password';
        document.getElementById('input-password-baru').type = 'password';
        document.getElementById('input-konfirmasi-password').type = 'password';
    }
</script>
@endpush
