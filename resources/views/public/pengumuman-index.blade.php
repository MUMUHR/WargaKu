@extends('layouts.public')

@section('title', 'Pengumuman RT')
@section('meta_description', 'Daftar pengumuman terbaru dari RT 04 / RW 08 untuk seluruh warga.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pengumuman.css') }}">
@endpush

@section('content')
<div class="page-content">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="breadcrumb__link">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Beranda
        </a>
        <span class="breadcrumb__sep" aria-hidden="true">/</span>
        <span class="breadcrumb__current">Pengumuman RT</span>
    </nav>

    {{-- Header --}}
    <header class="page-header">
        <h1 class="page-header__title">Pengumuman RT</h1>
        <p class="page-header__subtitle">Informasi terbaru dari RT untuk seluruh warga.</p>
    </header>

    {{-- Daftar pengumuman --}}
    @if(count($pengumuman) > 0)
        <div class="announcement-list" role="list">
            @foreach($pengumuman as $p)
                <article class="announcement-row" role="listitem">
                    {{-- Gambar --}}
                    @if($p['gambar'])
                        @php
                            $pImg = Str::startsWith($p['gambar'], ['http', 'images/', 'storage/'])
                                ? asset($p['gambar'])
                                : asset('storage/' . $p['gambar']);
                        @endphp
                        <div class="announcement-row__img-wrap">
                            <img src="{{ $pImg }}"
                                 alt="Gambar {{ $p['judul'] }}"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="announcement-row__no-img" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Isi --}}
                    <div class="announcement-row__body">
                        <p class="announcement-row__date">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            {{ \App\Data\DummyData::formatTanggal($p['created_at']) }}
                        </p>
                        <h2 class="announcement-row__title">{{ $p['judul'] }}</h2>
                        <p class="announcement-row__excerpt">
                            {{ Str::limit(str_replace("\n", ' ', $p['isi']), 220) }}
                        </p>
                    </div>

                    {{-- Tombol --}}
                    <div class="announcement-row__action">
                        <a href="{{ route('pengumuman.show', $p['id']) }}"
                           class="btn btn--primary btn--sm"
                           aria-label="Lihat selengkapnya: {{ $p['judul'] }}">
                            Lihat Selengkapnya &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrap" aria-label="Navigasi halaman">
            <p class="pagination-info">
                Menampilkan {{ $offset + 1 }}–{{ min($offset + $perHalaman, $total) }} dari {{ $total }} pengumuman
            </p>

            <nav class="pagination">
                {{-- Sebelumnya --}}
                @if($halaman > 1)
                    <a href="{{ route('pengumuman.index', ['halaman' => $halaman - 1]) }}"
                       class="pagination__btn"
                       aria-label="Halaman sebelumnya">&laquo; Sebelumnya</a>
                @else
                    <span class="pagination__btn pagination__btn--disabled" aria-disabled="true">&laquo; Sebelumnya</span>
                @endif

                {{-- Nomor halaman --}}
                @for($i = 1; $i <= $totalHalaman; $i++)
                    <a href="{{ route('pengumuman.index', ['halaman' => $i]) }}"
                       class="pagination__btn {{ $i === $halaman ? 'pagination__btn--active' : '' }}"
                       aria-label="Halaman {{ $i }}"
                       @if($i === $halaman) aria-current="page" @endif>
                        {{ $i }}
                    </a>
                @endfor

                {{-- Selanjutnya --}}
                @if($halaman < $totalHalaman)
                    <a href="{{ route('pengumuman.index', ['halaman' => $halaman + 1]) }}"
                       class="pagination__btn"
                       aria-label="Halaman selanjutnya">Selanjutnya &raquo;</a>
                @else
                    <span class="pagination__btn pagination__btn--disabled" aria-disabled="true">Selanjutnya &raquo;</span>
                @endif
            </nav>
        </div>

    @else
        {{-- State kosong --}}
        <div style="text-align:center;padding:var(--space-20) var(--space-6);color:var(--color-gray-500);">
            <svg style="width:56px;height:56px;margin:0 auto var(--space-4);display:block;opacity:0.4;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <p>Belum ada pengumuman.</p>
        </div>
    @endif

</div>
@endsection
