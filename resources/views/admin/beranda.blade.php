@extends('layouts.admin')

@section('title', 'Dashboard')
@section('meta_description', 'Dashboard Admin RT 04 — WargaKu')

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
        <h1 class="admin-page-header__title" style="margin:0;">Dashboard</h1>
    </div>
</div>

{{-- Top 4 Small Stat Boxes (AdminLTE Classic) --}}
<div class="admin-stat-grid">

    {{-- Box 1: Total KK --}}
    <div class="admin-stat-card admin-stat-card--cyan">
        <div class="admin-stat-card__inner">
            <h3 class="admin-stat-card__number">{{ $totalKk ?? 0 }}</h3>
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
            <h3 class="admin-stat-card__number">{{ $totalWargaHidup ?? 0 }}</h3>
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
            <h3 class="admin-stat-card__number">{{ $totalPengajuanMenunggu ?? 0 }}</h3>
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
            <h3 class="admin-stat-card__number">Rp {{ number_format($iuranTerkumpulBulanIni ?? 0, 0, ',', '.') }}</h3>
            <p class="admin-stat-card__label">Iuran Terkumpul Bulan Ini ({{ $namaBulanTahun ?? now()->translatedFormat('F Y') }})</p>
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
            @forelse($pengumumanTerbaru ?? [] as $p)
                @php
                    $statusVal = is_object($p->status_publikasi) ? $p->status_publikasi->value : $p->status_publikasi;
                    $penulis = $p->admin ? ($p->admin->nama ?? $p->admin->name ?? 'Admin RT') : 'Admin RT';
                    $tanggal = $p->created_at ? $p->created_at->format('d M Y') : '-';
                    $hasImg = !empty($p->gambar);
                    $pImg = $hasImg
                        ? (Str::startsWith($p->gambar, ['http', 'images/', 'storage/']) ? asset($p->gambar) : asset('storage/' . $p->gambar))
                        : null;
                @endphp
                <div class="dash-pengumuman-item">
                    @if($hasImg)
                        <img src="{{ $pImg }}" alt="{{ $p->judul }}" class="dash-pengumuman-thumb" onerror="this.style.display='none'">
                    @endif
                    <div class="dash-pengumuman-content">
                        <div class="dash-pengumuman-meta">
                            @if($statusVal === 'publish')
                                <span class="badge-publish">Publish</span>
                            @else
                                <span class="badge-draft">Draft</span>
                            @endif
                            <span>{{ $tanggal }} &bull; Oleh {{ $penulis }}</span>
                        </div>
                        <h3 class="dash-pengumuman-title">{{ $p->judul }}</h3>
                        <p class="dash-pengumuman-desc">{{ Str::limit(str_replace("\n", ' ', $p->isi), 130) }}</p>
                    </div>
                    <a href="{{ route('admin.pengumuman.edit', $p->id) }}" class="btn-edit-item {{ $statusVal === 'draft' ? 'btn-edit-item--gray' : '' }}">
                        {{ $statusVal === 'draft' ? 'Edit Draf' : 'Edit' }}
                    </a>
                </div>
            @empty
                <div style="padding: 32px 18px; text-align: center; color: #6c757d;">
                    <i class="bx bx-news" style="font-size: 38px; color: #cbd5e1; margin-bottom: 6px; display: block;"></i>
                    <p style="font-size: 13.5px; font-weight: 600; color: #334155; margin: 0 0 4px;">Belum Ada Pengumuman RT</p>
                    <p style="font-size: 12px; color: #64748b; margin: 0 0 12px;">Buat pengumuman baru untuk menyampaikan informasi penting ke warga.</p>
                    <a href="{{ route('admin.pengumuman.create') }}" class="btn-buat-pengumuman" style="display: inline-flex;">
                        <i class="bx bx-plus"></i> Buat Pengumuman Baru
                    </a>
                </div>
            @endforelse
        </div>

        <div class="dash-card__footer">
            <span>Menampilkan {{ min(count($pengumumanTerbaru ?? []), $totalPengumuman ?? 0) }} rilis pengumuman teratas</span>
            <a href="{{ route('admin.pengumuman') }}" class="link-kelola-pengumuman">
                Lihat Semua Arsip Pengumuman ({{ $totalPengumuman ?? 0 }})
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
            <span class="badge-outline-green">{{ $namaBulanTahun ?? now()->translatedFormat('F Y') }}</span>
        </div>
        <div class="dash-iuran-body">
            <p class="dash-iuran-label">Terkumpul Bulan Ini</p>
            <div class="dash-iuran-amount">Rp {{ number_format($iuranTerkumpulBulanIni ?? 0, 0, ',', '.') }}</div>
            <a href="{{ route('admin.iuran') }}" class="btn-buka-iuran">
                <span>Buka Kelola & Rekap Iuran</span>
                <i class="bx bx-right-arrow-alt" style="font-size: 16px;"></i>
            </a>
        </div>
    </div>

</div>

@endsection