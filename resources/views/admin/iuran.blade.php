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
    use App\Data\DummyData;
    $listTunggakan = DummyData::listTunggakanIuranAdmin();
    $listRiwayat = DummyData::listRiwayatIuranAdmin();
@endphp

{{-- ========================================================
     HEADER HALAMAN
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <nav style="font-size:12px;color:#6c757d;margin-bottom:4px;display:flex;align-items:center;gap:4px;" id="header-breadcrumb">
            WargaKu / Portal RT 04 / Keuangan &amp; Kas / <span style="font-weight: 600; color: #212529;">Kelola &amp; Rekap Iuran</span>
        </nav>
        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;">
            <h1 class="admin-page-header__title" id="page-title" style="margin:0;">Kelola &amp; Rekap Iuran Warga</h1>
            <span class="badge-periode-aktif" id="header-periode-badge">Periode Aktif: Februari 2026</span>
        </div>
        <p class="admin-page-header__sub" id="page-subtitle" style="margin-top:4px;">Monitoring penerimaan kas lingkungan, verifikasi pembayaran rutin bulanan, dan status tunggakan RT 04 / RW 08 Sukamaju.</p>
    </div>
    <div>
        <button type="button" class="btn-atur-iuran" onclick="bukaModalAturIuran()">
            <i class='bx bx-cog'></i> Atur Iuran &amp; Catatan Bulanan
        </button>
    </div>
</div>

{{-- ========================================================
     TOP STAT CARDS: 3 ADMINLTE SMALL BOXES
     ======================================================== --}}
<div class="iuran-stats-grid">
    <div class="iuran-stat-box iuran-stat-box--green" style="cursor: pointer;" onclick="switchIuranTab('riwayat')" title="Lihat riwayat penerimaan kas">
        <div class="iuran-stat-box__val">Rp 3.850.000</div>
        <div class="iuran-stat-box__label">Total Penerimaan Kas</div>
        <i class='bx bx-wallet iuran-stat-box__icon'></i>
    </div>
    <div class="iuran-stat-box iuran-stat-box--blue" style="cursor: pointer;" onclick="switchIuranTab('riwayat')" title="Lihat KK telah lunas">
        <div class="iuran-stat-box__val">77 KK</div>
        <div class="iuran-stat-box__label">KK Telah Lunas</div>
        <i class='bx bx-group iuran-stat-box__icon'></i>
    </div>
    <div class="iuran-stat-box iuran-stat-box--red" style="cursor: pointer;" onclick="switchIuranTab('tunggakan')" title="Lihat KK belum membayar">
        <div class="iuran-stat-box__val">11 KK</div>
        <div class="iuran-stat-box__label">KK Belum Membayar</div>
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
                Daftar Tunggakan KK
                <span class="badge-tab-red">11 KK Belum Lunas</span>
            </button>
            <button type="button" class="iuran-tab-btn" id="tab-btn-riwayat" onclick="switchIuranTab('riwayat')">
                Riwayat Penerimaan Kas
                <span class="badge-tab-gray">77 Transaksi</span>
            </button>
        </div>
        <div class="iuran-tabs-right">
            <span>Besaran Iuran Wajib:</span>
            <span class="badge-besaran-iuran" id="label-besaran-wajib">Rp 50.000 / KK</span>
        </div>
    </div>

    {{-- ═══ TAB 1: DAFTAR TUNGGAKAN KK ═══ --}}
    <div id="pane-tab-tunggakan">
        {{-- Toolbar Search & Info --}}
        <div class="iuran-toolbar-row">
            <div class="iuran-search-wrap">
                <i class='bx bx-search iuran-search-icon'></i>
                <input type="text" id="search-tunggakan-input" class="iuran-search-input"
                       placeholder="Cari Nama Kepala Keluarga, Blok, atau No. KK..."
                       onkeyup="filterTunggakanTable()">
            </div>
            <div class="iuran-besaran-tag" id="tag-besaran-toolbar">
                <i class='bx bx-money'></i>
                <span>Besaran Iuran: <strong id="val-besaran-toolbar">Rp 50.000 / KK</strong></span>
            </div>
        </div>

        {{-- Table Tunggakan --}}
        <div class="iuran-table-wrap">
            <table class="iuran-table" id="table-tunggakan">
                <colgroup>
                    <col style="width: 4%;">
                    <col style="width: 17%;">
                    <col style="width: 22%;">
                    <col style="width: 12%;">
                    <col style="width: 14%;">
                    <col style="width: 14%;">
                    <col style="width: 17%;">
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
                    @foreach($listTunggakan as $idx => $t)
                    <tr class="row-tunggakan-item"
                        data-nokk="{{ $t['no_kk'] }}"
                        data-nama="{{ strtolower($t['nama_kepala']) }}"
                        data-blok="{{ strtolower($t['blok']) }}">
                        <td style="text-align: center; color: #495057; font-weight: 500;">{{ $idx + 1 }}</td>
                        <td>
                            <div class="no-kk-text">{{ $t['no_kk'] }}</div>
                            <div class="sub-ekonomi-text">{{ $t['status_ekonomi'] }}</div>
                        </td>
                        <td>
                            <div class="nama-warga-text">{{ $t['nama_kepala'] }}</div>
                            <div class="blok-sub-text">
                                <i class='bx bx-home'></i> {{ $t['blok'] }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-periode-pill">{{ $t['periode'] }}</span>
                        </td>
                        <td>
                            <div class="nominal-text">{{ $t['nominal'] }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status-belum">
                                <i class='bx bx-x-circle'></i> Belum Lunas
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn-tandai-lunas"
                                    onclick="tandaiLunasKK({{ $t['id'] }}, '{{ addslashes($t['nama_kepala']) }}', '{{ $t['nominal'] }}')"
                                    title="Catat dan verifikasi pembayaran lunas">
                                <i class='bx bx-check-circle'></i> Tandai Lunas
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Bar --}}
        <div class="iuran-pagination-bar">
            <div id="info-tunggakan-count">Menampilkan <strong>1 - 7</strong> dari <strong>11</strong> data KK Belum Lunas RT 04</div>
            <div class="iuran-pagination" id="pagination-tunggakan"></div>
        </div>
    </div>

    {{-- ═══ TAB 2: RIWAYAT PENERIMAAN KAS ═══ --}}
    <div id="pane-tab-riwayat" style="display: none;">
        {{-- Toolbar Search --}}
        <div class="iuran-toolbar-row">
            <div class="iuran-search-wrap">
                <i class='bx bx-search iuran-search-icon'></i>
                <input type="text" id="search-riwayat-input" class="iuran-search-input"
                       placeholder="Cari transaksi lunas, nama warga, atau nomor KK..."
                       onkeyup="filterRiwayatTable()">
            </div>
            <div class="iuran-besaran-tag">
                <i class='bx bx-check-double'></i>
                <span>Total 77 KK Telah Lunas Terverifikasi</span>
            </div>
        </div>

        {{-- Table Riwayat --}}
        <div class="iuran-table-wrap">
            <table class="iuran-table" id="table-riwayat">
                <colgroup>
                    <col style="width: 4%;">
                    <col style="width: 17%;">
                    <col style="width: 22%;">
                    <col style="width: 20%;">
                    <col style="width: 14%;">
                    <col style="width: 11%;">
                    <col style="width: 12%;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">NO</th>
                        <th>NOMOR KK</th>
                        <th>NAMA KEPALA KELUARGA &amp; BLOK</th>
                        <th>PERIODE &amp; TGL BAYAR</th>
                        <th>METODE</th>
                        <th>NOMINAL</th>
                        <th style="text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody id="tbody-riwayat">
                    @foreach($listRiwayat as $rIdx => $r)
                    <tr class="row-riwayat-item"
                        data-nokk="{{ $r['no_kk'] }}"
                        data-nama="{{ strtolower($r['nama_kepala']) }}"
                        data-blok="{{ strtolower($r['blok']) }}">
                        <td style="text-align: center; color: #495057; font-weight: 500;">{{ $rIdx + 1 }}</td>
                        <td>
                            <div class="no-kk-text">{{ $r['no_kk'] }}</div>
                        </td>
                        <td>
                            <div class="nama-warga-text">{{ $r['nama_kepala'] }}</div>
                            <div class="blok-sub-text">
                                <i class='bx bx-home'></i> {{ $r['blok'] }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #212529;">Periode: {{ $r['periode'] }}</div>
                            <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">{{ $r['tanggal_bayar'] }}</div>
                        </td>
                        <td>
                            <span class="badge-periode-pill">{{ $r['metode'] }}</span>
                        </td>
                        <td>
                            <div class="nominal-text">{{ $r['nominal'] }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status-lunas"><i class='bx bx-check'></i> Lunas</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Bar --}}
        <div class="iuran-pagination-bar">
            <div id="info-riwayat-count">Menampilkan <strong>1 - 4</strong> dari <strong>8</strong> data transaksi lunas</div>
            <div class="iuran-pagination" id="pagination-riwayat"></div>
        </div>
    </div>

</div>

{{-- ========================================================
     MODAL ATUR IURAN & CATATAN BULANAN
     ======================================================== --}}
<div class="iuran-modal-backdrop" id="modal-atur-iuran">
    <div class="iuran-modal-content">
        <div class="iuran-modal-header">
            <h3 class="iuran-modal-title">
                <i class='bx bx-cog' style="color:#007bff;"></i> Atur Iuran &amp; Catatan Bulanan
            </h3>
            <button type="button" class="iuran-modal-close" onclick="tutupModalAturIuran()">&times;</button>
        </div>
        <div class="iuran-modal-body">
            <div style="margin-bottom: 14px;">
                <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#495057;">Besaran Tarif Iuran Wajib per KK:</label>
                <input type="text" id="modal-input-tarif" value="Rp 50.000" style="width:100%;height:38px;padding:6px 12px;border:1px solid #ced4da;border-radius:4px;font-size:13px;box-sizing:border-box;">
            </div>
            <div style="margin-bottom: 14px;">
                <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#495057;">Periode Bulan Aktif:</label>
                <select id="modal-select-periode" style="width:100%;height:38px;padding:6px 12px;border:1px solid #ced4da;border-radius:4px;font-size:13px;box-sizing:border-box;">
                    <option value="Februari 2026" selected>Februari 2026</option>
                    <option value="Maret 2026">Maret 2026</option>
                    <option value="April 2026">April 2026</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:#495057;">Catatan Pengumuman Kas RT:</label>
                <textarea id="modal-input-catatan" rows="3" style="width:100%;padding:8px 12px;border:1px solid #ced4da;border-radius:4px;font-size:12.5px;box-sizing:border-box;" placeholder="Contoh: Batas pelunasan iuran kebersihan & keamanan tanggal 15 setiap bulan.">Batas pelunasan iuran kebersihan &amp; keamanan RT 04 adalah tanggal 15 setiap bulan.</textarea>
            </div>
        </div>
        <div class="iuran-modal-footer">
            <button type="button" onclick="tutupModalAturIuran()" style="background:#f8f9fa;border:1px solid #ced4da;padding:8px 16px;border-radius:4px;font-size:12.5px;font-weight:600;cursor:pointer;">Batal</button>
            <button type="button" onclick="simpanPengaturanIuran()" style="background:#007bff;color:#fff;border:none;padding:8px 16px;border-radius:4px;font-size:12.5px;font-weight:600;cursor:pointer;">Simpan Pengaturan</button>
        </div>
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
    const TUNGGAKAN_PAGE_SIZE = 7;
    let currentTunggakanPage = 1;

    const RIWAYAT_PAGE_SIZE = 4;
    let currentRiwayatPage = 1;

    // Render & Pagination Tabel Tunggakan
    function renderTunggakanPagination() {
        var q = (document.getElementById('search-tunggakan-input').value || '').toLowerCase().trim();
        var allRows = Array.from(document.querySelectorAll('#tbody-tunggakan tr.row-tunggakan-item'));

        var matchedRows = allRows.filter(function(row) {
            var nokk = (row.dataset.nokk || '').toLowerCase();
            var nama = (row.dataset.nama || '').toLowerCase();
            var blok = (row.dataset.blok || '').toLowerCase();
            return !q || nokk.includes(q) || nama.includes(q) || blok.includes(q);
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
        if (totalItems === 0) {
            infoEl.innerHTML = 'Tidak ada data tunggakan warga yang cocok dengan pencarian';
        } else {
            infoEl.innerHTML = 'Menampilkan <strong>' + (startIndex + 1) + ' - ' + endIndex + '</strong> dari <strong>' + totalItems + '</strong> data KK Belum Lunas RT 04';
        }

        var container = document.getElementById('pagination-tunggakan');
        container.innerHTML = '';

        if (totalPages <= 1 && totalItems <= TUNGGAKAN_PAGE_SIZE) {
            // Tetap render tombol disabled 1
            var bPrev = createPageBtn('&lt;', true, false, function(){});
            var b1 = createPageBtn('1', false, true, function(){});
            var bNext = createPageBtn('&gt;', true, false, function(){});
            container.appendChild(bPrev);
            container.appendChild(b1);
            container.appendChild(bNext);
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
        var q = (document.getElementById('search-riwayat-input').value || '').toLowerCase().trim();
        var allRows = Array.from(document.querySelectorAll('#tbody-riwayat tr.row-riwayat-item'));

        var matchedRows = allRows.filter(function(row) {
            var nokk = (row.dataset.nokk || '').toLowerCase();
            var nama = (row.dataset.nama || '').toLowerCase();
            var blok = (row.dataset.blok || '').toLowerCase();
            return !q || nokk.includes(q) || nama.includes(q) || blok.includes(q);
        });

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

        var infoEl = document.getElementById('info-riwayat-count');
        if (totalItems === 0) {
            infoEl.innerHTML = 'Tidak ada data transaksi lunas yang cocok dengan pencarian';
        } else {
            infoEl.innerHTML = 'Menampilkan <strong>' + (startIndex + 1) + ' - ' + endIndex + '</strong> dari <strong>' + totalItems + '</strong> data transaksi lunas';
        }

        var container = document.getElementById('pagination-riwayat');
        container.innerHTML = '';

        if (totalPages <= 1 && totalItems <= RIWAYAT_PAGE_SIZE) {
            var bPrev = createPageBtn('&lt;', true, false, function(){});
            var b1 = createPageBtn('1', false, true, function(){});
            var bNext = createPageBtn('&gt;', true, false, function(){});
            container.appendChild(bPrev);
            container.appendChild(b1);
            container.appendChild(bNext);
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

    // Tandai Lunas KK
    function tandaiLunasKK(id, nama, nominal) {
        if (confirm('Konfirmasi pembayaran iuran ' + nominal + ' untuk ' + nama + '?\nStatus akan otomatis diperbarui menjadi LUNAS.')) {
            alert('Pembayaran iuran dari ' + nama + ' sebesar ' + nominal + ' berhasil diverifikasi Lunas.');
        }
    }

    // Cetak Kwitansi
    function cetakKwitansi(noKk, nama, nominal) {
        alert('Mencetak kwitansi resmi pembayaran iuran warga:\n\nNama: ' + nama + '\nNo. KK: ' + noKk + '\nNominal: ' + nominal + '\nStatus: Lunas (Terverifikasi)');
        window.print();
    }

    // Modal Atur Iuran
    function bukaModalAturIuran() {
        document.getElementById('modal-atur-iuran').style.display = 'flex';
    }

    function tutupModalAturIuran() {
        document.getElementById('modal-atur-iuran').style.display = 'none';
    }

    function simpanPengaturanIuran() {
        var tarif = document.getElementById('modal-input-tarif').value;
        var periode = document.getElementById('modal-select-periode').value;

        document.getElementById('header-periode-badge').textContent = 'Periode Aktif: ' + periode;
        document.getElementById('label-besaran-wajib').textContent = tarif + ' / KK';
        document.getElementById('val-besaran-toolbar').textContent = tarif + ' / KK';

        tutupModalAturIuran();
        alert('Pengaturan tarif ' + tarif + ' untuk ' + periode + ' berhasil disimpan.');
    }

    // Inisialisasi awal saat load
    document.addEventListener('DOMContentLoaded', function() {
        renderTunggakanPagination();
        renderRiwayatPagination();
    });
    renderTunggakanPagination();
    renderRiwayatPagination();
</script>
@endpush
