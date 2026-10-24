<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Surat Pengantar RT - WargaKu</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_wargaku.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/admin-surat.css') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top Header Bar ── */
        .preview-topbar {
            background: #ffffff;
            height: 60px;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .preview-topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-kembali-surat {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 7px 16px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .btn-kembali-surat:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .preview-topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            border-left: 2px solid #e2e8f0;
            padding-left: 16px;
        }

        .preview-topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-preview-action {
            height: 36px;
            padding: 0 16px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-preview-cetak {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #334155;
        }

        .btn-preview-cetak:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .btn-preview-tolak {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        .btn-preview-tolak:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #b91c1c;
        }

        .btn-preview-setuju {
            background: #15803d;
            border-color: #15803d;
            color: #ffffff;
        }

        .btn-preview-setuju:hover {
            background: #166534;
            border-color: #166534;
        }

        /* ── Alert Info Banner ── */
        .preview-alert-wrap {
            max-width: 820px;
            margin: 20px auto 14px auto;
            padding: 0 16px;
            width: 100%;
        }

        .preview-alert-banner {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #3730a3;
        }

        .preview-alert-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-alert-left i {
            font-size: 18px;
            color: #4f46e5;
        }

        .badge-format-a4 {
            background: #e0e7ff;
            color: #4338ca;
            padding: 3px 9px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Paper Simulation A4 ── */
        .preview-main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 10px 16px 40px 16px;
        }

        .surat-a4-paper {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 25mm 25mm 25mm 30mm; /* Atas 2.5cm, Bawah 2.5cm, Kanan 2.5cm, Kiri 3.0cm */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border-radius: 2px;
            position: relative;
            color: #000000;
            font-family: 'Times New Roman', Times, serif;
        }

        /* Kop Surat */
        .kop-surat-header {
            text-align: center;
            line-height: 1.3;
            margin-bottom: 2px;
        }

        .kop-rt-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-kel-title {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-kota-title {
            font-size: 13px;
            margin-top: 1px;
        }

        .kop-divider-double {
            border-top: 3px solid #000000;
            border-bottom: 1px solid #000000;
            height: 4px;
            margin-top: 6px;
            margin-bottom: 20px;
        }

        /* Document Title & Number */
        .doc-title-block {
            text-align: center;
            margin-bottom: 24px;
        }

        .doc-main-title {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 1px;
            display: inline-block;
        }

        .doc-nomor-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
        }

        /* Paragraphs & Data */
        .doc-paragraph {
            font-size: 14px;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 14px;
            text-indent: 30px;
        }

        .doc-data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            line-height: 1.5;
            margin: 12px 0 16px 0;
        }

        .doc-data-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .doc-label-col {
            width: 175px;
        }

        .doc-sep-col {
            width: 20px;
        }

        .doc-val-col {
            padding-right: 20px;
        }

        .doc-closing-paragraph {
            font-size: 14px;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 24px;
            text-indent: 30px;
        }

        /* Signatures */
        .doc-signature-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-top: 70px;
            margin-bottom: 35px;
            padding: 0;
            line-height: 1.5;
        }

        .doc-sig-box {
            width: 220px;
            text-align: center;
        }

        .doc-sig-space {
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .doc-sig-img {
            max-height: 75px;
            max-width: 150px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            filter: contrast(1.15);
        }

        .doc-sig-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print CSS (Atas 2.5cm, Bawah 2.5cm, Kiri 3cm, Kanan 2.5cm) */
        @page {
            size: A4 portrait;
            margin-top: 2.5cm;
            margin-bottom: 2.5cm;
            margin-left: 3cm;
            margin-right: 2.5cm;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .preview-topbar,
            .preview-alert-wrap,
            .surat-modal-overlay,
            .no-print {
                display: none !important;
            }
            .preview-main-content {
                padding: 0 !important;
                margin: 0 !important;
            }
            .surat-a4-paper {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body>

@php
    $statusStr = is_object($surat->status) ? $surat->status->value : $surat->status;
    $isDisetujui = ($statusStr === 'disetujui');
    $isDitolak = ($statusStr === 'ditolak');
    $isMenunggu = ($statusStr === 'menunggu');
    $nomorSurat = $surat->nomor_surat ?? $rekomendasiNomorSurat;

    $pemohon = $surat->pemohon?->nama ?? 'Warga RT';
    $tujuan = $surat->wargaTujuan?->nama ?? $pemohon;
    $nik = $surat->wargaTujuan?->nik ?? ($surat->pemohon?->nik ?? '-');
    $tempatLahir = $surat->wargaTujuan?->tempat_lahir ?? 'Batam';
    $rawTglLahir = $surat->wargaTujuan?->tanggal_lahir;
    $tglLahir = $rawTglLahir
        ? (\Illuminate\Support\Carbon::parse($rawTglLahir)->translatedFormat('d F Y'))
        : '-';
    $ttl = ($tempatLahir ?: '-') . ', ' . $tglLahir;

    $jk = is_object($surat->wargaTujuan?->jenis_kelamin)
        ? $surat->wargaTujuan->jenis_kelamin->label()
        : ($surat->wargaTujuan?->jenis_kelamin === 'laki_laki' ? 'Laki-laki' : ($surat->wargaTujuan?->jenis_kelamin === 'perempuan' ? 'Perempuan' : '-'));

    $agama = is_object($surat->wargaTujuan?->agama)
        ? $surat->wargaTujuan->agama->label()
        : ucfirst($surat->wargaTujuan?->agama ?? '-');

    $alamat = $surat->wargaTujuan?->alamat ?? ($surat->pemohon?->alamat ?? 'RT 04');
    $keperluan = $surat->keperluan;
    $rawCreatedAt = $surat->created_at;
    $tglSurat = $rawCreatedAt
        ? (\Illuminate\Support\Carbon::parse($rawCreatedAt)->translatedFormat('d F Y'))
        : now()->translatedFormat('d F Y');
@endphp

{{-- ═══ TOP NAVBAR ═══ --}}
<header class="preview-topbar no-print">
    <div class="preview-topbar-left">
        <a href="{{ route('admin.surat') }}" class="btn-kembali-surat">
            <i class='bx bx-arrow-back'></i> Kembali
        </a>
        <h1 class="preview-topbar-title">Pratinjau Surat Pengantar RT (Admin RT)</h1>
    </div>
    <div class="preview-topbar-right">
        {{-- Button Cetak / Unduh PDF --}}
        <button type="button" class="btn-preview-action btn-preview-cetak" onclick="window.print()" title="Cetak atau Unduh sebagai PDF">
            <i class='bx bx-printer'></i> Cetak / Unduh PDF
        </button>

        {{-- Tombol Tolak & Setujui hanya jika status masih Menunggu --}}
        @if($isMenunggu)
            <button type="button" class="btn-preview-action btn-preview-tolak" onclick="bukaModalTolakPreview()">
                <i class='bx bx-x-circle'></i> Tolak Permohonan
            </button>
            <button type="button" class="btn-preview-action btn-preview-setuju" onclick="bukaModalSetujuiPreview()">
                <i class='bx bx-check-circle'></i> Setujui &amp; Terbitkan
            </button>
        @elseif($isDisetujui)
            <span style="background: #ecfdf5; color: #047857; font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: 4px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 5px;">
                <i class='bx bx-check-circle'></i> Disetujui
            </span>
        @else
            <span style="background: #fef2f2; color: #b91c1c; font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: 4px; border: 1px solid #fecaca; display: inline-flex; align-items: center; gap: 5px;">
                <i class='bx bx-x-circle'></i> Ditolak
            </span>
        @endif
    </div>
</header>

{{-- ═══ ALERT INFO ═══ --}}
<div class="preview-alert-wrap no-print">
    @if($surat->pemohon?->status_warga === 'meninggal' || $surat->wargaTujuan?->status_warga === 'meninggal')
        <div style="background: #fff7ed; border: 1px solid #fdba74; border-radius: 6px; padding: 10px 18px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: #9a3412;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class='bx bx-error' style="font-size: 20px; color: #f97316;"></i>
                <span><strong>Catatan Status Kependudukan:</strong>
                    @if($surat->wargaTujuan?->status_warga === 'meninggal')
                        Warga yang bersangkutan ({{ $surat->wargaTujuan->nama }}) tercatat telah <strong>Meninggal Dunia</strong>.
                    @elseif($surat->pemohon?->status_warga === 'meninggal')
                        Pemohon awal surat ({{ $surat->pemohon->nama }}) tercatat telah <strong>Meninggal Dunia</strong>.
                    @endif
                </span>
            </div>
            <span class="badge-format-a4" style="background: #fee2e2; color: #b91c1c;">Almarhum</span>
        </div>
    @endif
    @if($isDitolak)
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 10px 18px; display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: #991b1b;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class='bx bx-error-circle' style="font-size: 20px; color: #ef4444;"></i>
                <span><strong>Permohonan Ditolak:</strong> {{ $surat->alasan ?? 'Berkas permohonan belum memenuhi syarat.' }}</span>
            </div>
            <span class="badge-format-a4" style="background: #fee2e2; color: #b91c1c;">Ditolak</span>
        </div>
    @else
        <div class="preview-alert-banner">
            <div class="preview-alert-left">
                <i class='bx bx-info-circle'></i>
                <span>Periksa kesesuaian data NIK &amp; domisili warga sebelum mencetak atau memberikan persetujuan penerbitan surat resmi.</span>
            </div>
            <span class="badge-format-a4">Format A4 Resmi</span>
        </div>
    @endif
</div>

{{-- ═══ A4 PAPER DOCUMENT ═══ --}}
<main class="preview-main-content">
    <div class="surat-a4-paper">
        {{-- Kop Surat Resmi --}}
        <div class="kop-surat-header">
            <div class="kop-rt-title">RUKUN TETANGGA 04 KELURAHAN BELIAN</div>
            <div class="kop-kel-title">KECAMATAN BATAM KOTA – KOTA BATAM</div>
            <div class="kop-kota-title">KOTA BATAM – 29464</div>
        </div>
        <div class="kop-divider-double"></div>

        {{-- Judul Surat & Nomor --}}
        <div class="doc-title-block">
            <div class="doc-main-title">SURAT PENGANTAR</div>
            <div class="doc-nomor-title">
                NO. <span id="label-doc-nomor">{{ $surat->nomor_surat ?? ($isDisetujui ? $nomorSurat : '..... / SP / RT04 / ' . now()->format('Y')) }}</span>
            </div>
        </div>

        {{-- Paragraf Pembuka --}}
        <p class="doc-paragraph">
            Yang bertanda tangan di bawah ini Ketua RT 04 Kelurahan Belian, Kecamatan Batam Kota, menerangkan bahwa:
        </p>

        {{-- Tabel Rincian Data Warga (diambil via relasi warga_tujuan_id sesuai PRD BR-10) --}}
        <table class="doc-data-table">
            <tr>
                <td class="doc-label-col">1. Nama Lengkap</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col" style="font-weight: bold;" id="label-doc-tujuan">{{ $tujuan }}</td>
            </tr>
            <tr>
                <td class="doc-label-col">2. Nomor NIK</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col" id="label-doc-nik">{{ $nik }}</td>
            </tr>
            <tr>
                <td class="doc-label-col">3. Tempat / Tgl Lahir</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col">{{ $ttl }}</td>
            </tr>
            <tr>
                <td class="doc-label-col">4. Jenis Kelamin</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col">{{ $jk }}</td>
            </tr>
            <tr>
                <td class="doc-label-col">5. Agama</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col">{{ $agama }}</td>
            </tr>
            <tr>
                <td class="doc-label-col">6. Alamat Domisili</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col" id="label-doc-alamat">{{ $alamat }}</td>
            </tr>
            <tr>
                <td class="doc-label-col">7. Keperluan</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col" style="font-weight: bold; text-decoration: underline;" id="label-doc-keperluan">{{ $keperluan }}</td>
            </tr>
        </table>

        {{-- Paragraf Penutup --}}
        <p class="doc-closing-paragraph">
            Demikian Surat Keterangan Pengantar ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        {{-- Blok Tanda Tangan --}}
        <div class="doc-signature-row" style="margin-top: 50px; margin-bottom: 20px;">
            <div class="doc-sig-box">
                <div>&nbsp;</div>
                <div style="margin-bottom: 6px;">Pemohon,</div>
                <div class="doc-sig-space" style="height: 85px;"></div>
                <div class="doc-sig-name" id="label-doc-pemohon">{{ $pemohon }}</div>
            </div>
            <div class="doc-sig-box">
                <div style="margin-bottom: 6px;" id="label-doc-tanggal">Batam, {{ $tglSurat }}</div>
                <div style="margin-bottom: 6px;">Ketua RT. 04</div>
                <div class="doc-sig-space" id="box-ttd-rt" style="height: 85px;">
                    @if($isDisetujui)
                        <img src="{{ asset('tanda_tangan_rt.png') }}" 
                             alt="Tanda Tangan Ketua RT 04" 
                             class="doc-sig-img" 
                             id="img-ttd-rt"
                             onerror="this.style.display='none'; document.getElementById('alt-ttd-rt').style.display='block';">
                        <div id="alt-ttd-rt" style="display: none; font-size: 12px; font-weight: 700; color: #15803d; border: 1px dashed #86efac; padding: 4px 8px; border-radius: 4px;">
                            (Telah Ditandatangani)
                        </div>
                    @else
                        <div id="empty-ttd-rt" class="no-print" style="font-size: 11px; color: #94a3b8; font-style: italic; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 6px 8px; background: #f8fafc;">
                            (Menunggu Persetujuan RT)
                        </div>
                    @endif
                </div>
                <div class="doc-sig-name">Supriyadi</div>
            </div>
        </div>
    </div>
</main>

{{-- ═══ MODAL PERSETUJUAN SURAT (2.2.14) ═══ --}}
@if($isMenunggu)
<div class="surat-modal-overlay" id="modal-persetujuan-preview" onclick="if(event.target===this) tutupModalSetujuiPreview()">
    <div class="surat-modal-dialog">
        <form method="POST" action="{{ route('admin.surat.setujui', $surat->id) }}">
            @csrf
            <div class="surat-modal-header surat-modal-header--green">
                <h3 class="surat-modal-title">
                    <i class='bx bx-check-shield' style="font-size: 19px;"></i>
                    Persetujuan &amp; Penerbitan Surat Pengantar
                </h3>
                <button type="button" class="surat-modal-close-btn" onclick="tutupModalSetujuiPreview()">&times;</button>
            </div>
            <div class="surat-modal-body">
                <div class="surat-modal-info-block">
                    <div class="surat-modal-info-row">
                        <span class="surat-modal-info-label">Pemohon:</span>
                        <span class="surat-modal-info-val surat-modal-info-val--blue">{{ $pemohon }}</span>
                    </div>
                    <div class="surat-modal-info-row">
                        <span class="surat-modal-info-label">Warga Tujuan:</span>
                        <span class="surat-modal-info-val surat-modal-info-val--dark">{{ $tujuan }}</span>
                    </div>
                </div>

                <div class="surat-modal-grid-2">
                    <div class="surat-modal-field">
                        <label class="surat-modal-label" for="modal-input-nomor-prev">
                            <i class='bx bx-id-card'></i> Nomor Surat Resmi RT
                            <span style="font-size: 10.5px; background: #e0f2fe; color: #0369a1; padding: 2px 7px; border-radius: 4px; font-weight: 600; margin-left: 6px;">
                                <i class='bx bx-bot'></i> Otomatis Sistem
                            </span>
                        </label>
                        <input type="text" name="nomor_surat" id="modal-input-nomor-prev" class="surat-modal-input" readonly style="background: #f8fafc; font-weight: 700; color: #0f172a; cursor: not-allowed;" value="{{ $rekomendasiNomorSurat }}">
                        <div class="surat-modal-subtext">Dibuat otomatis berurutan sesuai aturan BR-05 (reset tiap tahun)</div>
                    </div>

                    <div class="surat-modal-field">
                        <label class="surat-modal-label" for="modal-input-tanggal-prev">
                            <i class='bx bx-calendar'></i> Tanggal Penerbitan <span class="req">*</span>
                        </label>
                        <input type="text" id="modal-input-tanggal-prev" class="surat-modal-input" readonly value="{{ now()->translatedFormat('d F Y') }}">
                        <div class="surat-modal-subtext">Tanggal tertera pada surat pengantar</div>
                    </div>
                </div>
            </div>
            <div class="surat-modal-footer">
                <button type="button" class="btn-modal-batal" onclick="tutupModalSetujuiPreview()">Batal</button>
                <button type="submit" class="btn-modal-simpan">
                    <i class='bx bx-check'></i> Simpan &amp; Terbitkan Surat
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL PENOLAKAN SURAT (2.2.15) ═══ --}}
<div class="surat-modal-overlay" id="modal-tolak-preview" onclick="if(event.target===this) tutupModalTolakPreview()">
    <div class="surat-modal-dialog">
        <form method="POST" action="{{ route('admin.surat.tolak', $surat->id) }}">
            @csrf
            <div class="surat-modal-header surat-modal-header--red">
                <h3 class="surat-modal-title">
                    <i class='bx bx-x-circle' style="font-size: 19px;"></i>
                    Penolakan Permohonan Surat Pengantar
                </h3>
                <button type="button" class="surat-modal-close-btn" onclick="tutupModalTolakPreview()">&times;</button>
            </div>
            <div class="surat-modal-body">
                <div class="surat-modal-info-block">
                    <div class="surat-modal-info-row">
                        <span class="surat-modal-info-label">Pemohon:</span>
                        <span class="surat-modal-info-val surat-modal-info-val--blue">{{ $pemohon }}</span>
                    </div>
                    <div class="surat-modal-info-row">
                        <span class="surat-modal-info-label">Warga Tujuan:</span>
                        <span class="surat-modal-info-val surat-modal-info-val--dark">{{ $tujuan }}</span>
                    </div>
                </div>

                <div class="surat-modal-grid-2">
                    <div class="surat-modal-field">
                        <label class="surat-modal-label" for="modal-input-alasan-prev">
                            <i class='bx bx-message-error' style="color: #dc3545;"></i> Alasan Penolakan RT
                        </label>
                        <input type="text" name="alasan" id="modal-input-alasan-prev" class="surat-modal-input" placeholder="Masukkan alasan penolakan..." value="">
                        <div class="surat-modal-subtext">Catatan ini akan tersimpan pada detail penolakan</div>
                    </div>

                    <div class="surat-modal-field">
                        <label class="surat-modal-label" for="modal-input-tanggal-tolak-prev">
                            <i class='bx bx-calendar'></i> Tanggal Penolakan
                        </label>
                        <input type="text" id="modal-input-tanggal-tolak-prev" class="surat-modal-input" readonly value="{{ now()->translatedFormat('d F Y') }}">
                        <div class="surat-modal-subtext">Tanggal tercatat dalam sistem administrasi</div>
                    </div>
                </div>
            </div>
            <div class="surat-modal-footer">
                <button type="button" class="btn-modal-batal" onclick="tutupModalTolakPreview()">Batal</button>
                <button type="submit" class="btn-modal-tolak">
                    <i class='bx bx-x'></i> Tolak Permohonan Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function bukaModalSetujuiPreview() {
        var el = document.getElementById('modal-persetujuan-preview');
        if (el) el.style.display = 'flex';
    }

    function tutupModalSetujuiPreview() {
        var el = document.getElementById('modal-persetujuan-preview');
        if (el) el.style.display = 'none';
    }

    function bukaModalTolakPreview() {
        var el = document.getElementById('modal-tolak-preview');
        if (el) el.style.display = 'flex';
    }

    function tutupModalTolakPreview() {
        var el = document.getElementById('modal-tolak-preview');
        if (el) el.style.display = 'none';
    }
</script>

</body>
</html>
