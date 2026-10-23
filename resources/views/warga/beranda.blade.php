@extends('layouts.warga')

@section('title', 'Dashboard Warga')
@section('meta_description', 'Portal Layanan Mandiri Warga RT 04 Kelurahan Belian')

@section('content')

{{-- ── Page Header ── --}}
<div class="warga-page-header">
    <div class="warga-page-header__left">
        <h1 class="warga-page-header__title">Dashboard Warga</h1>
        <p class="warga-page-header__sub">Sistem Administrasi Rukun Tetangga 04 Mandiri Terpadu</p>
    </div>
    <div class="warga-page-header__breadcrumb">
        <a href="{{ route('warga.beranda') }}">Home</a>
        <span class="sep">/</span>
        <span class="current">Dashboard</span>
    </div>
</div>

{{-- ── Card Selamat Datang (Data Riil Warga / KK) ── --}}
<div class="warga-greeting-card">
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="background:#e0f2fe;color:#0284c7;width:48px;height:48px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0;">
            <i class='bx bx-id-card'></i>
        </div>
        <div>
            <h2 class="warga-greeting-card__title">Selamat Datang, {{ $kepala?->nama ?? 'Warga RT 04' }}</h2>
            <p class="warga-greeting-card__sub">
                No. KK: <strong>{{ $keluarga?->no_kk ?? '-' }}</strong> | Alamat: {{ $kepala?->alamat ?? 'RT 04 Belian' }}
            </p>
        </div>
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
            <div class="warga-summary-card__val">Rp {{ number_format($nominalIuran, 0, ',', '.') }}</div>
            <div class="warga-summary-card__label" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <span>Iuran Warga {{ $namaBulan }} {{ $tahunBerjalan }}</span>
                @if($isIuranLunas)
                    <span style="background:#28a745;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">LUNAS</span>
                @else
                    <span style="background:#dc3545;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">BELUM LUNAS</span>
                @endif
            </div>
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
            <div class="warga-summary-card__val">{{ $totalSuratMenunggu }} Menunggu</div>
            <div class="warga-summary-card__label">
                Status Surat: {{ $totalSuratSelesai }} Selesai, {{ $totalSuratMenunggu }} Sedang Diproses RT
            </div>
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
                Cek status pembayaran iuran bulanan kebersihan, keamanan, dan dana kas sosial RT 04 untuk seluruh periode tahun berjalan {{ $tahunBerjalan }}.
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

{{-- ── Riwayat Pengajuan Surat Terakhir (Mockup Sesuai Desain) ── --}}
<div class="warga-table-card">
    <div class="warga-table-card__header">
        <h3 class="warga-table-card__title" style="display:flex;align-items:center;gap:8px;">
            <i class='bx bx-history' style="color:#007bff;font-size:20px;"></i>
            Riwayat Pengajuan Surat Terakhir
        </h3>
        <a href="{{ route('warga.surat') }}" class="warga-table-link-more">
            Semua Berkas <i class='bx bx-right-arrow-alt'></i>
        </a>
    </div>

    <div class="warga-table-wrap">
        <table class="warga-dashboard-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">NO</th>
                    <th style="width: 45%;">JENIS SURAT KETERANGAN</th>
                    <th style="width: 20%;">TGL PERMOHONAN</th>
                    <th style="width: 15%;">STATUS</th>
                    <th style="width: 15%; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatSuratTerakhir as $idx => $s)
                @php
                    $wargaTujuanNama = $s->wargaTujuan?->nama ?? $s->pemohon?->nama ?? 'Warga';
                    $statusVal = is_object($s->status) ? $s->status->value : $s->status;
                @endphp
                <tr>
                    <td style="text-align: center; color: #6c757d; font-weight: 500;">{{ $idx + 1 }}</td>
                    <td>
                        <div class="warga-surat-title">Surat Pengantar RT</div>
                        <div style="font-size: 12px; color: #6c757d; margin-top: 2px;">
                            Keperluan: {{ $s->keperluan }} 
                            <span style="color:#0284c7;">({{ $wargaTujuanNama }})</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 13px; font-weight: 500;">
                            {{ $s->created_at->isoFormat('D MMM Y') }}
                        </div>
                        <div style="font-size: 11px; color: #6c757d;">
                            {{ $s->created_at->isoFormat('HH:mm') }} WIB
                        </div>
                    </td>
                    <td>
                        @if($statusVal === 'menunggu')
                            <span class="badge-warga-diproses">
                                <span style="font-size: 8px;">●</span> Diproses RT
                            </span>
                        @elseif($statusVal === 'disetujui')
                            <span class="badge-warga-selesai">
                                <i class='bx bx-check-circle'></i> Selesai / Terbit
                            </span>
                        @elseif($statusVal === 'ditolak')
                            <span style="background:#fee2e2;color:#b91c1c;border-radius:12px;padding:4px 12px;font-size:11.5px;font-weight:600;display:inline-flex;align-items:center;gap:5px;white-space:nowrap;">
                                <i class='bx bx-x-circle'></i> Ditolak
                            </span>
                        @else
                            <span style="background:#f1f5f9;color:#475569;border-radius:12px;padding:4px 12px;font-size:11.5px;font-weight:600;">
                                {{ ucfirst($statusVal) }}
                            </span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($statusVal === 'disetujui')
                            <a href="{{ route('warga.surat.cetak', $s->id) }}" class="btn-warga-table-download">
                                <i class='bx bx-download'></i> Unduh PDF
                            </a>
                        @else
                            <a href="{{ route('warga.surat') }}" class="btn-warga-table-detail">
                                <i class='bx bx-show'></i> Detail
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 32px; color: #6c757d; font-size: 13px;">
                        <i class='bx bx-file-blank' style="font-size: 32px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                        Belum ada riwayat permohonan surat pengantar dari keluarga Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="warga-table-card__footer">
        Update Realtime
    </div>
</div>

@endsection
