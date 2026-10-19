@extends('layouts.admin')

@section('title', 'Kelola & Rekap Iuran Warga')
@section('meta_description', 'Monitoring penerimaan kas lingkungan, verifikasi pembayaran rutin bulanan, dan status tunggakan RT 04 / RW 08 Sukamaju.')

@section('breadcrumb')
    <span class="admin-topbar__breadcrumb-current">Kelola &amp; Rekap Iuran</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-iuran.css') }}">
@endpush

@section('content')

@php
    $daftarBulanSingkat = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    $daftarBulanLengkap = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
@endphp

{{-- ========================================================
     HEADER HALAMAN
     ======================================================== --}}
<div class="admin-page-header">
    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
        <h1 class="admin-page-header__title" id="page-title" style="margin:0;">Kelola &amp; Rekap Iuran Warga</h1>
        <span class="badge-periode-aktif" id="header-periode-badge">Periode Aktif: {{ $periodeAktifTeks }}</span>
    </div>
    <div>
        <button type="button" class="btn-atur-iuran" onclick="bukaModalAturIuran()">
            <i class='bx bx-cog'></i> Atur Iuran &amp; Catatan Bulanan
        </button>
    </div>
</div>

{{-- Notification Messages --}}
@if(session('success'))
<div style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px;">
    <i class='bx bx-check-circle' style="font-size:18px;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px;">
    <i class='bx bx-error-circle' style="font-size:18px;"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- ========================================================
     TOP STAT CARDS: 3 ADMINLTE SMALL BOXES
     ======================================================== --}}
<div class="iuran-stats-grid">
    <div class="iuran-stat-box iuran-stat-box--green" style="cursor: pointer;" onclick="switchIuranTab('riwayat')" title="Lihat riwayat penerimaan kas">
        <div class="iuran-stat-box__val" id="stat-total-kas">Rp {{ number_format($totalPenerimaanKas, 0, ',', '.') }}</div>
        <div class="iuran-stat-box__label">Total Penerimaan Kas</div>
        <i class='bx bx-wallet iuran-stat-box__icon'></i>
    </div>
    <div class="iuran-stat-box iuran-stat-box--blue" style="cursor: pointer;" onclick="switchIuranTab('riwayat')" title="Lihat KK telah lunas">
        <div class="iuran-stat-box__val" id="stat-kk-lunas">{{ $totalKkLunasBulanIni }} KK</div>
        <div class="iuran-stat-box__label">KK Telah Lunas Bulan Ini</div>
        <i class='bx bx-group iuran-stat-box__icon'></i>
    </div>
    <div class="iuran-stat-box iuran-stat-box--red" style="cursor: pointer;" onclick="switchIuranTab('tunggakan')" title="Lihat KK belum membayar">
        <div class="iuran-stat-box__val" id="stat-kk-belum">{{ $totalKkBelumBulanIni }} KK</div>
        <div class="iuran-stat-box__label">KK Belum Membayar Bulan Ini</div>
        <i class='bx bx-clipboard iuran-stat-box__icon'></i>
    </div>
</div>

{{-- ========================================================
     CARD CONTAINER: TABS & DATA TABLES
     ======================================================== --}}
