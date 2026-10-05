<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaKu - Portal Layanan Warga Mandiri RT 04 / RW 08')">
    <title>@yield('title', 'Dashboard Warga') - WargaKu RT 04</title>

    {{-- Design Tokens & Base --}}
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/warga.css') }}">

    {{-- Boxicons --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    @vite('resources/js/app.js')
    @stack('styles')
</head>
<body class="admin-body">

<aside class="admin-sidebar" id="admin-sidebar" role="complementary" aria-label="Navigasi Warga">
    <div class="admin-sidebar__header">
        <a href="{{ route('warga.beranda') }}" class="admin-sidebar__brand">
            <img src="{{ asset('Logo_WargaKu.png') }}" alt="Logo WargaKu" class="admin-sidebar__logo">
            <div class="admin-sidebar__brand-text">
                <span class="admin-sidebar__brand-name">WargaKu</span>
                <span class="admin-sidebar__brand-sub" style="color: #94a3b8;">RT 04</span>
            </div>
        </a>
    </div>

    <div class="admin-sidebar__profile">
        <div class="admin-sidebar__avatar" aria-hidden="true" style="background: #ffffff; color: #0284c7;">
            <i class="bx bxs-user" style="font-size: 20px;"></i>
        </div>
        <div class="admin-sidebar__profile-info">
            <span class="admin-sidebar__profile-name">Bpk. Bambang Pamungkas</span>
            <span class="admin-sidebar__profile-role" style="display: flex; align-items: center; gap: 4px; color: #28a745; font-weight: 600;">
                <span style="font-size: 9px;">●</span> Online
            </span>
        </div>
    </div>

    <nav class="admin-sidebar__nav" aria-label="Menu warga">
        <p class="admin-sidebar__nav-label">MENU UTAMA</p>

        <a href="{{ route('warga.beranda') }}" class="admin-sidebar__nav-link {{ request()->routeIs('warga.beranda') ? 'warga-nav-link--active' : '' }}">
            Beranda
        </a>

        <a href="{{ route('warga.keluarga') }}" class="admin-sidebar__nav-link {{ request()->routeIs('warga.keluarga*') ? 'warga-nav-link--active' : '' }}">
            Data Keluarga
        </a>

        <a href="{{ route('warga.surat') }}" class="admin-sidebar__nav-link {{ request()->routeIs('warga.surat*') ? 'warga-nav-link--active' : '' }}">
            Ajukan Surat
        </a>

        <a href="{{ route('warga.iuran') }}" class="admin-sidebar__nav-link {{ request()->routeIs('warga.iuran*') ? 'warga-nav-link--active' : '' }}">
            Iuran Warga
        </a>

        <a href="{{ route('warga.profil') }}" class="admin-sidebar__nav-link {{ request()->routeIs('warga.profil*') ? 'warga-nav-link--active' : '' }}">
            Profil &amp; Keamanan
        </a>
    </nav>

    <div class="admin-sidebar__footer">
        <span class="admin-sidebar__logout-label">Logout dari akun</span>
        <a href="{{ route('login') }}" class="admin-sidebar__logout-btn" id="btn-logout-sidebar">Logout</a>
    </div>
</aside>

<div class="admin-main" id="admin-main">
    <header class="admin-topbar" role="banner">
        <div class="admin-topbar__left">
            <button class="admin-topbar__hamburger" id="btn-sidebar-toggle" aria-label="Toggle sidebar" aria-expanded="true">
                <i class="bx bx-menu" style="font-size: 22px;"></i>
            </button>
            <nav class="admin-topbar__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('warga.beranda') }}" class="admin-topbar__breadcrumb-link">Beranda</a>
                <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
                @hasSection('topbar_section')
                    @yield('topbar_section')
                @else
                    <span class="admin-topbar__breadcrumb-current">Kependudukan</span>
                @endif
            </nav>
        </div>
        <div class="admin-topbar__right">
            <div class="admin-topbar__user-info" style="text-align: right;">
                <span class="admin-topbar__admin-name" style="font-size: 13.5px; font-weight: 700; color: #212529;">Bpk. Bambang Pamungkas</span>
                <span class="admin-topbar__admin-role" style="font-size: 11.5px; color: #6c757d;">Warga</span>
            </div>
            <div class="admin-topbar__avatar" aria-hidden="true" style="background: #007bff; color: #fff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="bx bxs-user" style="font-size: 18px;"></i>
            </div>
        </div>
    </header>

    <div class="admin-content" style="padding: 20px 24px 30px;">
        @yield('content')
    </div>

    <footer class="admin-footer" role="contentinfo">
        <span>Copyright &copy; 2025 <a href="{{ route('home') }}" class="admin-footer__link">WargaKu RT 04 / RW 08</a>. AdminLTE Classic Style.</span>
        <span class="admin-footer__right">Versi 3.2.0</span>
    </footer>
</div>

@stack('scripts')
<script>
    (function () {
        var sidebar = document.getElementById('admin-sidebar');
        var main    = document.getElementById('admin-main');
        var btn     = document.getElementById('btn-sidebar-toggle');
        if (!btn || !sidebar || !main) return;
        btn.addEventListener('click', function () {
            var collapsed = sidebar.classList.toggle('admin-sidebar--collapsed');
            main.classList.toggle('admin-main--expanded', collapsed);
            btn.setAttribute('aria-expanded', String(!collapsed));
        });
    })();
</script>
</body>
</html>
