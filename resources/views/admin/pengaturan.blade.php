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
         MAIN GRID (2 CARDS)
         ======================================================== --}}
    <div class="profil-grid">

        {{-- ── KARTU KIRI: Akun & Kontak Pengurus RT ── --}}
        <div class="profil-card">
            <div class="profil-card__header">
                <h3 class="profil-card__title">Akun &amp; Kontak Pengurus RT</h3>
                <span class="badge-tag badge-tag--blue">DATA PENGURUS</span>
            </div>
            <form method="POST" action="{{ route('admin.pengaturan.kontak') }}">
                @csrf
                <div class="profil-card__body">
                    {{-- Input Username (Terkunci) --}}
                    <div class="profil-form-group">
                        <label class="profil-label">Username</label>
                        <div class="profil-input-wrap">
                            <input type="text"
                                   class="profil-input profil-input--locked"
                                   value="{{ $admin->username ?? 'adminrt' }}"
                                   readonly
                                   tabindex="-1"
                                   aria-label="Username administrator terkunci"
                                   title="Username sistem terkunci">
                            <i class='bx bx-lock-alt profil-input-icon' title="Terkunci"></i>
                        </div>
                    </div>

                    {{-- Input Role Akses (Terkunci) --}}
                    <div class="profil-form-group" style="margin-top: 14px;">
                        <label class="profil-label">Role Akses / Role Admin</label>
                        <div class="profil-input-wrap">
                            <input type="text"
                                   class="profil-input profil-input--locked"
                                   value="{{ ucfirst($admin->role ?? 'Admin') }}"
                                   readonly
                                   tabindex="-1"
                                   aria-label="Role administrator terkunci"
                                   title="Role akun terdaftar">
                            <i class='bx bx-id-card profil-input-icon' title="Hak Akses"></i>
                        </div>
                    </div>

                    {{-- Subtext / Catatan --}}
                    <p class="profil-footnote">Data identitas pengurus resmi terdaftar pada arsip pengurus RT 04 / RW 08.</p>

                    {{-- Input Nomor Telepon / WhatsApp --}}
                    <div class="profil-form-group" style="margin-top: 18px;">
                        <label class="profil-label">Nomor Telepon / WhatsApp <span style="color:#dc3545;">*</span></label>
                        <div class="profil-input-wrap">
                            <input type="text"
                                   name="no_telp"
                                   id="input-admin-no-telp"
                                   class="profil-input"
                                   value="{{ old('no_telp', $admin->no_telp ?? '') }}"
                                   placeholder="Contoh: 0812-3456-7890"
                                   required>
                            <i class='bx bx-mobile profil-input-icon' title="Nomor Kontak WhatsApp"></i>
                        </div>
                        <div style="font-size:11.5px;color:#6c757d;margin-top:4px;">Digunakan untuk menerima notifikasi sistem dan koordinasi warga.</div>
                    </div>

                    {{-- Tombol Simpan Kontak --}}
                    <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn-simpan-kontak" id="btn-simpan-kontak">
                            <i class='bx bx-save'></i> Simpan No. HP / Kontak
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ── KARTU KANAN: Ubah Password Akun ── --}}
        <div class="profil-card">
            <div class="profil-card__header">
                <h3 class="profil-card__title">Ubah Password Akun</h3>
                <span class="badge-tag badge-tag--gray">KEAMANAN</span>
            </div>
            <div class="profil-card__body">
                <p class="profil-desc">Pastikan menggunakan kata sandi yang aman untuk melindungi akses akun portal admin RT.</p>

                <form id="form-ubah-password-admin" method="POST" action="{{ route('admin.pengaturan.password') }}">
                    @csrf
                    {{-- Password Saat Ini --}}
                    <div class="profil-form-group">
                        <label class="profil-label">Password Saat Ini <span style="color:#dc3545;">*</span></label>
                        <div class="profil-input-wrap">
                            <input type="password"
                                   name="password_saat_ini"
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
                                   name="password_baru"
                                   id="input-password-baru"
                                   class="profil-input"
                                   placeholder="Minimal 5 karakter"
                                   minlength="5"
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
                                   name="konfirmasi_password"
                                   id="input-konfirmasi-password"
                                   class="profil-input"
                                   placeholder="Ulangi kata sandi baru"
                                   minlength="5"
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
                        <button type="button" class="btn-ubah-password" id="btn-trigger-ubah-password" onclick="bukaModalGantiPasswordAdmin()">
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
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout-banner" id="btn-logout-banner">
                    <i class='bx bx-power-off'></i> Logout / Keluar
                </button>
            </form>
        </div>
    </div>

</div>

{{-- ========================================================
     MODAL KONFIRMASI GANTI PASSWORD ADMIN (SESUAI MOCKUP UI)
     ======================================================== --}}
<div class="admin-modal-backdrop" id="modal-ganti-password-admin" role="dialog" aria-modal="true" aria-labelledby="modal-admin-pass-title">
    <div class="admin-modal-content">
        <h3 class="admin-modal-title" id="modal-admin-pass-title">Apakah anda yakin ingin Mengganti Passowrd?</h3>
        <p class="admin-modal-desc">
            Password tolong di catat Karena jika lupa anda harus meresetnya dari database
        </p>
        <div class="admin-modal-actions">
            <button type="button" class="btn-modal-batal" id="btn-batal-modal-pass" onclick="tutupModalGantiPasswordAdmin()">Batal</button>
            <button type="button" class="btn-modal-konfirmasi" id="btn-konfirmasi-modal-pass" onclick="eksekusiSubmitPasswordAdmin()">Konfirmasi</button>
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

    // Modal Konfirmasi Ubah Password Admin
    function bukaModalGantiPasswordAdmin() {
        var passLama = document.getElementById('input-password-lama').value;
        var passBaru = document.getElementById('input-password-baru').value;
        var passKonf = document.getElementById('input-konfirmasi-password').value;

        if (!passLama || !passBaru || !passKonf) {
            alert('Mohon isi semua field kata sandi.');
            return;
        }

        if (passBaru.length < 5) {
            alert('Kata sandi baru minimal harus 5 karakter!');
            return;
        }

        if (passBaru === passLama) {
            alert('Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
            return;
        }

        if (passBaru !== passKonf) {
            alert('Konfirmasi kata sandi baru tidak cocok dengan kata sandi baru.');
            return;
        }

        document.getElementById('modal-ganti-password-admin').style.display = 'flex';
    }

    function tutupModalGantiPasswordAdmin() {
        document.getElementById('modal-ganti-password-admin').style.display = 'none';
    }

    function eksekusiSubmitPasswordAdmin() {
        tutupModalGantiPasswordAdmin();
        document.getElementById('form-ubah-password-admin').submit();
    }

    // Tutup modal jika klik di luar box (backdrop)
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('modal-ganti-password-admin');
        if (e.target === modal) {
            tutupModalGantiPasswordAdmin();
        }
    });

    // Keyboard ESC listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalGantiPasswordAdmin();
        }
    });
</script>
@endpush
