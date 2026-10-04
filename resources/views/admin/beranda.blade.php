@extends('layouts.admin')

@section('title', 'Dashboard')
@section('meta_description', 'Dashboard Admin RT 04 / RW 08 — WargaKu')

@section('breadcrumb')
    <span class="admin-topbar__breadcrumb-current">Admin RT Dashboard</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-beranda.css') }}">
@endpush

@section('content')

{{-- Page Header --}}
<div class="admin-page-header">
    <div class="admin-page-header__left">
        <h1 class="admin-page-header__title">Dashboard</h1>
        <p class="admin-page-header__sub">Sistem Manajemen Informasi RT 04 / RW 08</p>
    </div>
    <div class="admin-page-header__breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span class="sep">/</span>
        <span class="current">Dashboard</span>
    </div>
</div>

{{-- Top 4 Small Stat Boxes (AdminLTE Classic) --}}
<div class="admin-stat-grid">

    {{-- Box 1: Total KK --}}
    <div class="admin-stat-card admin-stat-card--cyan">
        <div class="admin-stat-card__inner">
            <h3 class="admin-stat-card__number">148</h3>
            <p class="admin-stat-card__label">Total Kartu Keluarga</p>
        </div>
        <div class="admin-stat-card__icon">
            <i class="bx bx-id-card"></i>
        </div>
        <a href="{{ route('admin.data-warga') }}" class="admin-stat-card__footer">
            <span>More info / Kelola Data</span>
            <i class="bx bx-right-arrow-circle" style="font-size: 14px;"></i>
        </a>
    </div>

    {{-- Box 2: Total Warga --}}
    <div class="admin-stat-card admin-stat-card--green">
        <div class="admin-stat-card__inner">
            <h3 class="admin-stat-card__number">562</h3>
            <p class="admin-stat-card__label">Total Warga Terdaftar (Hidup)</p>
        </div>
        <div class="admin-stat-card__icon">
            <i class="bx bx-group"></i>
        </div>
        <a href="{{ route('admin.data-warga') }}" class="admin-stat-card__footer">
            <span>More info / Data Warga</span>
            <i class="bx bx-right-arrow-circle" style="font-size: 14px;"></i>
        </a>
    </div>

    {{-- Box 3: Pengajuan Menunggu --}}
    <div class="admin-stat-card admin-stat-card--teal">
        <div class="admin-stat-card__inner">
            <h3 class="admin-stat-card__number">8</h3>
            <p class="admin-stat-card__label">Pengajuan Menunggu (Butuh Tindakan Admin)</p>
        </div>
        <div class="admin-stat-card__icon">
            <i class="bx bx-paste"></i>
        </div>
        <a href="{{ route('admin.surat') }}" class="admin-stat-card__footer">
            <span>More info / Verifikasi</span>
            <i class="bx bx-right-arrow-circle" style="font-size: 14px;"></i>
        </a>
    </div>

    {{-- Box 4: Iuran Terkumpul --}}
    <div class="admin-stat-card admin-stat-card--dark">
        <div class="admin-stat-card__inner">
            <h3 class="admin-stat-card__number">Rp 4.250.000</h3>
            <p class="admin-stat-card__label">Iuran Terkumpul Bulan Ini (Mei 2025)</p>
        </div>
        <div class="admin-stat-card__icon">
            <i class="bx bx-money"></i>
        </div>
        <a href="{{ route('admin.iuran') }}" class="admin-stat-card__footer">
            <span>More info / Rekap Kas</span>
            <i class="bx bx-right-arrow-circle" style="font-size: 14px;"></i>
        </a>
    </div>

</div>