<div class="iuran-card-container">

    {{-- Tabs Navigation Bar --}}
    <div class="iuran-tabs-nav">
        <div class="iuran-tabs-left">
            <button type="button" class="iuran-tab-btn iuran-tab-btn--active" id="tab-btn-tunggakan" onclick="switchIuranTab('tunggakan')">
                Daftar Tunggakan KK <span class="badge-tab-count" id="badge-tab-tunggakan">{{ $totalKkBelumBulanIni }} KK Belum Lunas</span>
            </button>
            <button type="button" class="iuran-tab-btn" id="tab-btn-riwayat" onclick="switchIuranTab('riwayat')">
                Riwayat Penerimaan Kas Transaksi</span>
            </button>
        </div>
      
    </div>

    {{-- ═══ TAB 1: DAFTAR TUNGGAKAN KK ═══ --}}
    <div id="pane-tab-tunggakan">
        {{-- Toolbar Search & Info --}}
        <div class="iuran-toolbar-row">
            <div class="iuran-toolbar-left">
                <div class="iuran-search-wrap">
                    <i class='bx bx-search iuran-search-icon'></i>
                    <input type="text" id="search-tunggakan-input" class="iuran-search-input"
                           placeholder="Cari Nama Kepala Keluarga, Blok, atau No. KK..."
                           onkeyup="filterTunggakanTable()">
                </div>
                <select id="filter-tunggakan-ekonomi" class="iuran-filter-select" onchange="filterTunggakanTable()">
                    <option value="">Semua Status Ekonomi</option>
                    <option value="mampu">Ekonomi: Mampu</option>
                    <option value="kurang mampu">Ekonomi: Kurang Mampu</option>
                </select>
                <select id="filter-tunggakan-periode" class="iuran-filter-select" onchange="filterTunggakanTable()">
                    <option value="bulan_ini" selected>Bulan Berjalan ({{ $daftarBulanSingkat[$bulanBerjalan] ?? 'Okt' }} {{ $tahunBerjalan }})</option>
                    <option value="">Semua Periode</option>
                    <option value="tunggakan_lampau">Tunggakan Lampau (&lt; Bulan Ini)</option>
                    <option value="bulan_mendatang">Tagihan Mendatang (&gt; Bulan Ini)</option>
                </select>
            </div>
            <div class="iuran-besaran-tag" id="tag-besaran-toolbar">
                <i class='bx bx-money'></i>
                <span>Besaran Iuran: <strong id="val-besaran-toolbar">Rp {{ number_format($nominalWajibSaatIni, 0, ',', '.') }} / KK</strong></span>
            </div>
        </div>

        {{-- Table Tunggakan --}}
        <div class="iuran-table-wrap">
            <table class="iuran-table" id="table-tunggakan">
                <colgroup>
                    <col style="width: 45px;">
                    <col style="width: 180px;">
                    <col style="width: 230px;">
                    <col style="width: 110px;">
                    <col style="width: 125px;">
                    <col style="width: 125px;">
                    <col style="width: 160px;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">NO</th>
                        <th>NOMOR KK / EKONOMI</th>
                        <th>NAMA KEPALA KELUARGA &amp; BLOK</th>
                        <th style="text-align: center;">PERIODE</th>
                        <th>NOMINAL</th>
                        <th style="text-align: center;">STATUS</th>
                        <th style="text-align: center;">AKSI OPERASIONAL</th>
                    </tr>
                </thead>
                <tbody id="tbody-tunggakan">
                    @forelse($daftarTunggakan as $idx => $t)
                    @php
                        $noKk = $t->keluarga?->no_kk ?? '-';
                        $statusEkoLabel = ($t->keluarga?->status_ekonomi === 'kurang_mampu') ? 'EKONOMI: KURANG MAMPU' : 'EKONOMI: MAMPU';
                        $kepala = $t->keluarga?->kepalaKeluarga ?? $t->keluarga?->wargas?->first();
                        $namaKepala = $kepala?->nama ?? 'Kepala Keluarga';
                        $blokAlamat = $kepala?->alamat ?? 'RT 04 / RW 08';
                        $singkatBulan = $daftarBulanSingkat[$t->bulan] ?? $t->bulan;
                        $lengkapBulan = $daftarBulanLengkap[$t->bulan] ?? $t->bulan;
                        $periodeTeks = "{$singkatBulan} {$t->tahun}";
                        $periodeLengkap = "{$lengkapBulan} {$t->tahun}";
                        $nominalFmt = 'Rp ' . number_format($t->nominal, 0, ',', '.');
                    @endphp
                    <tr class="row-tunggakan-item"
                        id="row-tunggakan-{{ $t->id }}"
                        data-id="{{ $t->id }}"
                        data-nokk="{{ $noKk }}"
                        data-nama="{{ strtolower($namaKepala) }}"
                        data-nama-raw="{{ $namaKepala }}"
                        data-blok="{{ strtolower($blokAlamat) }}"
                        data-blok-raw="{{ $blokAlamat }}"
                        data-periode="{{ $periodeTeks }}"
                        data-bulan="{{ $t->bulan }}"
                        data-tahun="{{ $t->tahun }}"
                        data-nominal="{{ $nominalFmt }}"
                        data-ekonomi="{{ strtolower($statusEkoLabel) }}">
                        <td style="text-align: center; color: #495057; font-weight: 500;">{{ $idx + 1 }}</td>
                        <td>
                            <div class="no-kk-text">{{ $noKk }}</div>
                            <div class="sub-ekonomi-text">{{ $statusEkoLabel }}</div>
                        </td>
                        <td>
                            <div class="nama-warga-text">{{ $namaKepala }}</div>
                            <div class="blok-sub-text">
                                <i class='bx bx-home'></i> {{ $blokAlamat }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-periode-pill">{{ $periodeTeks }}</span>
                        </td>
                        <td>
                            <div class="nominal-text">{{ $nominalFmt }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status-belum">
                                <i class='bx bx-x-circle'></i> Belum Lunas
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn-tandai-lunas"
                                    onclick="bukaModalTandaiLunas({{ $t->id }}, '{{ addslashes($namaKepala) }}', '{{ $noKk }}', '{{ $periodeLengkap }}', '{{ $nominalFmt }}', '{{ addslashes($blokAlamat) }}', {{ (float) $t->nominal }})"
                                    title="Catat dan verifikasi pembayaran lunas">
                                <i class='bx bx-check-circle'></i> Tandai Lunas
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:36px;color:#6c757d;font-size:13px;">
                            <i class='bx bx-check-double' style="font-size:32px;display:block;margin-bottom:8px;color:#28a745;"></i>
                            Semua tagihan iuran warga telah lunas atau belum ada tagihan tunggakan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Bar --}}
        <div class="iuran-pagination-bar">
            <div id="info-tunggakan-count">Menampilkan <strong>{{ count($daftarTunggakan) }}</strong> data KK Belum Lunas RT 04</div>
            <div class="iuran-pagination" id="pagination-tunggakan"></div>
        </div>
    </div>

    {{-- ═══ TAB 2: RIWAYAT PENERIMAAN KAS ═══ --}}
    <div id="pane-tab-riwayat" style="display: none;">
        {{-- Toolbar Search & Filter Bulan/Tahun --}}
        <div class="iuran-toolbar-row">
            <div class="iuran-toolbar-left">
                <div class="iuran-search-wrap">
                    <i class='bx bx-search iuran-search-icon'></i>
                    <input type="text" id="search-riwayat-input" class="iuran-search-input"
                           placeholder="Cari riwayat transaksi atau nama KK..."
                           onkeyup="filterRiwayatTable()">
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:12px;color:#6c757d;font-weight:600;white-space:nowrap;">Filter Bulan:</span>
                    <select id="filter-riwayat-bulan" class="iuran-filter-select" onchange="filterRiwayatTable()">
                        <option value="">Semua Bulan</option>
                        @foreach($daftarBulanLengkap as $bIdx => $bNama)
                            <option value="{{ $bIdx }}" {{ (int) $bIdx === (int) $bulanBerjalan ? 'selected' : '' }}>{{ $bNama }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:12px;color:#6c757d;font-weight:600;white-space:nowrap;">Filter Tahun:</span>
                    <select id="filter-riwayat-tahun" class="iuran-filter-select" onchange="filterRiwayatTable()">
                        <option value="">Semua Tahun</option>
                        <option value="{{ $tahunBerjalan }}" selected>{{ $tahunBerjalan }}</option>
                        <option value="{{ $tahunBerjalan - 1 }}">{{ $tahunBerjalan - 1 }}</option>
                    </select>
                </div>
                <button type="button" class="btn-filter-reset" onclick="resetFilterRiwayat()" style="background:#f8f9fa;border:1px solid #ced4da;border-radius:4px;padding:6px 14px;font-size:12.5px;font-weight:600;color:#495057;cursor:pointer;">
                    Reset
                </button>`
            </div>
            <div class="iuran-besaran-tag" id="tag-riwayat-toolbar">
                <i class='bx bx-check-double'></i>
                <span id="label-total-riwayat-toolbar">Total {{ count($daftarRiwayat) }} Transaksi Lunas</span>
            </div>
        </div>

        {{-- Banner Catatan Pembayaran & Riwayat Kas (Mockup Layar 2.2.16) --}}
        <div class="riwayat-banner-header" style="display:flex;justify-content:space-between;align-items:center;padding:10px 18px;background:#f8fbf9;border-top:1px solid #d1e7dd;border-bottom:1px solid #d1e7dd;flex-wrap:wrap;gap:8px;">
            <div style="display:flex;align-items:center;gap:8px;font-weight:700;font-size:13px;color:#0f5132;">
                <i class='bx bx-receipt' style="font-size:18px;color:#198754;"></i>
                <span id="banner-riwayat-title">Catatan Pembayaran &amp; Riwayat Kas - {{ $namaBulanBerjalan }} {{ $tahunBerjalan }}</span>
            </div>
            <div style="font-size:12.5px;color:#495057;">
                Total Terverifikasi Lunas: <strong style="color:#198754;font-weight:700;" id="val-riwayat-total-nominal">Rp {{ number_format($totalTerverifikasiLunas, 0, ',', '.') }}</strong> (<span id="val-riwayat-total-transaksi">{{ count($daftarRiwayat) }} Transaksi</span>)
            </div>
        </div>

        {{-- Table Riwayat --}}
        <div class="iuran-table-wrap">
            <table class="iuran-table" id="table-riwayat">
                <colgroup>
                    <col style="width: 45px;">
                    <col style="width: 165px;">
                    <col style="width: 230px;">
                    <col style="width: 110px;">
                    <col style="width: 145px;">
                    <col style="width: 125px;">
                    <col style="width: 105px;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">NO</th>
                        <th>NO. KK</th>
                        <th>NAMA KEPALA KELUARGA &amp; BLOK</th>
                        <th style="text-align: center;">PERIODE IURAN</th>
                        <th>TANGGAL BAYAR</th>
                        <th>NOMINAL</th>
                        <th style="text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody id="tbody-riwayat">
                    @forelse($daftarRiwayat as $rIdx => $r)
                    @php
                        $noKkR = $r->keluarga?->no_kk ?? '-';
                        $kepalaR = $r->keluarga?->kepalaKeluarga ?? $r->keluarga?->wargas?->first();
                        $namaKepalaR = $kepalaR?->nama ?? 'Kepala Keluarga';
                        $blokAlamatR = $kepalaR?->alamat ?? 'RT 04 / RW 08';
                        $singkatBulanR = $daftarBulanSingkat[$r->bulan] ?? $r->bulan;
                        $lengkapBulanR = $daftarBulanLengkap[$r->bulan] ?? $r->bulan;
                        $periodeTeksR = "{$singkatBulanR} {$r->tahun}";
                        $tglBayarDateOnly = $r->tanggal_bayar ? \Carbon\Carbon::parse($r->tanggal_bayar)->isoFormat('D MMM Y') : '-';
                        $tglBayarTimeOnly = $r->tanggal_bayar ? \Carbon\Carbon::parse($r->tanggal_bayar)->isoFormat('HH:mm') . ' WIB' : '';
                        $tglBayarFullStr = $r->tanggal_bayar ? \Carbon\Carbon::parse($r->tanggal_bayar)->isoFormat('D MMM Y • HH:mm') . ' WIB' : '-';
                        $nominalFmtR = 'Rp ' . number_format($r->nominal, 0, ',', '.');
                    @endphp
                    <tr class="row-riwayat-item"
                        data-nokk="{{ $noKkR }}"
                        data-nama="{{ strtolower($namaKepalaR) }}"
                        data-blok="{{ strtolower($blokAlamatR) }}"
                        data-periode="{{ strtolower($periodeTeksR) }}"
                        data-bulan="{{ strtolower($lengkapBulanR) }}"
                        data-bulan-angka="{{ $r->bulan }}"
                        data-tahun="{{ $r->tahun }}"
                        data-nominal="{{ (float) $r->nominal }}"
                        data-tgl="{{ strtolower($tglBayarFullStr) }}">
                        <td style="text-align: center; color: #495057; font-weight: 500;">{{ $rIdx + 1 }}</td>
                        <td>
                            <div class="no-kk-text">{{ $noKkR }}</div>
                        </td>
                        <td>
                            <div class="nama-warga-text">{{ $namaKepalaR }}</div>
                            <div class="blok-sub-text">
                                <i class='bx bx-home'></i> {{ $blokAlamatR }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-periode-pill">{{ $periodeTeksR }}</span>
                        </td>
                        <td>
                            <div style="font-size: 12.5px; color: #212529; font-weight: 500;">{{ $tglBayarDateOnly }}</div>
                            @if($tglBayarTimeOnly)
                                <div style="font-size: 11px; color: #6c757d; margin-top: 1px;">{{ $tglBayarTimeOnly }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #198754; font-size: 13px;">{{ $nominalFmtR }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status-lunas"><i class='bx bx-check'></i> Lunas</span>
                        </td>
                    </tr>
                    @empty
                    @endforelse

                    {{-- Baris Empty State jika filter tidak menemukan data --}}
                    <tr id="empty-row-riwayat" style="display: none;">
                        <td colspan="7" style="text-align:center;padding:36px 20px;color:#6c757d;font-size:13px;">
                            <i class='bx bx-receipt' style="font-size:36px;display:block;margin-bottom:10px;color:#adb5bd;"></i>
                            <div id="empty-riwayat-text">Belum ada riwayat transaksi kas iuran warga yang berstatus lunas.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination Bar --}}
        <div class="iuran-pagination-bar">
            <div id="info-riwayat-count">Menampilkan <strong>{{ count($daftarRiwayat) }}</strong> data penerimaan kas RT 04</div>
            <div class="iuran-pagination" id="pagination-riwayat"></div>
        </div>
    </div>

</div>

{{-- ========================================================
     MODAL ATUR IURAN & CATATAN BULANAN (LAYAR 2.2.17)
     ======================================================== --}}
<div class="iuran-modal-backdrop" id="modal-atur-iuran">
    <div class="iuran-modal-content">
        <form action="{{ route('admin.iuran.atur-standar') }}" method="POST">
            @csrf
            <div class="iuran-modal-header">
                <div style="display:flex;align-items:center;gap:8px;">
                    <h3 class="iuran-modal-title" style="margin:0;">
                        <i class='bx bx-cog' style="color:#007bff;"></i> Pengaturan Standar Iuran Bulanan RT
                    </h3>
                    <span style="background:#0f172a;color:#fff;font-size:11px;padding:3px 8px;border-radius:4px;font-weight:600;">Tahun Anggaran {{ $tahunBerjalan }}</span>
                </div>
                <button type="button" class="iuran-modal-close" onclick="tutupModalAturIuran()">&times;</button>
            </div>
            <div class="iuran-modal-body">
                <div style="margin-bottom: 14px;">
                    <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#495057;">Nominal Iuran Wajib per KK (Rp) *</label>
                    <div style="position:relative;display:flex;align-items:center;">
                        <span style="position:absolute;left:12px;font-size:13px;font-weight:600;color:#64748b;">Rp</span>
                        <input type="number" name="nominal" id="modal-input-tarif"
                               value="{{ (int) $nominalWajibSaatIni }}"
                               min="1000" step="500" required
                               style="width:100%;height:38px;padding:6px 12px 6px 36px;border:1px solid #ced4da;border-radius:4px;font-size:13px;box-sizing:border-box;">
                    </div>
                    <div style="font-size:11px;color:#6c757d;margin-top:4px;">Nominal standar iuran bulanan wajib untuk setiap Kepala Keluarga (KK). Perubahan berlaku mulai bulan berjalan hingga akhir tahun.</div>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#495057;">Rincian Alokasi Dana / Detail Penggunaan Iuran:</label>
                    <textarea name="catatan" id="modal-input-catatan" rows="3"
                              style="width:100%;padding:8px 12px;border:1px solid #ced4da;border-radius:4px;font-size:12.5px;box-sizing:border-box;"
                              placeholder="Contoh: Iuran operasional kebersihan sampah, keamanan pos ronda 24 jam, & kas sosial RT 04.">{{ $catatanTerkini }}</textarea>
                    <div style="font-size:11px;color:#6c757d;margin-top:4px;">Beri tahu detail iuran ini uang apa saja (contoh: kebersihan, pos ronda, kas sosial). Keterangan ini akan langsung tampil di kartu tarif portal warga sebagai transparansi alokasi dana.</div>
                </div>
            </div>
            <div class="iuran-modal-footer">
                <button type="button" onclick="tutupModalAturIuran()" style="background:#f8f9fa;border:1px solid #ced4da;padding:8px 16px;border-radius:4px;font-size:12.5px;font-weight:600;cursor:pointer;">Batal / Tutup</button>
                <button type="submit" style="background:#007bff;color:#fff;border:none;padding:8px 16px;border-radius:4px;font-size:12.5px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <i class='bx bx-save'></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL KONFIRMASI PEMBAYARAN IURAN (TANDAI LUNAS - LAYAR 2.2.18)
     ======================================================== --}}
<div class="iuran-modal-backdrop" id="modal-tandai-lunas">
    <div class="iuran-modal-content iuran-modal-content--lunas">
        <form id="form-pelunasan-iuran" method="POST" action="">
            @csrf
            <div class="iuran-modal-header iuran-modal-header--green">
                <h3 class="iuran-modal-title iuran-modal-title--white">
                    <i class='bx bx-check-circle'></i> Konfirmasi Pembayaran Iuran (Tandai Lunas)
                </h3>
                <button type="button" class="iuran-modal-close iuran-modal-close--white" onclick="tutupModalTandaiLunas()">&times;</button>
            </div>
            <div class="iuran-modal-body" style="padding: 18px 20px 20px;">
                <input type="hidden" name="nominal" id="modal-lunas-nominal-raw" value="">

                {{-- Upper Card Info Box --}}
                <div class="modal-lunas-infobox">
                    <div class="modal-lunas-row" style="margin-bottom: 10px;">
                        <div>
                            <div class="modal-lunas-nama" id="modal-lunas-nama-text">Agus Supriyanto</div>
                            <div class="modal-lunas-nokk">No KK : <span id="modal-lunas-nokk-text">3276021204850001</span></div>
                        </div>
                        <div>
                            <span class="modal-lunas-badge-belum">
                                <i class='bx bx-minus-circle'></i> Belum Lunas
                            </span>
                        </div>
                    </div>
                    <div class="modal-lunas-row" style="padding-top: 8px; border-top: 1px dashed #e9ecef;">
                        <div class="modal-lunas-periode">
                            Periode Tagihan: <strong id="modal-lunas-periode-text">Februari 2026</strong>
                        </div>
                        <div class="modal-lunas-nominal-wrap">
                            <span>Nominal Iuran:</span>
                            <span class="modal-lunas-nominal-badge" id="modal-lunas-nominal-badge">Rp 50.000</span>
                        </div>
                    </div>
                </div>

                {{-- Form Field: Tanggal Pembayaran --}}
                <div style="margin-bottom: 12px;">
                    <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#343a40;">Tanggal Pembayaran *</label>
                    <input type="date" name="tanggal_bayar" id="modal-lunas-tanggal"
                           style="width:100%;height:38px;padding:6px 12px;border:1px solid #ced4da;border-radius:4px;font-size:13px;box-sizing:border-box;"
                           value="{{ now()->format('Y-m-d') }}" required>
                </div>

                {{-- Form Field: Catatan Pembayaran --}}
                <div style="margin-bottom: 14px;">
                    <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#343a40;">Catatan / Keterangan (Opsional):</label>
                    <input type="text" name="catatan" id="modal-lunas-catatan"
                           style="width:100%;height:38px;padding:6px 12px;border:1px solid #ced4da;border-radius:4px;font-size:13px;box-sizing:border-box;"
                           placeholder="Contoh: Lunas via Bendahara RT, pembayaran tunai.">
                </div>

                {{-- Information Banner --}}
                <div class="modal-lunas-alert">
                    <i class='bx bx-info-circle'></i>
                    <div>
                        Setelah ditandai lunas, status KK ini akan otomatis berubah menjadi <strong>LUNAS</strong> dan tercatat di riwayat penerimaan kas RT 04.
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-modal-batal" onclick="tutupModalTandaiLunas()">Batal</button>
                    <button type="submit" class="btn-modal-simpan-lunas">
                        <i class='bx bx-check'></i> Konfirmasi &amp; Simpan Lunas
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tab switching antara Daftar Tunggakan dan Riwayat
    function switchIuranTab(tabName) {
        var btnTunggakan = document.getElementById('tab-btn-tunggakan');
        var btnRiwayat = document.getElementById('tab-btn-riwayat');
        var paneTunggakan = document.getElementById('pane-tab-tunggakan');
        var paneRiwayat = document.getElementById('pane-tab-riwayat');

        if (tabName === 'tunggakan') {
            btnTunggakan.classList.add('iuran-tab-btn--active');
            btnRiwayat.classList.remove('iuran-tab-btn--active');
            paneTunggakan.style.display = 'block';
            paneRiwayat.style.display = 'none';
            renderTunggakanPagination();
        } else {
            btnRiwayat.classList.add('iuran-tab-btn--active');
            btnTunggakan.classList.remove('iuran-tab-btn--active');
            paneRiwayat.style.display = 'block';
            paneTunggakan.style.display = 'none';
            renderRiwayatPagination();
        }
    }

    // ── Pagination Config & State ──
    const TUNGGAKAN_PAGE_SIZE = 10;
    let currentTunggakanPage = 1;

    const RIWAYAT_PAGE_SIZE = 10;
    let currentRiwayatPage = 1;

    // Render & Pagination Tabel Tunggakan
    function renderTunggakanPagination() {
        var searchInput = document.getElementById('search-tunggakan-input');
        var q = (searchInput ? searchInput.value : '').toLowerCase().trim();
        var filterEkoSelect = document.getElementById('filter-tunggakan-ekonomi');
        var filterEko = (filterEkoSelect ? filterEkoSelect.value : '').toLowerCase().trim();
        var filterPeriodeSelect = document.getElementById('filter-tunggakan-periode');
        var filterPeriode = (filterPeriodeSelect ? filterPeriodeSelect.value : '');
        var bulanBerjalanNow = {{ (int) $bulanBerjalan }};
        var allRows = Array.from(document.querySelectorAll('#tbody-tunggakan tr.row-tunggakan-item'));

        var matchedRows = allRows.filter(function(row) {
            var nokk = (row.dataset.nokk || '').toLowerCase();
            var nama = (row.dataset.nama || '').toLowerCase();
            var blok = (row.dataset.blok || '').toLowerCase();
            var eko = (row.dataset.ekonomi || '').toLowerCase();
            var bulan = parseInt(row.dataset.bulan, 10);

            var matchSearch = !q || nokk.includes(q) || nama.includes(q) || blok.includes(q);
            var matchEko = !filterEko || eko.includes(filterEko);

            var matchPeriode = true;
            if (filterPeriode === 'bulan_ini') {
                matchPeriode = (bulan === bulanBerjalanNow);
            } else if (filterPeriode === 'tunggakan_lampau') {
                matchPeriode = (bulan < bulanBerjalanNow);
            } else if (filterPeriode === 'bulan_mendatang') {
                matchPeriode = (bulan > bulanBerjalanNow);
            }

            return matchSearch && matchEko && matchPeriode;
        });

        var totalItems = matchedRows.length;
        var totalPages = Math.max(1, Math.ceil(totalItems / TUNGGAKAN_PAGE_SIZE));

        if (currentTunggakanPage > totalPages) currentTunggakanPage = totalPages;
        if (currentTunggakanPage < 1) currentTunggakanPage = 1;

        allRows.forEach(function(row) { row.style.display = 'none'; });

        var startIndex = (currentTunggakanPage - 1) * TUNGGAKAN_PAGE_SIZE;
        var endIndex = Math.min(startIndex + TUNGGAKAN_PAGE_SIZE, totalItems);

        for (var i = startIndex; i < endIndex; i++) {
            matchedRows[i].style.display = '';
            var noCell = matchedRows[i].querySelector('td:first-child');
            if (noCell) noCell.textContent = (i + 1);
        }

        var infoEl = document.getElementById('info-tunggakan-count');
        var badgeTabTunggakan = document.getElementById('badge-tab-tunggakan');
        if (badgeTabTunggakan) {
            badgeTabTunggakan.textContent = totalItems + ' KK Belum Lunas';
        }
        if (infoEl) {
            if (totalItems === 0) {
                infoEl.innerHTML = 'Tidak ada data tunggakan warga yang cocok dengan filter';
            } else {
                infoEl.innerHTML = 'Menampilkan <strong>' + (startIndex + 1) + ' - ' + endIndex + '</strong> dari <strong>' + totalItems + '</strong> KK belum lunas';
            }
        }

        var container = document.getElementById('pagination-tunggakan');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1) {
            return;
        }

        // Tombol Prev
        var prevDisabled = (currentTunggakanPage <= 1);
        var btnPrev = createPageBtn('&lt;', prevDisabled, false, function() {
            if (currentTunggakanPage > 1) {
                currentTunggakanPage--;
                renderTunggakanPagination();
            }
        });
        container.appendChild(btnPrev);

        // Tombol Angka Halaman
        for (var p = 1; p <= totalPages; p++) {
            (function(pageNum) {
                var isActive = (pageNum === currentTunggakanPage);
                var btn = createPageBtn(pageNum, false, isActive, function() {
                    currentTunggakanPage = pageNum;
                    renderTunggakanPagination();
                });
                container.appendChild(btn);
            })(p);
        }

        // Tombol Next
        var nextDisabled = (currentTunggakanPage >= totalPages);
        var btnNext = createPageBtn('&gt;', nextDisabled, false, function() {
            if (currentTunggakanPage < totalPages) {
                currentTunggakanPage++;
                renderTunggakanPagination();
            }
        });
        container.appendChild(btnNext);
    }

    // Render & Pagination Tabel Riwayat
    function renderRiwayatPagination() {
        var searchRiwayatInput = document.getElementById('search-riwayat-input');
        var q = (searchRiwayatInput ? searchRiwayatInput.value : '').toLowerCase().trim();
        var filterBulanSelect = document.getElementById('filter-riwayat-bulan');
        var filterBulan = (filterBulanSelect ? filterBulanSelect.value : '').trim();
        var filterTahunSelect = document.getElementById('filter-riwayat-tahun');
        var filterTahun = (filterTahunSelect ? filterTahunSelect.value : '').trim();
        var allRows = Array.from(document.querySelectorAll('#tbody-riwayat tr.row-riwayat-item'));

        var matchedRows = allRows.filter(function(row) {
            var nokk = (row.dataset.nokk || '').toLowerCase();
            var nama = (row.dataset.nama || '').toLowerCase();
            var blok = (row.dataset.blok || '').toLowerCase();
            var periode = (row.dataset.periode || '').toLowerCase();
            var bulan = (row.dataset.bulan || '').toLowerCase();
            var bulanAngka = (row.dataset.bulanAngka || '').trim();
            var tahun = (row.dataset.tahun || '').trim();
            var tgl = (row.dataset.tgl || '').toLowerCase();

            var matchSearch = !q || nokk.includes(q) || nama.includes(q) || blok.includes(q) || periode.includes(q);

            var matchBulan = true;
            if (filterBulan) {
                var rowBulanAngka = parseInt(bulanAngka, 10);
                var filterBulanAngka = parseInt(filterBulan, 10);
                if (!isNaN(filterBulanAngka) && !isNaN(rowBulanAngka)) {
                    matchBulan = (rowBulanAngka === filterBulanAngka);
                } else {
                    var bulanAbbr = filterBulan.substring(0, 3).toLowerCase();
                    matchBulan = bulan.includes(filterBulan.toLowerCase()) || periode.includes(bulanAbbr);
                }
            }

            var matchTahun = true;
            if (filterTahun) {
                matchTahun = (tahun === filterTahun) || periode.includes(filterTahun) || tgl.includes(filterTahun);
            }

            return matchSearch && matchBulan && matchTahun;
        });

        // Hitung total nominal penerimaan kas untuk filter aktif
        var totalNominalMatched = matchedRows.reduce(function(acc, row) {
            var nom = parseFloat(row.dataset.nominal || '0');
            return acc + (isNaN(nom) ? 0 : nom);
        }, 0);

        var totalItems = matchedRows.length;
        var totalPages = Math.max(1, Math.ceil(totalItems / RIWAYAT_PAGE_SIZE));

        if (currentRiwayatPage > totalPages) currentRiwayatPage = totalPages;
        if (currentRiwayatPage < 1) currentRiwayatPage = 1;

        allRows.forEach(function(row) { row.style.display = 'none'; });

        var startIndex = (currentRiwayatPage - 1) * RIWAYAT_PAGE_SIZE;
        var endIndex = Math.min(startIndex + RIWAYAT_PAGE_SIZE, totalItems);

        for (var i = startIndex; i < endIndex; i++) {
            matchedRows[i].style.display = '';
            var noCell = matchedRows[i].querySelector('td:first-child');
            if (noCell) noCell.textContent = (i + 1);
        }

        // Tampilkan / sembunyikan baris empty state
        var emptyRow = document.getElementById('empty-row-riwayat');
        if (emptyRow) {
            emptyRow.style.display = (totalItems === 0) ? '' : 'none';
            var emptyTextEl = document.getElementById('empty-riwayat-text');
            if (emptyTextEl && totalItems === 0) {
                var selectedBulanText = (filterBulanSelect && filterBulanSelect.selectedIndex > 0)
                    ? filterBulanSelect.options[filterBulanSelect.selectedIndex].text
                    : '';
                var selectedTahunText = filterTahun || '';
                var periodeLabel = (selectedBulanText ? selectedBulanText + ' ' : '') + selectedTahunText;
                if (periodeLabel.trim()) {
                    emptyTextEl.innerHTML = 'Belum ada riwayat transaksi penerimaan kas lunas pada periode <strong>' + periodeLabel.trim() + '</strong>.<br><span style="font-size:12px;color:#868e96;margin-top:6px;display:inline-block;">Pilih bulan sebelumnya (misal September atau Agustus) pada filter di atas untuk melihat riwayat kas bulan lalu.</span>';
                } else {
                    emptyTextEl.innerHTML = 'Belum ada riwayat transaksi kas iuran warga yang cocok dengan filter pencarian.';
                }
            }
        }

        // Update banner judul & total nominal (Mockup Layar 2.2.16)
        var bannerTitle = document.getElementById('banner-riwayat-title');
        if (bannerTitle) {
            var selectedBulanText = (filterBulanSelect && filterBulanSelect.selectedIndex > 0)
                ? filterBulanSelect.options[filterBulanSelect.selectedIndex].text
                : 'Semua Bulan';
            var selectedTahunText = filterTahun ? filterTahun : 'Semua Tahun';
            bannerTitle.textContent = 'Catatan Pembayaran & Riwayat Kas - ' + selectedBulanText + ' ' + selectedTahunText;
        }

        var valNominalEl = document.getElementById('val-riwayat-total-nominal');
        if (valNominalEl) {
            valNominalEl.textContent = 'Rp ' + totalNominalMatched.toLocaleString('id-ID');
        }

        var valTransaksiEl = document.getElementById('val-riwayat-total-transaksi');
        if (valTransaksiEl) {
            valTransaksiEl.textContent = totalItems + ' Transaksi';
        }

        var infoEl = document.getElementById('info-riwayat-count');
        if (infoEl) {
            if (totalItems === 0) {
                infoEl.innerHTML = 'Tidak ada data transaksi lunas yang cocok dengan filter';
            } else {
                infoEl.innerHTML = 'Menampilkan <strong>' + (startIndex + 1) + ' - ' + endIndex + '</strong> dari <strong>' + totalItems + '</strong> data penerimaan kas RT 04';
            }
        }

        var labelToolbar = document.getElementById('label-total-riwayat-toolbar');
        if (labelToolbar) {
            labelToolbar.textContent = totalItems + ' Transaksi Lunas';
        }

        var container = document.getElementById('pagination-riwayat');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1) {
            return;
        }

        // Tombol Prev
        var prevDisabled = (currentRiwayatPage <= 1);
        var btnPrev = createPageBtn('&lt;', prevDisabled, false, function() {
            if (currentRiwayatPage > 1) {
                currentRiwayatPage--;
                renderRiwayatPagination();
            }
        });
        container.appendChild(btnPrev);

        // Tombol Angka Halaman
        for (var p = 1; p <= totalPages; p++) {
            (function(pageNum) {
                var isActive = (pageNum === currentRiwayatPage);
                var btn = createPageBtn(pageNum, false, isActive, function() {
                    currentRiwayatPage = pageNum;
                    renderRiwayatPagination();
                });
                container.appendChild(btn);
            })(p);
        }

        // Tombol Next
        var nextDisabled = (currentRiwayatPage >= totalPages);
        var btnNext = createPageBtn('&gt;', nextDisabled, false, function() {
            if (currentRiwayatPage < totalPages) {
                currentRiwayatPage++;
                renderRiwayatPagination();
            }
        });
        container.appendChild(btnNext);
    }

    // Helper untuk membuat button pagination yang seragam
    function createPageBtn(label, isDisabled, isActive, onClickHandler) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'iuran-page-link' + (isActive ? ' active' : '') + (isDisabled ? ' disabled' : '');
        btn.innerHTML = label;
        btn.disabled = !!isDisabled;
        if (!isDisabled && !isActive) {
            btn.onclick = onClickHandler;
        }
        return btn;
    }

    // Filter event handlers
    function filterTunggakanTable() {
        currentTunggakanPage = 1;
        renderTunggakanPagination();
    }

    function filterRiwayatTable() {
        currentRiwayatPage = 1;
        renderRiwayatPagination();
    }

    function resetFilterRiwayat() {
        var inputSearch = document.getElementById('search-riwayat-input');
        var selectBulan = document.getElementById('filter-riwayat-bulan');
        var selectTahun = document.getElementById('filter-riwayat-tahun');
        if (inputSearch) inputSearch.value = '';
        if (selectBulan) selectBulan.value = '{{ $bulanBerjalan }}';
        if (selectTahun) selectTahun.value = '{{ $tahunBerjalan }}';
        filterRiwayatTable();
    }

    // Modal Tandai Lunas
    function bukaModalTandaiLunas(id, nama, noKk, periode, nominalFmt, blok, rawNominal) {
        var form = document.getElementById('form-pelunasan-iuran');
        if (form) {
            form.action = "{{ url('admin/iuran/pelunasan') }}/" + id;
        }

        document.getElementById('modal-lunas-nama-text').textContent = nama;
        document.getElementById('modal-lunas-nokk-text').textContent = noKk;
        document.getElementById('modal-lunas-periode-text').textContent = periode;
        document.getElementById('modal-lunas-nominal-badge').textContent = nominalFmt;
        document.getElementById('modal-lunas-nominal-raw').value = rawNominal || '';

        // Default tanggal hari ini
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('modal-lunas-tanggal').value = today;

        document.getElementById('modal-tandai-lunas').style.display = 'flex';
    }

    function tutupModalTandaiLunas() {
        document.getElementById('modal-tandai-lunas').style.display = 'none';
    }

    // Modal Atur Iuran
    function bukaModalAturIuran() {
        document.getElementById('modal-atur-iuran').style.display = 'flex';
    }

    function tutupModalAturIuran() {
        document.getElementById('modal-atur-iuran').style.display = 'none';
    }

    // Close modal ketika klik di area luar backdrop
    document.addEventListener('click', function(e) {
        var modalLunas = document.getElementById('modal-tandai-lunas');
        var modalAtur = document.getElementById('modal-atur-iuran');
        if (e.target === modalLunas) tutupModalTandaiLunas();
        if (e.target === modalAtur) tutupModalAturIuran();
    });

    // Keyboard ESC listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalTandaiLunas();
            tutupModalAturIuran();
        }
    });

    // Inisialisasi awal saat load
    document.addEventListener('DOMContentLoaded', function() {
        renderTunggakanPagination();
        renderRiwayatPagination();
    });
</script>
@endpush
