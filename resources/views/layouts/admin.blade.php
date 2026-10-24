<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaKu - Panel Admin RT 04')">
    <title>@yield('title', 'Admin') - WargaKu RT 04</title>

    {{-- Design Tokens & Base --}}
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    {{-- Boxicons --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    @vite('resources/js/app.js')
    @stack('styles')
</head>
<body class="admin-body">

<aside class="admin-sidebar" id="admin-sidebar" role="complementary" aria-label="Navigasi Admin">
    <div class="admin-sidebar__header">
        <a href="{{ route('admin.beranda') }}" class="admin-sidebar__brand">
            <img src="{{ asset('Logo_WargaKu.png') }}" alt="Logo WargaKu" class="admin-sidebar__logo">
            <div class="admin-sidebar__brand-text">
                <span class="admin-sidebar__brand-name">WargaKu</span>
                <span class="admin-sidebar__brand-sub">ADMIN RT 04</span>
            </div>
        </a>
    </div>

    <a href="{{ route('admin.pengaturan') }}" class="admin-sidebar__profile" style="text-decoration:none;" title="Buka Profil & Keamanan">
        <div class="admin-sidebar__avatar" aria-hidden="true">
            <i class="bx bxs-user" style="font-size: 20px;"></i>
        </div>
        <div class="admin-sidebar__profile-info">
            <span class="admin-sidebar__profile-name">Admin RT</span>
            <span class="admin-sidebar__profile-role">Administrator</span>
        </div>
    </a>

    @php
        $badgeAdminVerifikasi = \App\Models\Riwayat_Perubahan::where('status', 'menunggu')->count();
        $badgeAdminSurat = \App\Models\Surat::where('status', 'menunggu')->count();
    @endphp

    <nav class="admin-sidebar__nav" aria-label="Menu admin">
        <p class="admin-sidebar__nav-label">MENU UTAMA</p>

        <a href="{{ route('admin.beranda') }}" class="admin-sidebar__nav-link {{ request()->routeIs('admin.beranda') ? 'admin-sidebar__nav-link--active' : '' }}">
            Beranda / Dashboard
        </a>

        <a href="{{ route('admin.pengumuman') }}" class="admin-sidebar__nav-link {{ request()->routeIs('admin.pengumuman*') ? 'admin-sidebar__nav-link--active' : '' }}">
            Kelola Pengumuman
        </a>

        <a href="{{ route('admin.data-warga') }}" class="admin-sidebar__nav-link {{ request()->routeIs('admin.data-warga*') ? 'admin-sidebar__nav-link--active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
            <span>Kelola Data Warga & Verifikasi</span>
            @if($badgeAdminVerifikasi > 0)
                <span style="background:#eab308;color:#000;font-size:10px;font-weight:800;padding:2px 7px;border-radius:10px;">{{ $badgeAdminVerifikasi }}</span>
            @endif
        </a>

        <a href="{{ route('admin.surat') }}" class="admin-sidebar__nav-link {{ request()->routeIs('admin.surat*') ? 'admin-sidebar__nav-link--active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
            <span>Proses Pengajuan Surat</span>
            @if($badgeAdminSurat > 0)
                <span style="background:#eab308;color:#000;font-size:10px;font-weight:800;padding:2px 7px;border-radius:10px;">{{ $badgeAdminSurat }}</span>
            @endif
        </a>

        <a href="{{ route('admin.iuran') }}" class="admin-sidebar__nav-link {{ request()->routeIs('admin.iuran*') ? 'admin-sidebar__nav-link--active' : '' }}">
            Kelola & Rekap Iuran
        </a>

        <p class="admin-sidebar__nav-label">PENGATURAN</p>

        <a href="{{ route('admin.pengaturan') }}" class="admin-sidebar__nav-link {{ request()->routeIs('admin.pengaturan') ? 'admin-sidebar__nav-link--active' : '' }}">
            Profil & Keamanan
        </a>
    </nav>

    <div class="admin-sidebar__footer">
        <span class="admin-sidebar__logout-label">Logout dari akun</span>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="admin-sidebar__logout-btn" id="btn-logout-sidebar">Logout</button>
        </form>
    </div>
</aside>

<div class="admin-main" id="admin-main">
    <header class="admin-topbar" role="banner">
        <div class="admin-topbar__left">
            <button class="admin-topbar__hamburger" id="btn-sidebar-toggle" aria-label="Toggle sidebar" aria-expanded="true">
                <i class="bx bx-menu" style="font-size: 22px;"></i>
            </button>
            <nav class="admin-topbar__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.beranda') }}" class="admin-topbar__breadcrumb-link">Home</a>
                @hasSection('breadcrumb')
                    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
                    @yield('breadcrumb')
                @endif
            </nav>
        </div>
        <a href="{{ route('admin.pengaturan') }}" class="admin-topbar__right" style="text-decoration:none;cursor:pointer;" title="Buka Profil & Keamanan">
            <div class="admin-topbar__user-info">
                <span class="admin-topbar__admin-name">Admin RT</span>
                <span class="admin-topbar__admin-role">Administrator RT</span>
            </div>
            <div class="admin-topbar__avatar" aria-hidden="true" style="background: #28a745; color: #fff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="bx bxs-user" style="font-size: 18px;"></i>
            </div>
        </a>
    </header>

    <div class="admin-content">
        @yield('content')
    </div>

    <footer class="admin-footer" role="contentinfo">
        <span>Copyright &copy; 2026 <a href="{{ route('home') }}" class="admin-footer__link">WargaKu RT 04</a>. All rights reserved.</span>
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

@include('partials.sweetalert')
</body>
</html>