{{-- Dashboard Grid Layout --}}
<div class="admin-dashboard-grid">

    {{-- Kolom Kiri: Pengumuman RT Terbaru --}}
    <div class="dash-card">
        <div class="dash-card__header">
            <h2 class="dash-card__title">
                <i class="bx bx-broadcast" style="color: #007bff;"></i>
                Pengumuman RT Terbaru
            </h2>
            <div class="dash-card__actions">
                <a href="{{ route('admin.pengumuman.create') }}" class="btn-buat-pengumuman">
                    <i class="bx bx-plus"></i>
                    Buat Pengumuman Baru
                </a>
                <a href="{{ route('admin.pengumuman') }}" class="link-kelola-pengumuman">
                    Kelola Seluruh Pengumuman
                    <i class="bx bx-right-arrow-alt"></i>
                </a>
            </div>
        </div>

        <div class="dash-pengumuman-list">
            {{-- Item 1 --}}
            <div class="dash-pengumuman-item">
                <img src="{{ asset('images/pengumuman/kerjabakti.jpg') }}" alt="Kerja Bakti Lingkungan" class="dash-pengumuman-thumb" onerror="this.src='{{ asset('bg_hero.png') }}'">
                <div class="dash-pengumuman-content">
                    <div class="dash-pengumuman-meta">
                        <span class="badge-publish">Publish</span>
                        <span>16 Mei 2025 &bull; Oleh admin</span>
                    </div>
                    <h3 class="dash-pengumuman-title">Kerja Bakti Lingkungan Serentak & PSN</h3>
                    <p class="dash-pengumuman-desc">Kegiatan gotong royong membersihkan saluran air, selokan, dan pencegahan sarang nyamuk jelang musim penghujan di seluruh gang RT 04.</p>
                </div>
                <a href="{{ route('admin.pengumuman') }}" class="btn-edit-item">Edit</a>
            </div>

            {{-- Item 2 --}}
            <div class="dash-pengumuman-item">
                <img src="{{ asset('images/pengumuman/fogging.jpg') }}" alt="Jadwal Fogging" class="dash-pengumuman-thumb" onerror="this.src='{{ asset('bg_hero.png') }}'">
                <div class="dash-pengumuman-content">
                    <div class="dash-pengumuman-meta">
                        <span class="badge-publish">Publish</span>
                        <span>12 Mei 2025 &bull; Oleh admin</span>
                    </div>
                    <h3 class="dash-pengumuman-title">Jadwal Fogging Nyamuk DBD Wilayah RT 04</h3>
                    <p class="dash-pengumuman-desc">Penyemprotan disinfeksi dan pengasapan fogging DBD terjadwal dari Puskesmas. Mohon warga menutup makanan dan mengamankan hewan peliharaan.</p>
                </div>
                <a href="{{ route('admin.pengumuman') }}" class="btn-edit-item">Edit</a>
            </div>

            {{-- Item 3 --}}
            <div class="dash-pengumuman-item">
                <img src="{{ asset('images/pengumuman/sembako.jpg') }}" alt="Tata Tertib Keamanan" class="dash-pengumuman-thumb" onerror="this.src='{{ asset('bg_hero.png') }}'">
                <div class="dash-pengumuman-content">
                    <div class="dash-pengumuman-meta">
                        <span class="badge-draft">Draft</span>
                        <span>10 Mei 2025 &bull; Oleh admin</span>
                    </div>
                    <h3 class="dash-pengumuman-title">Draf Tata Tertib Parkir & Keamanan Malam</h3>
                    <p class="dash-pengumuman-desc">Rancangan aturan bersama jam malam portal gang, penitipan kendaraan tamu, serta sistem jadwal ronda warga untuk disetujui bersama.</p>
                </div>
                <a href="{{ route('admin.pengumuman') }}" class="btn-edit-item btn-edit-item--gray">Edit Draf</a>
            </div>
        </div>

        <div class="dash-card__footer">
            <span>Menampilkan 3 rilis pengumuman teratas</span>
            <a href="{{ route('admin.pengumuman') }}" class="link-kelola-pengumuman">
                Lihat Semua Arsip Pengumuman (12)
                <i class="bx bx-right-arrow-alt"></i>
            </a>
        </div>
    </div>

    {{-- Kolom Kanan: Rekap & Progress Iuran Bulan Ini (Tanpa Progress Fill Bar) --}}
    <div class="dash-card">
        <div class="dash-card__header">
            <h2 class="dash-card__title">
                <i class="bx bx-book-content" style="color: #28a745;"></i>
                Rekap & Progress Iuran Bulan Ini
            </h2>
            <span class="badge-outline-green">Mei 2025</span>
        </div>
        <div class="dash-iuran-body">
            <p class="dash-iuran-label">Terkumpul Bulan Ini</p>
            <div class="dash-iuran-amount">Rp 4.250.000</div>
            <a href="{{ route('admin.iuran') }}" class="btn-buka-iuran">
                <span>Buka Kelola & Rekap Iuran</span>
                <i class="bx bx-right-arrow-alt" style="font-size: 16px;"></i>
            </a>
        </div>
    </div>

</div>

@endsection