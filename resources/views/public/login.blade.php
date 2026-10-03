<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk ke Portal Layanan Mandiri WargaKu - RT 04 / RW 08.">
    <title>Masuk ke Akun — WargaKu</title>

    {{-- CSS Vanilla terpisah --}}
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    {{-- Alpine.js dari Vite --}}
    @vite('resources/js/app.js')
</head>
<body>

<div class="auth-wrapper" x-data="{ 
    username: '', 
    password: '', 
    showPassword: false,
    fillWarga() {
        this.username = '3271048809920001';
        this.password = 'password123';
    },
    fillAdmin() {
        this.username = 'adminrt';
        this.password = 'password123';
    }
}">

    {{-- ═══════════════════════════════════════════
         KOLOM KIRI: HERO BANNER
         ═══════════════════════════════════════════ --}}
    <aside class="auth-hero" aria-label="Brand Banner WargaKu">
        <div class="auth-hero__bg">
            <img src="{{ asset('bg_hero.png') }}" alt="Pemandangan Asri WargaKu" class="auth-hero__bg-img">
        </div>
        <div class="auth-hero__overlay"></div>

        {{-- Brand Logo --}}
        <a href="{{ route('home') }}" class="auth-hero__brand" title="Kembali ke Beranda">
            <img src="{{ asset('Logo_WargaKu.png') }}" alt="Logo WargaKu" class="auth-hero__logo">
            <span class="auth-hero__brand-name">WargaKu</span>
        </a>
    </aside>

    {{-- ═══════════════════════════════════════════
         KOLOM KANAN: FORM LOGIN
         ═══════════════════════════════════════════ --}}
    <main class="auth-main" role="main">
        <div class="auth-card">

            {{-- Badge Portal Login Warga --}}
            <div class="auth-badge">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Portal Login Warga</span>
            </div>

            {{-- Header Judul & Deskripsi --}}
            <header class="auth-header">
                <h1 class="auth-title">Masuk ke Akun Warga</h1>
                <p class="auth-subtitle">Gunakan Nomor Induk Kependudukan (NIK) atau Username Anda</p>
            </header>

            {{-- Form Login --}}
            <form action="{{ route('login.post') }}" method="POST" class="auth-form">
                @csrf

                {{-- Field NIK atau Username --}}
                <div class="form-group">
                    <label for="username" class="form-label">NIK atau Username</label>
                    <div class="input-wrapper">
                        <input type="text"
                               name="username"
                               id="username"
                                 class="form-input"
                               placeholder="Contoh: 3271048809920001 atau username"
                               autocomplete="username"
                               required
                               x-model="username">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- Field Kata Sandi --}}
                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-wrapper">
                        <input :type="showPassword ? 'text' : 'password'"
                               name="password"
                               id="password"
                               class="form-input"
                               placeholder="Masukkan kata sandi akun"
                               autocomplete="current-password"
                               required
                               x-model="password">
                        <button type="button"
                                class="input-icon-btn"
                                @click="showPassword = !showPassword"
                                :title="showPassword ? 'Sembunyikan Kata Sandi' : 'Tampilkan Kata Sandi'"
                                :aria-label="showPassword ? 'Sembunyikan Kata Sandi' : 'Tampilkan Kata Sandi'">
                            {{-- Icon Mata Terbuka --}}
                            <svg x-show="!showPassword" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{-- Icon Mata Silang --}}
                            <svg x-show="showPassword" viewBox="0 0 24 24" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" class="btn--auth-submit">
                    <span>Masuk Aplikasi</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            {{-- Divider ATAU --}}
            <div class="auth-divider">
                <span>Atau</span>
            </div>

            {{-- Registrasi Akun Baru --}}
            <div class="auth-register-box">
                Belum punya akun warga?
                <a href="{{ route('register') }}">Registrasi Akun Warga Baru</a>
            </div>

            {{-- Quick Fill (Memudahkan Uji Coba Prototipe Frontend) --}}
            <div class="auth-demo-hint">
                <p class="auth-demo-hint-title">Pintasan Uji Coba Prototipe:</p>
                <div class="auth-demo-buttons">
                    <button type="button" class="auth-demo-btn" @click="fillWarga()">Isi Akun Warga</button>
                    <button type="button" class="auth-demo-btn" @click="fillAdmin()">Isi Akun Admin RT</button>
                </div>
            </div>

        </div>
    </main>

</div>

</body>
</html>
