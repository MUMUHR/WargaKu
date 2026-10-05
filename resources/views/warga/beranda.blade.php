@extends('layouts.warga')

@section('title', 'Dashboard Warga')
@section('meta_description', 'Portal Layanan Mandiri Warga RT 04 / RW 08 Kelurahan Sukamaju')

@section('topbar_section')
    <span class="admin-topbar__breadcrumb-current">Kependudukan</span>
@endsection

@section('content')

{{-- ── Page Header ── --}}
<div class="warga-page-header">
    <div class="warga-page-header__left">
        <h1 class="warga-page-header__title">Dashboard Warga</h1>
        <p class="warga-page-header__sub">Sistem Administrasi Rukun Tetangga 04 Mandiri Terpadu</p>
    </div>
    <nav class="warga-page-header__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('warga.beranda') }}">Home</a>
        <span class="sep">/</span>
        <span class="current">Dashboard</span>
    </nav>
</div>

{{-- ── Card Selamat Datang ── --}}
<div class="warga-greeting-card">
    <div>
        <h2 class="warga-greeting-card__title">Selamat Datang, Bpk. Bambang Pamungkas</h2>
        <p class="warga-greeting-card__sub">No. KK: 3273010908840001 | Alamat: RT 04 / RW 08, Blok B No. 14</p>
    </div>
    <div>
        <a href="{{ route('warga.keluarga') }}" class="btn-detail-kk">
            <i class='bx bx-id-card'></i>
            <span>Detail Profil KK</span>
        </a>
    </div>
</div>

{{-- ── 2 Summary Stat Cards (Teal & Amber) ── --}}
<div class="warga-summary-grid">
    {{-- Card 1: Iuran --}}
    <div class="warga-summary-card warga-summary-card--teal">
        <div class="warga-summary-card__body">
            <div class="warga-summary-card__val">Rp 50.000</div>
            <div class="warga-summary-card__label">Iuran Warga Bulan Ini</div>
            <i class='bx bx-wallet warga-summary-card__watermark'></i>
        </div>
        <a href="{{ route('warga.iuran') }}" class="warga-summary-card__footer">
            <span>Rincian Iuran</span>
            <i class='bx bx-right-arrow-circle'></i>
        </a>
    </div>

    {{-- Card 2: Pengajuan Surat --}}
    <div class="warga-summary-card warga-summary-card--amber">
        <div class="warga-summary-card__body">
            <div class="warga-summary-card__val">1 Menunggu</div>
            <div class="warga-summary-card__label">Status Surat: 3 Selesai, 1 Sedang Diproses RT</div>
            <i class='bx bx-envelope warga-summary-card__watermark'></i>
        </div>
        <a href="{{ route('warga.surat') }}" class="warga-summary-card__footer">
            <span>Cek Status Surat</span>
            <i class='bx bx-right-arrow-circle'></i>
        </a>
    </div>
</div>

{{-- ── 3 Quick Action Cards ── --}}
<div class="warga-actions-grid">
    {{-- Card A: Ajukan Surat Baru --}}
    <div class="warga-action-card">
        <div>
            <div class="warga-action-card__head">
                <div class="warga-action-card__icon warga-action-card__icon--blue">
                    <i class='bx bx-file-blank'></i>
                </div>
                <div>
                    <h3 class="warga-action-card__title">Ajukan Surat Baru</h3>
                    <p class="warga-action-card__sub">Layanan Mandiri Warga</p>
                </div>
            </div>
            <p class="warga-action-card__desc">
                Butuh Surat Pengantar RT untuk KTP, SKCK, atau Keterangan Domisili? Ajukan dokumen secara online tanpa antre fisik di sekretariat.
            </p>
        </div>
        <a href="{{ route('warga.surat') }}?tab=buat" class="btn-warga-action-btn">
            <i class='bx bx-plus-circle'></i>
            <span>Mulai Ajukan</span>
        </a>
    </div>

    {{-- Card B: Lihat Iuran --}}
    <div class="warga-action-card">
        <div>
            <div class="warga-action-card__head">
                <div class="warga-action-card__icon warga-action-card__icon--green">
                    <i class='bx bx-credit-card'></i>
                </div>
                <div>
                    <h3 class="warga-action-card__title">Lihat Iuran</h3>
                    <p class="warga-action-card__sub">Transparansi Keuangan</p>
                </div>
            </div>
            <p class="warga-action-card__desc">
                Cek status pembayaran iuran bulanan kebersihan, keamanan, dan dana kas sosial RT 04 untuk seluruh periode tahun berjalan 2025.
            </p>
        </div>
        <a href="{{ route('warga.iuran') }}" class="btn-warga-action-btn">
            <i class='bx bx-book-open'></i>
            <span>Buka Buku Kas</span>
        </a>
    </div>

    {{-- Card C: Perbarui Data Keluarga --}}
    <div class="warga-action-card">
        <div>
            <div class="warga-action-card__head">
                <div class="warga-action-card__icon warga-action-card__icon--cyan">
                    <i class='bx bx-group'></i>
                </div>
                <div>
                    <h3 class="warga-action-card__title">Perbarui Data Keluarga</h3>
                    <p class="warga-action-card__sub">Sinkronisasi Kependudukan</p>
                </div>
            </div>
            <p class="warga-action-card__desc">
                Ada kelahiran anggota keluarga baru, perpindahan domisili, atau perubahan status pekerjaan? Perbarui data kependudukan KK Anda.
            </p>
        </div>
        <a href="{{ route('warga.keluarga') }}" class="btn-warga-action-btn">
            <i class='bx bx-user-pin'></i>
            <span>Kelola Data KK</span>
        </a>
    </div>
</div>

@endsection
