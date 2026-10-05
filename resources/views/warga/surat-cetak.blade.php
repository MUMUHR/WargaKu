<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar RT 04 - 470/882/RT04/X/2025</title>
    
    {{-- Design Tokens & Boxicons --}}
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/warga-surat.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body class="ws-cetak-body">

    {{-- Top Action Bar --}}
    <div class="ws-cetak-topbar">
        <a href="{{ route('warga.surat') }}" class="ws-btn-kembali-layar">
            <i class='bx bx-arrow-back'></i>
            <span>Kembali ke Layar Pengajuan Surat</span>
        </a>
        <div class="ws-cetak-topbar-actions">
            <button type="button" class="ws-btn-print" onclick="window.print()">
                <i class='bx bx-printer'></i>
                <span>Cetak Surat (Print)</span>
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
            <p class="ws-kop-instansi">RUKUN TETANGGA 04 / RW 08 KELURAHAN BARANANGSIANG</p>
            <p class="ws-kop-sub">KECAMATAN BOGOR TIMUR – KOTA BOGOR</p>
            <p class="ws-kop-kota">Sekretariat: Jl. Melati Blok B No. 14, RT 04 / RW 08 Kode Pos: 16143</p>
        </div>
        <div class="ws-kop-line"></div>

        {{-- Judul Surat --}}
        <div class="ws-judul-surat-box">
            <h1 class="ws-judul-surat">SURAT PENGANTAR</h1>
            <p class="ws-nomor-surat">NO. 470 / 882 / RT04 / X / 2025</p>
        </div>

        {{-- Isi Surat --}}
        <div class="ws-surat-content">
            <p>
                Yang bertanda tangan di bawah ini Ketua RT. 04 RW. 08 Kelurahan Baranangsiang Kecamatan Bogor Timur Kota Bogor, menerangkan bahwa:
            </p>

            <table class="ws-surat-table-data">
                <tr>
                    <td class="ws-surat-num">1.</td>
                    <td class="ws-surat-lbl">Nama Lengkap</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">Dimas Arya Pratama</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">2.</td>
                    <td class="ws-surat-lbl">Tempat/Tgl Lahir</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">Bogor, 08 Agustus 2005</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">3.</td>
                    <td class="ws-surat-lbl">Jenis Kelamin</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">Laki-Laki</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">4.</td>
                    <td class="ws-surat-lbl">Agama</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">Islam</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">5.</td>
                    <td class="ws-surat-lbl">Alamat</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">Jl. Melati Blok B No. 14, RT 04 / RW 08 Kel. Baranangsiang Kec. Bogor Timur Kota Bogor</td>
                </tr>
                <tr>
                    <td class="ws-surat-num">6.</td>
                    <td class="ws-surat-lbl">Keperluan</td>
                    <td class="ws-surat-sep">:</td>
                    <td class="ws-surat-val">
                        Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan rekrutmen BUMN
                    </td>
                </tr>
            </table>

            <p style="margin-top: 24px;">
                Demikian Surat keterangan ini dibuat untuk dapat dipergunakan sesuai dengan keperluannya.
            </p>
        </div>

        {{-- Tanda Tangan Tiga Pihak --}}
        <div class="ws-ttd-grid">
            <div class="ws-ttd-box">
                <p style="margin: 0;">&nbsp;</p>
                <p style="margin: 0; font-weight: bold;">Pemohon,</p>
                <div class="ws-ttd-space"></div>
                <span class="ws-ttd-nama">Dimas Arya Pratama</span>
            </div>

            <div class="ws-ttd-box">
                <p style="margin: 0;">Bogor, 24 Oktober 2025</p>
                <p style="margin: 0; font-weight: bold;">Ketua RT. 04 RW. 08,</p>
                <div class="ws-ttd-space">
                    <img src="{{ asset('tanda_tangan_rt.png') }}" alt="Tanda Tangan Ketua RT 04" class="ws-ttd-img-rt">
                </div>
                <span class="ws-ttd-nama">Supriyadi</span>
            </div>
        </div>

        <div class="ws-ttd-tengah">
            <p style="margin: 0; font-weight: bold;">Mengetahui,</p>
            <p style="margin: 0; font-weight: bold;">Ketua RW. 08</p>
            <div class="ws-ttd-space"></div>
            <span class="ws-ttd-nama">Hendra Wijaya</span>
        </div>

    </div>

    {{-- Bottom Validation Footer Banner --}}
    <div class="ws-cetak-footer-bar">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class='bx bx-info-circle' style="color: #0284c7; font-size: 16px;"></i>
            <span>Dokumen ini diterbitkan secara elektronik oleh Pengurus RT 04 RW 08 Kelurahan Baranangsiang.</span>
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
