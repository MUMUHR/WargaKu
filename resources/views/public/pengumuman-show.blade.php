@extends('layouts.public')

@section('title', $item['judul'])
@section('meta_description', Str::limit(str_replace("\n", ' ', $item['isi']), 160))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pengumuman.css') }}">
@endpush

@section('content')
<div class="detail-page">

    {{-- Tombol kembali (hijau sesuai mockup) --}}
    <div class="detail-back">
        <a href="{{ route('pengumuman.index') }}" class="btn btn--back">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Konten artikel --}}
    <article class="detail-article">
        <h1 class="detail-article__title">{{ $item['judul'] }}</h1>

        {{-- Gambar Utama --}}
        @if($item['gambar'])
            @php
                $imgSrc = Str::startsWith($item['gambar'], ['http', 'images/', 'storage/'])
                    ? asset($item['gambar'])
                    : asset('storage/' . $item['gambar']);
            @endphp
            <div class="detail-article__img-wrap">
                <img src="{{ $imgSrc }}"
                     alt="Foto {{ $item['judul'] }}"
                     loading="eager">
            </div>
        @endif

        {{-- Tanggal publikasi --}}
        <p class="detail-article__date">
            Tanggal: {{ \App\Data\DummyData::formatTanggal($item['created_at']) }}
        </p>

        {{-- Isi pengumuman --}}
        <div class="detail-article__body">{{ $item['isi'] }}</div>
    </article>

</div>
@endsection
