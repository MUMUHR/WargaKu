@extends('layouts.public')

@section('title', 'Beranda')
@section('meta_description', 'WargaKu - Informasi dan Layanan Warga RT 04 / RW 08. Kelola iuran, ajukan surat pengantar, dan akses pengumuman RT secara digital.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

{{-- ═══════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════ --}}
<section class="hero" aria-label="Selamat datang di WargaKu">
    <div class="hero__bg">
        <img src="{{ asset('bg_hero.png') }}" alt="Pemandangan Asri Lingkungan WargaKu" class="hero__bg-img">
    </div>
    <div class="hero__gradient-overlay"></div>
    <div class="hero__content">
        <div class="hero__title-bar" aria-hidden="true"></div>
        <div class="hero__text">
            <div class="hero__title-box">
                <h1 class="hero__title">
                    Informasi dan Layanan Warga dalam<br>Satu Tempat
                </h1>
            </div>
            <p class="hero__subtitle">
                WargaKu membantu warga mendapatkan informasi terbaru dari RT serta mengakses
                berbagai layanan administrasi dengan lebih mudah dan praktis. Semua kebutuhan
                warga dapat dikelola secara terstruktur, transparan, dan terpercaya dalam satu tempat.
            </p>
            <div class="hero__actions">
                <a href="{{ route('login') }}" class="btn btn--hero-login">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn--hero-register">Daftar pengguna baru</a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     SECTION FITUR & CARA KERJA
     ═══════════════════════════════════════════ --}}
<section class="section section--light" aria-label="Tentang WargaKu">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Wargaku: Solusi Digital Pengurusan RT Lebih Praktis & Transparan</h2>
            <p class="section__subtitle">
                Kelola iuran, surat pengantar, hingga pengumuman warga dalam satu aplikasi.
                Semua makin gampang, aman, dan transparan dari genggaman tangan.
            </p>
        </div>

        <div class="features-grid">
            {{-- Kiri: gambar komunitas --}}
            <div class="features-image">
                <img src="{{ asset('ui-wargaku/image 25.png') }}"
                     alt="Komunitas warga RT 04"
                     style="width:100%;height:420px;object-fit:cover;"
                     loading="lazy">
            </div>

            {{-- Kanan: fitur + cara kerja --}}
            <div>
                <p style="font-size:var(--font-size-sm);font-weight:var(--font-weight-semibold);color:var(--color-gray-700);margin-bottom:var(--space-4);">
                    Keunggulan Utama (Features)
                </p>

                <ul class="features-list">
                    <li class="feature-item">
                        <span class="feature-item__icon">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div class="feature-item__body">
                            <p class="feature-item__title">Surat-menyurat Otomatis</p>
                            <p class="feature-item__desc">Buat surat pengantar RT/RW secara digital tanpa perlu antre atau bertamu di jam istirahat.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-item__icon">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div class="feature-item__body">
                            <p class="feature-item__title">Manajemen Iuran Transparan</p>
                            <p class="feature-item__desc">Bayar iuran bulanan digital dan cek laporan keuangan RT/RW secara real-time.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-item__icon">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div class="feature-item__body">
                            <p class="feature-item__title">Pengumuman & Agenda Komunitas</p>
                            <p class="feature-item__desc">Dapatkan notifikasi langsung mengenai kegiatan warga, jadwal siskamling, hingga rapat rutin.</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <span class="feature-item__icon">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div class="feature-item__body">
                            <p class="feature-item__title">Data Warga Terstruktur</p>
                            <p class="feature-item__desc">Perbarui data kependudukan keluarga, ajukan perubahan data, dan kelola anggota KK secara mandiri.</p>
                        </div>
                    </li>
                </ul>

                <div class="features-cara-kerja">
                    <p class="features-cara-kerja__title">Cara Kerja (How It Works)</p>
                    <ul class="how-it-works-list">
                        <li class="how-it-works-item">
                            <span class="how-it-works-item__dot"></span>
                            <p class="how-it-works-item__text"><strong>Daftar & Verifikasi:</strong> Masukkan data diri untuk diverifikasi oleh pengurus RT/RW kamu.</p>
                        </li>
                        <li class="how-it-works-item">
                            <span class="how-it-works-item__dot"></span>
                            <p class="how-it-works-item__text"><strong>Pilih Layanan:</strong> Akses menu iuran, pembuatan surat, atau ruang diskusi warga.</p>
                        </li>
                        <li class="how-it-works-item">
                            <span class="how-it-works-item__dot"></span>
                            <p class="how-it-works-item__text"><strong>Selesai:</strong> Urusan administratif selesai dengan cepat tanpa ribet.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     SECTION PENGUMUMAN TERKINI
     ═══════════════════════════════════════════ --}}
<section class="announcements-section" aria-label="Pengumuman terkini">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title" style="color:var(--color-white);">Pengumuman Terkini</h2>
        </div>

        @if(count($pengumuman) > 0)
            <div class="announcements-grid">
                @foreach($pengumuman as $p)
                    <article class="announcement-card">
                        {{-- Gambar / Placeholder --}}
                        <div class="announcement-card__img-wrap">
                            @if($p['gambar'])
                                @php
                                    $pImg = Str::startsWith($p['gambar'], ['http', 'images/', 'storage/'])
                                        ? asset($p['gambar'])
                                        : asset('storage/' . $p['gambar']);
                                @endphp
                                <img src="{{ $pImg }}"
                                     alt="Gambar {{ $p['judul'] }}"
                                     loading="lazy">
                            @else
                                <div class="announcement-card__no-img">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="announcement-card__body">
                            <p class="announcement-card__meta">
                                {{ \App\Data\DummyData::formatTanggal($p['created_at']) }}
                            </p>
                            <h3 class="announcement-card__title">{{ $p['judul'] }}</h3>
                            <p class="announcement-card__excerpt">
                                {{ Str::limit(str_replace("\n", ' ', $p['isi']), 120) }}
                            </p>
                            <div class="announcement-card__footer">
                                <a href="{{ route('pengumuman.show', $p['id']) }}"
                                   class="btn btn--primary btn--sm"
                                   aria-label="Lihat selengkapnya: {{ $p['judul'] }}">
                                    Lihat Selengkapnya
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="announcements-empty">
                <svg class="announcements-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <p class="announcements-empty__text">Belum ada pengumuman.</p>
            </div>
        @endif
    </div>
</section>

@endsection
