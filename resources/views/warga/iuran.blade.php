@extends('layouts.warga')

@section('title', 'Monitoring Iuran Warga RT 04')
@section('meta_description', 'Sistem monitoring status pembayaran iuran warga per bulan resmi tercatat oleh Bendahara RT 04 / RW 08.')

@section('topbar_section')
    <span class="admin-topbar__breadcrumb-link">Layanan Keuangan</span>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Iuran Warga RT 04</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-iuran.css') }}">
@endpush

@section('content')

{{-- ========================================================
     HEADER & BREADCRUMB
     ======================================================== --}}
<div class="warga-iuran-header">
    <h1 class="warga-iuran-header__title">Monitoring Iuran Warga RT 04</h1>
    <p class="warga-iuran-header__sub">Sistem monitoring status pembayaran iuran warga per bulan (read-only) resmi tercatat oleh Bendahara RT 04 / RW 08.</p>
</div>

{{-- ========================================================
     FILTER BAR TAHUN PERIODE
     ======================================================== --}}
<div class="warga-iuran-filter-bar">
    <div class="warga-iuran-filter-left">
        <label for="select-tahun-iuran" class="warga-iuran-filter-label" style="margin:0;cursor:pointer;">
            <i class='bx bx-filter-alt'></i> TAHUN PERIODE:
        </label>
        <select id="select-tahun-iuran" class="warga-iuran-select" onchange="window.location.href='{{ route('warga.iuran') }}?tahun=' + this.value" aria-label="Pilih Tahun Periode Iuran">
            @foreach($daftarTahun as $th)
                <option value="{{ $th }}" {{ $th == $tahunDipilih ? 'selected' : '' }}>
                    Tahun {{ $th }} {{ $th == $tahunBerjalan ? '(Tahun Berjalan)' : '(Riwayat)' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="warga-iuran-filter-right">
        Keluarga: <strong>{{ $kepala?->nama ?? 'Kepala Keluarga' }}</strong> 
        @if($keluarga)
            <span style="color:#64748b;font-size:12.5px;">(No. KK: {{ $keluarga->no_kk }})</span>
        @endif
    </div>
</div>

{{-- ========================================================
     2 BIG STAT BANNER CARDS
     ======================================================== --}}
<div class="warga-iuran-banner-grid">
    {{-- Card Kiri: Status Bulan Ini --}}
    @if($isBulanIniLunas)
        <div class="warga-iuran-banner" style="background:#28a745;" id="card-status-bulan-ini">
            <div class="warga-iuran-banner__body">
                <div class="warga-iuran-banner__nominal" id="val-status-nominal">
                    Rp {{ number_format($nominalBulanBerjalan, 0, ',', '.') }}
                </div>
                <div class="warga-iuran-banner__subtitle" id="val-status-subtitle">
                    STATUS BULAN INI ({{ strtoupper(\Carbon\Carbon::create($tahunBerjalan, $bulanBerjalan, 1)->translatedFormat('F Y')) }})
                </div>
                <div>
                    <span class="warga-iuran-banner__badge" style="color:#28a745;" id="badge-status-bulan-ini">LUNAS</span>
                </div>
                <i class='bx bx-check-double warga-iuran-banner__watermark' aria-hidden="true"></i>
            </div>
            <div class="warga-iuran-banner__footer" style="background:#1e7e34;color:#d4edda;">
                <span id="footer-text-bulan-ini">Lunas Terverifikasi Bendahara RT</span>
                <i class='bx bx-check-circle' aria-hidden="true"></i>
            </div>
        </div>
    @else
        <div class="warga-iuran-banner warga-iuran-banner--merah" id="card-status-bulan-ini">
            <div class="warga-iuran-banner__body">
                <div class="warga-iuran-banner__nominal" id="val-status-nominal">
                    Rp {{ number_format($nominalBulanBerjalan, 0, ',', '.') }}
                </div>
                <div class="warga-iuran-banner__subtitle" id="val-status-subtitle">
                    STATUS BULAN INI ({{ strtoupper(\Carbon\Carbon::create($tahunBerjalan, $bulanBerjalan, 1)->translatedFormat('F Y')) }})
                </div>
                <div>
                    <span class="warga-iuran-banner__badge" id="badge-status-bulan-ini">BELUM LUNAS</span>
                </div>
                <i class='bx bx-calendar-event warga-iuran-banner__watermark' aria-hidden="true"></i>
            </div>
            <div class="warga-iuran-banner__footer">
                <span id="footer-text-bulan-ini">Periode Bulan Berjalan</span>
                <i class='bx bx-error-circle' aria-hidden="true"></i>
            </div>
        </div>
    @endif

    {{-- Card Kanan: Tarif Iuran RT 04 (Biru) --}}
    <div class="warga-iuran-banner warga-iuran-banner--biru">
        <div class="warga-iuran-banner__body">
            <div class="warga-iuran-banner__nominal">
                Rp {{ number_format($tarifStandar, 0, ',', '.') }} <span class="warga-iuran-banner__nominal-sub">/ bln</span>
            </div>
            <div class="warga-iuran-banner__subtitle">TARIF IURAN RT 04</div>
            <p class="warga-iuran-banner__desc">
                {{ $catatanTarifAdmin }}
            </p>
            <i class='bx bx-wallet warga-iuran-banner__watermark' aria-hidden="true"></i>
        </div>
        <div class="warga-iuran-banner__footer">
            <span>Rincian Alokasi Kas dari Pengurus RT</span>
            <i class='bx bx-info-circle' aria-hidden="true"></i>
        </div>
    </div>
</div>

{{-- ========================================================
     CARD UTAMA: STATUS PEMBAYARAN IURAN PER BULAN (12 BULAN)
     ======================================================== --}}
<div class="warga-iuran-main-card">
    <div class="warga-iuran-main-header">
        <h2 class="warga-iuran-main-title" id="main-card-title">
            Status Pembayaran Iuran Warga per Bulan (Tahun {{ $tahunDipilih }})
        </h2>
        <div class="warga-iuran-badge-readonly">
            <i class='bx bx-lock-alt'></i> SIFAT DATA: MONITORING READ-ONLY TERCATAT OLEH BENDAHARA RT
        </div>
    </div>

    <div class="warga-iuran-main-body">
        <div class="warga-iuran-months-grid" id="months-grid-container">

            @foreach($cards12Bulan as $card)
                <div class="month-card {{ $card['card_modifier_class'] }}">
                    <div class="month-card-header">
                        <div class="month-card-title-wrap">
                            <span class="month-card-name">{{ $card['nama_bulan'] }}</span>
                            @if($card['penanda_berjalan'])
                                <span class="month-badge month-badge--berjalan">BERJALAN</span>
                            @endif
                        </div>
                        <span class="month-badge {{ $card['badge_class'] }}">
                            {{ $card['badge_label'] }}
                        </span>
                    </div>

                    <div class="month-card-body">
                        <div class="month-card-nominal">{{ $card['nominal_format'] }}</div>
                        <div class="month-card-info {{ $card['is_lunas'] ? 'month-card-info--lunas' : ($card['penanda_berjalan'] ? 'month-card-info--berjalan' : 'month-card-info--future') }}" @if(!empty($card['catatan'])) title="{{ $card['catatan'] }}" @endif>
                            @if($card['is_lunas'])
                                <i class='bx bx-check-circle'></i>
                            @endif
                            <span>{{ $card['info_tanggal'] }}</span>
                        </div>
                    </div>

                    <div class="month-card-footer">
                        {{ $card['footer_text'] }}
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

@endsection
