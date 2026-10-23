<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar RT 04 - {{ $surat->nomor_surat ?? 'DRAFT' }}</title>
    
    {{-- Design Tokens & Boxicons --}}
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/warga-surat.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body class="ws-cetak-body">

    @php
        $targetWarga = $surat->wargaTujuan ?? $surat->pemohon;
        $namaWarga = $targetWarga?->nama ?? '-';
        $tempatLahir = $targetWarga?->tempat_lahir ?? 'Batam';
        $tglLahir = $targetWarga?->tanggal_lahir ? $targetWarga->tanggal_lahir->translatedFormat('d F Y') : '-';
        $ttl = ($targetWarga?->tempat_lahir ? $targetWarga->tempat_lahir . ', ' : '') . $tglLahir;
        $jk = $targetWarga?->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : ($targetWarga?->jenis_kelamin === 'perempuan' ? 'Perempuan' : '-');
        $agama = ucfirst($targetWarga?->agama ?? 'Islam');
        $alamat = $targetWarga?->alamat ?? ($surat->pemohon?->alamat ?? 'RT 04');
        $nomorSurat = $surat->nomor_surat ?? '-';
        $tglSurat = $surat->updated_at ? $surat->updated_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
    @endphp

    {{-- Top Action Bar --}}
    <div class="ws-cetak-topbar">
        <a href="{{ route('warga.surat') }}" class="ws-btn-kembali-layar">
            <i class='bx bx-arrow-back'></i>
            <span>Kembali ke Pengajuan Surat</span>
        </a>
        <div class="ws-cetak-topbar-actions">
            <button type="button" class="ws-btn-print" onclick="window.print()">
                <i class='bx bx-printer'></i>
                <span>Cetak (Print)</span>
            </button>
            <button type="button" class="ws-btn-download-pdf" onclick="window.print()">
                <i class='bx bx-download'></i>
                <span>Unduh PDF</span>
            </button>
        </div>
    </div>

    {{-- Printable Paper Sheet (A4 Layout) --}}
    <div class="ws-paper-sheet" id="print-area">
        
        {{-- Kop Surat --}}
        <div class="ws-kop">
            <p class="ws-kop-instansi">RUKUN TETANGGA 04 KELURAHAN BELIAN</p>
            <p class="ws-kop-sub">KECAMATAN BATAM KOTA – KOTA BATAM</p>
            <p class="ws-kop-kota">Sekretariat: Jl. Melati Blok A No. 1, RT 04 Kode Pos: 29464</p>
        </div>
        <div class="ws-kop-line"></div>

        {{-- Judul Surat --}}
        <div class="ws-judul-surat-box">
            <h1 class="ws-judul-surat">SURAT PENGANTAR</h1>
            <p class="ws-nomor-surat">NO. {{ $nomorSurat }}</p>
        </div>

        {{-- Isi Surat --}}
        <div class="ws-surat-content">
            <p>
                Yang bertanda tangan di bawah ini Ketua RT. 04 Kelurahan Belian Kecamatan Batam Kota Kota Batam, menerangkan bahwa:
            </p>

            <table class="ws-surat-table-data">
                <tr>
                    <td class="ws-surat-num">1.</td>
                    <td class="ws-surat-lbl">Nama Lengkap</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">{{ $namaWarga }}</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">2.</td>
                    <td class="ws-surat-lbl">Tempat/Tgl Lahir</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">{{ $ttl }}</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">3.</td>
                    <td class="ws-surat-lbl">Jenis Kelamin</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">{{ $jk }}</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">4.</td>
                    <td class="ws-surat-lbl">Agama</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">{{ $agama }}</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">5.</td>
                    <td class="ws-surat-lbl">Alamat Domisili</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">{{ $alamat }}</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">6.</td>
                    <td class="ws-surat-lbl">Keperluan</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">
                        {{ $surat->keperluan }}
                    </td>
                </tr>
            </table>

            <p style="margin-top: 24px;">
                Demikian Surat keterangan pengantar ini dibuat dengan sebenarnya untuk dapat dipergunakan sesuai dengan keperluannya.
            </p>
        </div>

        {{-- Tanda Tangan Dua Pihak (Pemohon & Ketua RT) --}}
        <div class="ws-ttd-grid">
            <div class="ws-ttd-box">
                <p style="margin: 0;">&nbsp;</p>
                <p style="margin: 0; font-weight: bold;">Pemohon,</p>
                <div class="ws-ttd-space"></div>
                <span class="ws-ttd-nama">{{ $namaWarga }}</span>
            </div>

            <div class="ws-ttd-box">
                <p style="margin: 0;">Batam, {{ $tglSurat }}</p>
                <p style="margin: 0; font-weight: bold;">Ketua RT. 04,</p>
                <div class="ws-ttd-space">
                    <img src="{{ asset('tanda_tangan_rt.png') }}" alt="Tanda Tangan Ketua RT 04" class="ws-ttd-img-rt">
                </div>
                <span class="ws-ttd-nama">Supriyadi</span>
            </div>
        </div>

    </div>

    {{-- Bottom Validation Footer Banner --}}
    <div class="ws-cetak-footer-bar">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class='bx bx-info-circle' style="color: #0284c7; font-size: 16px;"></i>
            <span>Dokumen ini diterbitkan secara elektronik oleh Pengurus RT 04 Kelurahan Belian.</span>
        </div>
        <button type="button" class="ws-btn-salin-validasi" onclick="salinValidasi(this)">
            <i class='bx bx-link-alt'></i>
            <span>Salin Tautan Validasi</span>
        </button>
    </div>

    <script>
        function salinValidasi(btn) {
            navigator.clipboard.writeText(window.location.href).then(function() {
                var oldText = btn.innerHTML;
                btn.innerHTML = "<i class='bx bx-check'></i> Tautan Berhasil Disalin!";
                btn.style.background = "#dcfce7";
                btn.style.color = "#15803d";
                setTimeout(function() {
                    btn.innerHTML = oldText;
                    btn.style.background = "";
                    btn.style.color = "";
                }, 2000);
            });
        }
    </script>
</body>
</html>
