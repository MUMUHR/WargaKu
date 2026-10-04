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
            color: #312e81;
        }

        .preview-alert-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .preview-alert-left i {
            font-size: 18px;
            color: #4f46e5;
        }

        .badge-format-a4 {
            background: #e0e7ff;
            color: #3730a3;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* ── Paper Document (A4 Container) ── */
        .preview-main-content {
            flex: 1;
            padding: 0 16px 40px 16px;
            display: flex;
            justify-content: center;
        }

        .surat-a4-paper {
            width: 794px; /* Standard A4 at 96 DPI */
            min-height: 1123px;
            background: #ffffff;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
            border-radius: 2px;
            padding: 55px 75px;
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            line-height: 1.5;
            position: relative;
        }

        /* Kop Surat */
        .kop-surat-header {
            text-align: center;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .kop-rt-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .kop-kel-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-kota-title {
            font-size: 14.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-divider-double {
            border-top: 3px solid #000000;
            border-bottom: 1px solid #000000;
            height: 2px;
            margin: 10px 0 24px 0;
        }

        /* Title Surat */
        .doc-title-block {
            text-align: center;
            margin-bottom: 24px;
        }

        .doc-main-title {
            font-size: 16.5px;
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
            line-height: 1.65;
            text-align: justify;
            margin-bottom: 14px;
            text-indent: 30px;
        }

        .doc-data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            line-height: 1.85;
            margin: 10px 0 18px 20px;
        }

        .doc-data-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .doc-label-col {
            width: 165px;
        }

        .doc-sep-col {
            width: 18px;
        }

        .doc-val-col {
            padding-right: 30px;
        }

        .doc-closing-paragraph {
            font-size: 14px;
            line-height: 1.65;
            text-align: justify;
            margin-bottom: 35px;
            text-indent: 30px;
        }

        /* Signatures */
        .doc-signature-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 35px;
            padding: 0 10px;
        }

        .doc-sig-box {
            width: 220px;
            text-align: center;
        }

        .doc-sig-space {
            height: 70px;
        }

        .doc-sig-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .doc-sig-center {
            text-align: center;
            font-size: 14px;
        }

        /* Print CSS */
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
                padding: 40px 50px !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body>

@php
    $id = $id ?? 1;
    $status = $surat['status'] ?? 'Menunggu';
    $nomorSurat = $surat['nomor_surat'] ?? null;
    $pemohon = $surat['pemohon_nama'] ?? 'Bambang Santoso, S.T.';
    $tujuan = $surat['warga_tujuan'] ?? $pemohon;
    $nik = $surat['pemohon_nik'] ?? '3275012304780001';
    $alamat = $surat['pemohon_alamat'] ?? 'Blok B4 No. 12';
    $keperluan = $surat['keperluan'] ?? 'Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan rekrutmen BUMN';
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
        {{-- Button Cetak / Unduh PDF selalu tampil --}}
        <button type="button" class="btn-preview-action btn-preview-cetak" onclick="window.print()" title="Cetak atau Unduh sebagai PDF">
            <i class='bx bx-printer'></i> Cetak / Unduh PDF
        </button>

        {{-- Tombol Tolak & Setujui hanya jika status belum Disetujui --}}
        <div id="wrap-action-pending" style="{{ $status === 'Disetujui' ? 'display: none;' : 'display: flex; gap: 10px;' }}">
            <button type="button" class="btn-preview-action btn-preview-tolak" onclick="bukaModalTolakPreview()">
                <i class='bx bx-x-circle'></i> Tolak Permohonan
            </button>
            <button type="button" class="btn-preview-action btn-preview-setuju" onclick="bukaModalSetujuiPreview()">
                <i class='bx bx-check-circle'></i> Setujui &amp; Terbitkan
            </button>
        </div>
    </div>
</header>

{{-- ═══ ALERT INFO ═══ --}}
<div class="preview-alert-wrap no-print">
    <div class="preview-alert-banner">
        <div class="preview-alert-left">
            <i class='bx bx-info-circle'></i>
            <span>Periksa kesesuaian data NIK &amp; domisili warga sebelum memberikan persetujuan penerbitan surat resmi.</span>
        </div>
        <span class="badge-format-a4">Format A4 Resmi</span>
    </div>
</div>

{{-- ═══ A4 PAPER DOCUMENT ═══ --}}
<main class="preview-main-content">
    <div class="surat-a4-paper">
        {{-- Kop Surat Resmi --}}
        <div class="kop-surat-header">
            <div class="kop-rt-title">RUKUN TETANGGA 04 PERUMAHAN SUKAMAJU INDAH</div>
            <div class="kop-kel-title">KELURAHAN SUKAMAJU, KECAMATAN CILODONG</div>
            <div class="kop-kota-title">KOTA MUHAR – 16415</div>
        </div>
        <div class="kop-divider-double"></div>

        {{-- Judul Surat & Nomor --}}
        <div class="doc-title-block">
            <div class="doc-main-title">SURAT PENGANTAR</div>
            <div class="doc-nomor-title">
                NO. <span id="label-doc-nomor">{{ $nomorSurat ?? '..... / SP / RT04 / II / ' . date('Y') }}</span>
            </div>
        </div>

        {{-- Paragraf Pembuka --}}
        <p class="doc-paragraph">
            Yang bertanda tangan di bawah ini Ketua RT. 04 Kelurahan Sukamaju Kecamatan Cilodong, Menerangkan bahwa :
        </p>

        {{-- Tabel Rincian Data Warga --}}
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
                <td class="doc-label-col">3. Tempat/Tgl Lahir</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col">Depok, 14 Februari 1988</td>
            </tr>
            <tr>
                <td class="doc-label-col">4. Jenis Kelamin</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col">Laki-laki / Perempuan</td>
            </tr>
            <tr>
                <td class="doc-label-col">5. Agama</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col">Islam</td>
            </tr>
            <tr>
                <td class="doc-label-col">6. Alamat Domisili</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col" id="label-doc-alamat">Perumahan Sukamaju Indah {{ $alamat }}, RT. 04 Kelurahan Sukamaju, Kecamatan Cilodong.</td>
            </tr>
            <tr>
                <td class="doc-label-col">7. Keperluan</td>
                <td class="doc-sep-col">:</td>
                <td class="doc-val-col" style="font-weight: bold; text-decoration: underline;" id="label-doc-keperluan">{{ $keperluan }}</td>
            </tr>
        </table>

        {{-- Paragraf Penutup --}}
        <p class="doc-closing-paragraph">
            Demikian Surat keterangan ini dibuat untuk dapat dipergunakan sesuai dengan keperluannya.
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
                <div style="margin-bottom: 6px;" id="label-doc-tanggal">Depok, {{ date('d F Y') }}</div>
                <div style="margin-bottom: 6px;">Ketua RT. 04</div>
                <div class="doc-sig-space" style="height: 85px;"></div>
                <div class="doc-sig-name">Supriyadi</div>
            </div>
        </div>
    </div>
</main>

{{-- ═══ MODAL PERSETUJUAN SURAT ═══ --}}
<div class="surat-modal-overlay" id="modal-persetujuan-preview" onclick="if(event.target===this) tutupModalSetujuiPreview()">
    <div class="surat-modal-dialog">
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
                        <i class='bx bx-id-card'></i> Nomor Surat Resmi RT <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-input-nomor-prev" class="surat-modal-input" value="05/SP/RT04/II/2026">
                    <div class="surat-modal-subtext">Format: [No]/SP/RT04/[Bulan]/[Tahun]</div>
                </div>

                <div class="surat-modal-field">
                    <label class="surat-modal-label" for="modal-input-tanggal-prev">
                        <i class='bx bx-calendar'></i> Tanggal Penerbitan <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-input-tanggal-prev" class="surat-modal-input" readonly value="{{ date('m/d/Y') }}">
                    <div class="surat-modal-subtext">Tanggal tertera pada surat pengantar</div>
                </div>
            </div>
        </div>
        <div class="surat-modal-footer">
            <button type="button" class="btn-modal-batal" onclick="tutupModalSetujuiPreview()">Batal</button>
            <button type="button" class="btn-modal-simpan" onclick="simpanPersetujuanDariPreview()">
                <i class='bx bx-check'></i> Simpan &amp; Terbitkan Surat
            </button>
        </div>
    </div>
</div>

{{-- ═══ MODAL PENOLAKAN SURAT ═══ --}}
<div class="surat-modal-overlay" id="modal-tolak-preview" onclick="if(event.target===this) tutupModalTolakPreview()">
    <div class="surat-modal-dialog">
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
                        <i class='bx bx-message-error' style="color: #dc3545;"></i> Alasan Penolakan RT <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-input-alasan-prev" class="surat-modal-input" value="Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.">
                    <div class="surat-modal-subtext">Catatan ini akan tersimpan pada detail penolakan</div>
                </div>

                <div class="surat-modal-field">
                    <label class="surat-modal-label" for="modal-input-tanggal-tolak-prev">
                        <i class='bx bx-calendar'></i> Tanggal Penolakan <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-input-tanggal-tolak-prev" class="surat-modal-input" readonly value="{{ date('m/d/Y') }}">
                    <div class="surat-modal-subtext">Tanggal tercatat dalam sistem administrasi</div>
                </div>
            </div>
        </div>
        <div class="surat-modal-footer">
            <button type="button" class="btn-modal-batal" onclick="tutupModalTolakPreview()">Batal</button>
            <button type="button" class="btn-modal-tolak" onclick="simpanPenolakanDariPreview()">
                <i class='bx bx-x'></i> Tolak Permohonan Surat
            </button>
        </div>
    </div>
</div>

<script>
    const SURAT_ID = {{ $id }};

    // Sinkronisasi status dari localStorage jika ada
    document.addEventListener('DOMContentLoaded', function() {
        var savedStatus = localStorage.getItem('surat_status_' + SURAT_ID);
        var savedNomor = localStorage.getItem('surat_nomor_' + SURAT_ID);

        if (savedStatus === 'disetujui' || '{{ $status }}' === 'Disetujui') {
            tampilkanModeDisetujui(savedNomor || '{{ $nomorSurat ?? "04/SP/RT04/II/2026" }}');
        }
    });

    function tampilkanModeDisetujui(noSurat) {
        var wrapPending = document.getElementById('wrap-action-pending');
        if (wrapPending) wrapPending.style.display = 'none';

        var labelNomor = document.getElementById('label-doc-nomor');
        if (labelNomor) labelNomor.textContent = noSurat;
    }

    // Modal Persetujuan Handlers
    function bukaModalSetujuiPreview() {
        document.getElementById('modal-persetujuan-preview').style.display = 'flex';
    }

    function tutupModalSetujuiPreview() {
        document.getElementById('modal-persetujuan-preview').style.display = 'none';
    }

    function simpanPersetujuanDariPreview() {
        var noSurat = (document.getElementById('modal-input-nomor-prev').value || '05/SP/RT04/II/2026').trim();

        // Simpan ke localStorage agar tabel utama di /admin/surat otomatis update
        localStorage.setItem('surat_status_' + SURAT_ID, 'disetujui');
        localStorage.setItem('surat_nomor_' + SURAT_ID, noSurat);

        tampilkanModeDisetujui(noSurat);
        tutupModalSetujuiPreview();
    }

    // Modal Penolakan Handlers
    function bukaModalTolakPreview() {
        document.getElementById('modal-tolak-preview').style.display = 'flex';
    }

    function tutupModalTolakPreview() {
        document.getElementById('modal-tolak-preview').style.display = 'none';
    }

    function simpanPenolakanDariPreview() {
        var alasan = (document.getElementById('modal-input-alasan-prev').value || 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.').trim();

        // Simpan status ditolak ke localStorage
        localStorage.setItem('surat_status_' + SURAT_ID, 'ditolak');
        localStorage.setItem('surat_alasan_' + SURAT_ID, alasan);

        tutupModalTolakPreview();

        // Kembali ke halaman daftar surat
        window.location.href = "{{ route('admin.surat') }}";
    }
</script>

</body>
</html>
