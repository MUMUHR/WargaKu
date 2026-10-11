<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaKu - Aplikasi Informasi dan Layanan Mandiri Warga RT 04 / RW 08. Transparan, aman, dan terpercaya.')">
    <title>@yield('title', 'WargaKu') — Layanan Warga RT 04 / RW 08</title>

    {{-- CSS Vanilla WargaKu (jangan muat resources/css/app.css / Tailwind) --}}
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">

    {{-- Alpine.js dari Vite (tanpa entri CSS Tailwind) --}}
    @vite('resources/js/app.js')

    @stack('styles')
</head>
<body class="{{ request()->routeIs('home') ? 'page-home' : 'page-inner' }}">

{{-- ═══ NAVBAR ═══ --}}
<header class="navbar-public {{ request()->routeIs('home') ? 'navbar-public--transparent' : 'navbar-public--solid' }}" 
        id="navbar-public" 
        role="banner">
    <div class="navbar-public__inner">
        {{-- Brand --}}
        <a href="{{ route('home') }}" class="navbar-public__brand">
            <img src="{{ asset('Logo_WargaKu.png') }}" alt="Logo WargaKu" class="navbar-public__logo">
            <span class="navbar-public__brand-name">WargaKu</span>
        </a>

        {{-- Nav tengah (Desktop) --}}
        <nav class="navbar-public__nav" aria-label="Menu utama">
            <a href="{{ route('home') }}"
               class="navbar-public__link {{ request()->routeIs('home') ? 'navbar-public__link--active' : '' }}">
                Home
            </a>
            <a href="{{ route('pengumuman.index') }}"
               class="navbar-public__link {{ request()->routeIs('pengumuman.*') ? 'navbar-public__link--active' : '' }}">
                Pengumuman
            </a>
        </nav>

        {{-- Tombol kanan (Desktop) --}}
        <div class="navbar-public__actions">
            <a href="{{ route('login') }}" class="btn btn--nav-login btn--sm">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn--nav-register btn--sm">Daftar penguna baru</a>
        </div>

        {{-- Tombol Toggle Hamburger (Mobile & Tablet) --}}
        <button type="button" class="navbar-public__toggle" id="navbar-public-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="navbar-public-mobile">
            <span class="navbar-public__toggle-bar"></span>
            <span class="navbar-public__toggle-bar"></span>
            <span class="navbar-public__toggle-bar"></span>
        </button>
    </div>

    {{-- Menu Dropdown Mobile --}}
    <div class="navbar-public__mobile-menu" id="navbar-public-mobile">
        <nav class="navbar-public__mobile-nav" aria-label="Menu mobile">
            <a href="{{ route('home') }}"
               class="navbar-public__mobile-link {{ request()->routeIs('home') ? 'navbar-public__mobile-link--active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Home / Beranda</span>
            </a>
            <a href="{{ route('pengumuman.index') }}"
               class="navbar-public__mobile-link {{ request()->routeIs('pengumuman.*') ? 'navbar-public__mobile-link--active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <span>Pengumuman RT</span>
            </a>
        </nav>
        <div class="navbar-public__mobile-actions">
            <a href="{{ route('login') }}" class="btn btn--nav-login" style="width:100%;">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn--nav-register" style="width:100%;">Daftar pengguna baru</a>
        </div>
    </div>
</header>

{{-- ═══ KONTEN ═══ --}}
<main id="main-content" role="main">
    @yield('content')
</main>

{{-- ═══ FOOTER ═══ --}}
<footer class="footer-public" role="contentinfo">
    <div class="footer-public__main">
        {{-- Brand + deskripsi --}}
        <div>
            <p class="footer-public__brand-name">Wargaku</p>
            <p class="footer-public__brand-desc">
                Aplikasi Informasi dan Layanan Mandiri Warga.<br>
                Transparan, aman, dan terpercaya.
            </p>
            <span class="footer-public__tag">TRPL-102</span>
        </div>

        {{-- Nav links --}}
        <div class="footer-public__nav-group">
            <div>
                <p class="footer-public__nav-title">Navigasi</p>
                <ul class="footer-public__nav-links">
                    <li><a href="{{ route('home') }}" class="footer-public__nav-link">Beranda</a></li>
                    <li><a href="{{ route('pengumuman.index') }}" class="footer-public__nav-link">Pengumuman</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-public__nav-title">Akun</p>
                <ul class="footer-public__nav-links">
                    <li><a href="{{ route('login') }}" class="footer-public__nav-link">Login</a></li>
                    <li><a href="{{ route('register') }}" class="footer-public__nav-link">Daftar</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-public__bottom">
        <div class="footer-public__bottom-inner">
            <span class="footer-public__copy">© {{ date('Y') }} WargaKu. All rights reserved.</span>
            <div class="footer-public__auth-links">
                <a href="{{ route('login') }}">Login</a>
                <span>|</span>
                <a href="{{ route('register') }}">Register</a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
<script>
    (function() {
        const nav = document.getElementById('navbar-public');
        const toggleBtn = document.getElementById('navbar-public-toggle');
        const mobileMenu = document.getElementById('navbar-public-mobile');

        // Scroll listener untuk transparansi navbar
        if (nav) {
            const handleScroll = () => {
                if (window.scrollY > 40) {
                    nav.classList.add('navbar-public--scrolled');
                } else {
                    nav.classList.remove('navbar-public--scrolled');
                }
            };
            window.addEventListener('scroll', handleScroll, { passive: true });
            handleScroll();
        }

        // Toggle menu mobile
        if (toggleBtn && mobileMenu) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = mobileMenu.classList.toggle('navbar-public__mobile-menu--open');
                toggleBtn.classList.toggle('navbar-public__toggle--active', isOpen);
                toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            // Tutup jika klik di luar navbar
            document.addEventListener('click', function(e) {
                if (!nav.contains(e.target)) {
                    mobileMenu.classList.remove('navbar-public__mobile-menu--open');
                    toggleBtn.classList.remove('navbar-public__toggle--active');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    })();
</script>
</body>
</html>
