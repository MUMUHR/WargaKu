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

        {{-- Nav tengah --}}
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

        {{-- Tombol kanan --}}
        <div class="navbar-public__actions">
            <a href="{{ route('login') }}" class="btn btn--nav-login btn--sm">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn--nav-register btn--sm">Daftar penguna baru</a>
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
        if (!nav) return;
        const handleScroll = () => {
            if (window.scrollY > 40) {
                nav.classList.add('navbar-public--scrolled');
            } else {
                nav.classList.remove('navbar-public--scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
    })();
</script>
</body>
</html>
