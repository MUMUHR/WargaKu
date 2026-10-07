@extends('layouts.admin')

@section('title', 'Proses Pengajuan Surat Pengantar Warga')
@section('meta_description', 'Kelola, validasi permohonan surat pengantar lingkungan RT, penerbitan nomor surat resmi, dan cetak dokumen.')

@section('breadcrumb')
    <span class="admin-topbar__breadcrumb-current">Proses Pengajuan Surat</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-surat.css') }}">
@endpush

@section('content')

@php
    use App\Data\DummyData;
    $listSurat = DummyData::listPengajuanSuratAdmin();
    // Prioritaskan status Menunggu di paling depan, yang sudah disetujui / ditolak di belakang
    usort($listSurat, function ($a, $b) {
        $prio = [
            'menunggu' => 1,
            'disetujui' => 2,
            'ditolak' => 3,
        ];
        $prioA = $prio[strtolower($a['status'] ?? '')] ?? 99;
        $prioB = $prio[strtolower($b['status'] ?? '')] ?? 99;
        if ($prioA !== $prioB) {
            return $prioA <=> $prioB;
        }
        return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
    });
@endphp

{{-- ========================================================
     HEADER HALAMAN
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <nav style="font-size:12px;color:#6c757d;margin-bottom:4px;display:flex;align-items:center;gap:4px;" id="header-breadcrumb">
            Beranda / Portal RT 04 / Administrasi &amp; Layanan / <span style="font-weight: 600; color: #212529;">Proses Pengajuan Surat</span>
        </nav>
        <h1 class="admin-page-header__title" id="page-title">Proses Pengajuan Surat Pengantar Warga</h1>
        <p class="admin-page-header__sub" id="page-subtitle">Kelola, validasi permohonan surat pengantar lingkungan RT, penerbitan nomor surat resmi, dan cetak dokumen.</p>
    </div>
</div>

{{-- ========================================================
     TOP STAT CARDS: 3 ADMINLTE SMALL BOXES
     ======================================================== --}}
<div class="surat-stats-grid">
    <div class="surat-stat-box surat-stat-box--amber" style="cursor: pointer;" onclick="filterByBox('Menunggu')" title="Klik untuk filter status Menunggu">
        <div class="surat-stat-box__val" id="stat-count-menunggu">3</div>
        <div class="surat-stat-box__label">MENUNGGU VERIFIKASI</div>
        <div class="surat-stat-box__sub">Memerlukan tindakan persetujuan RT</div>
        <i class='bx bx-time-five surat-stat-box__icon'></i>
    </div>
    <div class="surat-stat-box surat-stat-box--green" style="cursor: pointer;" onclick="filterByBox('Disetujui')" title="Klik untuk filter status Disetujui">
        <div class="surat-stat-box__val" id="stat-count-disetujui">28</div>
        <div class="surat-stat-box__label">SURAT DISETUJUI</div>
        <div class="surat-stat-box__sub">Nomor registrasi resmi terbit</div>
        <i class='bx bx-envelope-open surat-stat-box__icon'></i>
    </div>
    <div class="surat-stat-box surat-stat-box--red" style="cursor: pointer;" onclick="filterByBox('Ditolak')" title="Klik untuk filter status Ditolak">
        <div class="surat-stat-box__val" id="stat-count-ditolak">2</div>
        <div class="surat-stat-box__label">SURAT DITOLAK</div>
        <div class="surat-stat-box__sub">Disertai catatan alasan penolakan</div>
        <i class='bx bx-x-circle surat-stat-box__icon'></i>
    </div>
</div>

{{-- ========================================================
     FILTER TOOLBAR
     ======================================================== --}}
<div class="surat-filter-bar">
    <div class="surat-filter-group-status">
        <label class="surat-filter-label" for="filter-status-select">Status:</label>
        <select id="filter-status-select" class="surat-filter-select" onchange="terapkanFilterSurat()">
            <option value="">Semua Status</option>
            <option value="Menunggu">Menunggu</option>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
        </select>
    </div>

    <div class="surat-filter-search-box">
        <i class='bx bx-search surat-filter-search-icon'></i>
        <input type="text" id="search-surat-input" class="surat-filter-search-input"
               placeholder="Cari nama pemohon, NIK, keperluan surat, atau nomor surat..."
               onkeyup="terapkanFilterSurat()">
    </div>

    <button type="button" class="btn-filter-reset-surat" onclick="resetFilterSurat()" title="Reset Filter">
        <i class='bx bx-refresh'></i> Reset Filter
    </button>
</div>

{{-- ========================================================
     TABLE CARD CONTAINER
     ======================================================== --}}
<div class="surat-card-container">
    <div class="surat-card-header">
        <div class="surat-card-title-group">
            <h2 class="surat-card-title">Daftar Pengajuan Surat Pengantar RT</h2>
            <span class="badge-count-surat" id="badge-total-surat">Menampilkan 5 dari 33 permohonan</span>
        </div>
       
    </div>

    <div class="surat-table-wrap">
        <table class="surat-table" id="table-pengajuan-surat">
            <colgroup>
                <col style="width: 3.5%;">
                <col style="width: 11%;">
                <col style="width: 15%;">
                <col style="width: 12%;">
                <col style="width: 22%;">
                <col style="width: 11%;">
                <col style="width: 7.5%;">
                <col style="width: 18%;">
            </colgroup>
            <thead>
                <tr>
                    <th style="text-align: center;">NO</th>
                    <th>TANGGAL PENGAJUAN</th>
                    <th>PEMOHON</th>
                    <th>DATA WARGA TUJUAN</th>
                    <th>KEPERLUAN</th>
                    <th>NOMOR SURAT</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="tbody-surat">
                @foreach($listSurat as $idx => $s)
                <tr class="row-surat-item"
                    id="row-surat-{{ $s['id'] }}"
                    data-id="{{ $s['id'] }}"
                    data-status="{{ strtolower($s['status']) }}"
                    data-pemohon="{{ strtolower($s['pemohon_nama']) }}"
                    data-pemohon-raw="{{ $s['pemohon_nama'] }}"
                    data-nik="{{ strtolower($s['pemohon_nik']) }}"
                    data-tujuan="{{ strtolower($s['warga_tujuan']) }}"
                    data-tujuan-raw="{{ $s['warga_tujuan'] }}"
                    data-keperluan="{{ strtolower($s['keperluan']) }}"
                    data-nomor="{{ strtolower($s['nomor_surat'] ?? '') }}"
                    data-alasan="{{ $s['alasan_tolak'] ?? 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.' }}">
                    <td style="text-align: center; color: #495057; font-weight: 500;">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight: 700; color: #212529; font-size: 12.5px;">{{ $s['tanggal'] }}</div>
                        <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">{{ $s['jam'] }}</div>
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="pemohon-nama-link" onclick="lihatProfilPemohon('{{ addslashes($s['pemohon_nama']) }}', '{{ $s['pemohon_nik'] }}')">
                            {{ $s['pemohon_nama'] }}
                        </a>
                        <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">NIK: {{ $s['pemohon_nik'] }}</div>
                        <div style="font-size: 11px; color: #868e96;">{{ $s['pemohon_alamat'] }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #212529; font-size: 13px;">{{ $s['warga_tujuan'] }}</div>
                    </td>
                    <td>
                        <div style="color: #495057; font-size: 12px; line-height: 1.4;">{{ $s['keperluan'] }}</div>
                    </td>
                    <td>
                        @if($s['status'] === 'Disetujui' && $s['nomor_surat'])
                            <div class="nomor-surat-copy" onclick="copyNomorSurat('{{ $s['nomor_surat'] }}')" title="Klik untuk menyalin nomor surat">
                                <span>{{ $s['nomor_surat'] }}</span>
                                <i class='bx bx-copy'></i>
                            </div>
                        @else
                            <span style="color: #adb5bd; font-size: 13px;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($s['status'] === 'Menunggu')
                            <span class="badge-status-surat badge-status-surat--menunggu">Menunggu</span>
                        @elseif($s['status'] === 'Disetujui')
                            <span class="badge-status-surat badge-status-surat--disetujui">Disetujui</span>
                        @else
                            <span class="badge-status-surat badge-status-surat--ditolak">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        @if($s['status'] === 'Menunggu')
                            <div class="surat-row-actions">
                                <a href="{{ route('admin.surat.preview', ['id' => $s['id']]) }}" class="btn-surat-preview-draft" title="Pratinjau Draf Surat">
                                    <i class='bx bx-show'></i> Preview Draft
                                </a>
                                <button type="button" class="btn-surat-setujui" onclick="bukaModalSetujui({{ $s['id'] }}, '{{ addslashes($s['pemohon_nama']) }}', '{{ addslashes($s['warga_tujuan']) }}')" title="Setujui dan Terbitkan Nomor">
                                    <i class='bx bx-check'></i> Setujui
                                </button>
                                <button type="button" class="btn-surat-tolak" onclick="bukaModalTolak({{ $s['id'] }}, '{{ addslashes($s['pemohon_nama']) }}', '{{ addslashes($s['warga_tujuan']) }}')" title="Tolak Permohonan">
                                    <i class='bx bx-x'></i> Tolak
                                </button>
                            </div>
                        @elseif($s['status'] === 'Disetujui')
                            <div class="surat-row-actions">
                                <a href="{{ route('admin.surat.preview', ['id' => $s['id']]) }}" class="btn-surat-cetak" title="Cetak Surat Pengantar Resmi">
                                    <i class='bx bx-printer'></i> Preview &amp; Cetak
                                </a>
                            </div>
                        @else
                            <div class="surat-row-actions">
                                <button type="button" class="btn-surat-alasan" onclick="lihatAlasanTolak({{ $s['id'] }}, '{{ addslashes($s['pemohon_nama']) }}', '{{ addslashes($s['alasan_tolak'] ?? 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.') }}')" title="Lihat Catatan Alasan Penolakan">
                                    <i class='bx bx-info-circle'></i> Detail Alasan
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination Bar --}}
    <div class="surat-pagination-bar">
        <div id="info-surat-count">Showing <strong>1 to 5</strong> of <strong>8</strong> entries</div>
        <div class="surat-pagination" id="pagination-surat"></div>
    </div>
</div>

{{-- ═════════════════════════════════════════════════════════
     MODAL 1: PERSETUJUAN & PENERBITAN SURAT PENGANTAR
     ═════════════════════════════════════════════════════════ --}}
<div class="surat-modal-overlay" id="modal-persetujuan-surat" onclick="if(event.target===this) tutupModalSetujui()">
    <div class="surat-modal-dialog">
        <div class="surat-modal-header surat-modal-header--green">
            <h3 class="surat-modal-title">
                <i class='bx bx-check-shield' style="font-size: 19px;"></i>
                Persetujuan &amp; Penerbitan Surat Pengantar
            </h3>
            <button type="button" class="surat-modal-close-btn" onclick="tutupModalSetujui()">&times;</button>
        </div>
        <div class="surat-modal-body">
            <input type="hidden" id="modal-setujui-id" value="">
            
            <div class="surat-modal-info-block">
                <div class="surat-modal-info-row">
                    <span class="surat-modal-info-label">Pemohon:</span>
                    <span class="surat-modal-info-val surat-modal-info-val--blue" id="modal-setujui-pemohon">Bambang Santoso, S.T.</span>
                </div>
                <div class="surat-modal-info-row">
                    <span class="surat-modal-info-label">Warga Tujuan:</span>
                    <span class="surat-modal-info-val surat-modal-info-val--dark" id="modal-setujui-tujuan">Siti Rahmawati, S.Ak.</span>
                </div>
            </div>

            <div class="surat-modal-grid-2">
                <div class="surat-modal-field">
                    <label class="surat-modal-label" for="modal-setujui-nomor">
                        <i class='bx bx-id-card'></i> Nomor Surat Resmi RT <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-setujui-nomor" class="surat-modal-input" value="05/SP/RT04/II/2026">
                    <div class="surat-modal-subtext">Format: [No]/SP/RT04/[Bulan]/[Tahun]</div>
                </div>

                <div class="surat-modal-field">
                    <label class="surat-modal-label" for="modal-setujui-tanggal">
                        <i class='bx bx-calendar'></i> Tanggal Penerbitan <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-setujui-tanggal" class="surat-modal-input" readonly value="{{ date('m/d/Y') }}">
                    <div class="surat-modal-subtext">Tanggal tertera pada surat pengantar</div>
                </div>
            </div>
        </div>
        <div class="surat-modal-footer">
            <button type="button" class="btn-modal-batal" onclick="tutupModalSetujui()">Batal</button>
            <button type="button" class="btn-modal-simpan" onclick="simpanPersetujuanSurat()">
                <i class='bx bx-check'></i> Simpan &amp; Terbitkan Surat
            </button>
        </div>
    </div>
</div>

{{-- ═════════════════════════════════════════════════════════
     MODAL 2: PENOLAKAN SURAT PENGANTAR (FORM PENOLAKAN)
     ═════════════════════════════════════════════════════════ --}}
<div class="surat-modal-overlay" id="modal-tolak-surat" onclick="if(event.target===this) tutupModalTolak()">
    <div class="surat-modal-dialog">
        <div class="surat-modal-header surat-modal-header--red">
            <h3 class="surat-modal-title">
                <i class='bx bx-x-circle' style="font-size: 19px;"></i>
                Penolakan Permohonan Surat Pengantar
            </h3>
            <button type="button" class="surat-modal-close-btn" onclick="tutupModalTolak()">&times;</button>
        </div>
        <div class="surat-modal-body">
            <input type="hidden" id="modal-tolak-id" value="">
            
            <div class="surat-modal-info-block">
                <div class="surat-modal-info-row">
                    <span class="surat-modal-info-label">Pemohon:</span>
                    <span class="surat-modal-info-val surat-modal-info-val--blue" id="modal-tolak-pemohon">-</span>
                </div>
                <div class="surat-modal-info-row">
                    <span class="surat-modal-info-label">Warga Tujuan:</span>
                    <span class="surat-modal-info-val surat-modal-info-val--dark" id="modal-tolak-tujuan">-</span>
                </div>
            </div>

            <div class="surat-modal-grid-2">
                <div class="surat-modal-field">
                    <label class="surat-modal-label" for="modal-tolak-alasan">
                        <i class='bx bx-message-error' style="color: #dc3545;"></i> Alasan Penolakan RT <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-tolak-alasan" class="surat-modal-input" placeholder="Masukkan alasan penolakan..." value="Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.">
                    <div class="surat-modal-subtext">Catatan ini akan tersimpan pada detail alasan</div>
                </div>

                <div class="surat-modal-field">
                    <label class="surat-modal-label" for="modal-tolak-tanggal">
                        <i class='bx bx-calendar'></i> Tanggal Penolakan <span class="req">*</span>
                    </label>
                    <input type="text" id="modal-tolak-tanggal" class="surat-modal-input" readonly value="{{ date('m/d/Y') }}">
                    <div class="surat-modal-subtext">Tanggal tercatat dalam sistem administrasi</div>
                </div>
            </div>
        </div>
        <div class="surat-modal-footer">
            <button type="button" class="btn-modal-batal" onclick="tutupModalTolak()">Batal</button>
            <button type="button" class="btn-modal-tolak" onclick="simpanPenolakanSurat()">
                <i class='bx bx-x'></i> Tolak Permohonan Surat
            </button>
        </div>
    </div>
</div>

{{-- ═════════════════════════════════════════════════════════
     MODAL 3: CATATAN ALASAN PENOLAKAN
     ═════════════════════════════════════════════════════════ --}}
<div class="surat-modal-overlay" id="modal-alasan-tolak" onclick="if(event.target===this) tutupModalAlasan()">
    <div class="surat-modal-dialog" style="max-width: 520px;">
        <div class="surat-modal-header surat-modal-header--plain">
            <h3 class="surat-modal-title">
                <i class='bx bx-error-circle' style="color: #dc3545; font-size: 22px;"></i>
                Catatan Alasan Penolakan
            </h3>
            <button type="button" class="surat-modal-close-btn" onclick="tutupModalAlasan()">&times;</button>
        </div>
        <div class="surat-modal-body">
            <div style="font-size: 12px; font-weight: 600; color: #6c757d; margin-bottom: 2px;">Nama Pemohon:</div>
            <div id="modal-alasan-pemohon" style="font-size: 15px; font-weight: 700; color: #212529; margin-bottom: 12px;">Dedi Suryana</div>

            <div class="surat-alasan-box">
                <div class="surat-alasan-box__title">Keterangan Pengurus RT:</div>
                <div class="surat-alasan-box__desc" id="modal-alasan-text">
                    Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.
                </div>
            </div>
        </div>
        <div class="surat-modal-footer">
            <button type="button" class="btn-modal-tutup-alasan" onclick="tutupModalAlasan()">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Pagination Config & State ──
    const SURAT_PAGE_SIZE = 5;
    let currentSuratPage = 1;

    function renderSuratPagination() {
        var status = (document.getElementById('filter-status-select').value || '').toLowerCase().trim();
        var q = (document.getElementById('search-surat-input').value || '').toLowerCase().trim();
        var tbody = document.getElementById('tbody-surat');
        if (!tbody) return;
        var allRows = Array.from(tbody.querySelectorAll('tr.row-surat-item'));

        // Prioritaskan status 'menunggu' tampil paling pertama (di atas), status lainnya ('disetujui'/'ditolak') pindah ke belakang
        allRows.sort(function(a, b) {
            var statusOrder = { 'menunggu': 1, 'disetujui': 2, 'ditolak': 3 };
            var orderA = statusOrder[(a.dataset.status || '').toLowerCase()] || 99;
            var orderB = statusOrder[(b.dataset.status || '').toLowerCase()] || 99;
            if (orderA !== orderB) {
                return orderA - orderB;
            }
            return (parseInt(a.dataset.id, 10) || 0) - (parseInt(b.dataset.id, 10) || 0);
        });

        // Re-append ke tbody agar urutan visual DOM benar-benar terupdate
        allRows.forEach(function(row) {
            tbody.appendChild(row);
        });

        var matchedRows = allRows.filter(function(row) {
            var rowStatus = (row.dataset.status || '').toLowerCase();
            var pemohon = (row.dataset.pemohon || '').toLowerCase();
            var nik = (row.dataset.nik || '').toLowerCase();
            var tujuan = (row.dataset.tujuan || '').toLowerCase();
            var keperluan = (row.dataset.keperluan || '').toLowerCase();
            var nomor = (row.dataset.nomor || '').toLowerCase();

            var matchStatus = !status || rowStatus === status;
            var matchSearch = !q || pemohon.includes(q) || nik.includes(q) || tujuan.includes(q) || keperluan.includes(q) || nomor.includes(q);

            return matchStatus && matchSearch;
        });

        var totalItems = matchedRows.length;
        var totalPages = Math.max(1, Math.ceil(totalItems / SURAT_PAGE_SIZE));

        if (currentSuratPage > totalPages) currentSuratPage = totalPages;
        if (currentSuratPage < 1) currentSuratPage = 1;

        allRows.forEach(function(row) { row.style.display = 'none'; });

        var startIndex = (currentSuratPage - 1) * SURAT_PAGE_SIZE;
        var endIndex = Math.min(startIndex + SURAT_PAGE_SIZE, totalItems);

        for (var i = startIndex; i < endIndex; i++) {
            matchedRows[i].style.display = '';
            var noCell = matchedRows[i].querySelector('td:first-child');
            if (noCell) noCell.textContent = (i + 1);
        }

        var badgeTotal = document.getElementById('badge-total-surat');
        if (badgeTotal) {
            badgeTotal.textContent = 'Menampilkan ' + totalItems + ' dari 8 permohonan';
        }

        var infoEl = document.getElementById('info-surat-count');
        if (infoEl) {
            if (totalItems === 0) {
                infoEl.innerHTML = 'No entries match the filter criteria';
            } else {
                infoEl.innerHTML = 'Showing <strong>' + (startIndex + 1) + ' to ' + endIndex + '</strong> of <strong>' + totalItems + '</strong> entries';
            }
        }

        var container = document.getElementById('pagination-surat');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1 && totalItems <= SURAT_PAGE_SIZE) {
            var bPrev = createSuratPageBtn('Previous', true, false, function(){});
            var b1 = createSuratPageBtn('1', false, true, function(){});
            var bNext = createSuratPageBtn('Next', true, false, function(){});
            container.appendChild(bPrev);
            container.appendChild(b1);
            container.appendChild(bNext);
            return;
        }

        // Previous
        var prevDisabled = (currentSuratPage <= 1);
        var btnPrev = createSuratPageBtn('Previous', prevDisabled, false, function() {
            if (currentSuratPage > 1) {
                currentSuratPage--;
                renderSuratPagination();
            }
        });
        container.appendChild(btnPrev);

        // Page buttons
        for (var p = 1; p <= totalPages; p++) {
            (function(pageNum) {
                var isActive = (pageNum === currentSuratPage);
                var btn = createSuratPageBtn(pageNum, false, isActive, function() {
                    currentSuratPage = pageNum;
                    renderSuratPagination();
                });
                container.appendChild(btn);
            })(p);
        }

        // Next
        var nextDisabled = (currentSuratPage >= totalPages);
        var btnNext = createSuratPageBtn('Next', nextDisabled, false, function() {
            if (currentSuratPage < totalPages) {
                currentSuratPage++;
                renderSuratPagination();
            }
        });
        container.appendChild(btnNext);
    }

    function createSuratPageBtn(label, isDisabled, isActive, onClickHandler) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'surat-page-link' + (isActive ? ' active' : '') + (isDisabled ? ' disabled' : '');
        btn.innerHTML = label;
        btn.disabled = !!isDisabled;
        if (!isDisabled && !isActive) {
            btn.onclick = onClickHandler;
        }
        return btn;
    }

    // Filter status & pencarian
    function terapkanFilterSurat() {
        currentSuratPage = 1;
        renderSuratPagination();
    }

    // Filter langsung dari klik top stat card
    function filterByBox(statusVal) {
        document.getElementById('filter-status-select').value = statusVal;
        terapkanFilterSurat();
    }

    // Reset Filter
    function resetFilterSurat() {
        document.getElementById('filter-status-select').value = '';
        document.getElementById('search-surat-input').value = '';
        terapkanFilterSurat();
    }

    // Salin nomor surat ke clipboard
    function copyNomorSurat(nomor) {
        if (!nomor) return;
        navigator.clipboard.writeText(nomor).then(function() {
            alert('Nomor surat ' + nomor + ' berhasil disalin ke clipboard.');
        }).catch(function() {
            alert('Nomor surat: ' + nomor);
        });
    }

    // ── Modal Persetujuan & Penerbitan Surat ──
    function getFormattedToday() {
        var d = new Date();
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        var yyyy = d.getFullYear();
        return mm + '/' + dd + '/' + yyyy;
    }

    function bukaModalSetujui(id, pemohon, tujuan) {
        document.getElementById('modal-setujui-id').value = id;
        document.getElementById('modal-setujui-pemohon').textContent = pemohon;
        document.getElementById('modal-setujui-tujuan').textContent = tujuan || pemohon;
        
        // Tanggal penerbitan otomatis hari ini & dikunci
        document.getElementById('modal-setujui-tanggal').value = getFormattedToday();

        // Rekomendasi nomor urut surat resmi
        var noInput = document.getElementById('modal-setujui-nomor');
        if (noInput) {
            var currentTotal = parseInt((document.getElementById('stat-count-disetujui') || {}).textContent || '28', 10) + 1;
            var padNum = String(currentTotal).padStart(2, '0');
            noInput.value = padNum + '/SP/RT04/II/2026';
        }

        document.getElementById('modal-persetujuan-surat').style.display = 'flex';
    }

    function tutupModalSetujui() {
        document.getElementById('modal-persetujuan-surat').style.display = 'none';
    }

    function simpanPersetujuanSurat() {
        var id = document.getElementById('modal-setujui-id').value;
        var noSurat = (document.getElementById('modal-setujui-nomor').value || '05/SP/RT04/II/2026').trim();
        var pemohon = document.getElementById('modal-setujui-pemohon').textContent;

        var row = document.getElementById('row-surat-' + id);
        if (row) {
            row.dataset.status = 'disetujui';
            row.dataset.nomor = noSurat.toLowerCase();

            // Kolom Nomor Surat (Index 5 / nth-child(6))
            var noCell = row.querySelector('td:nth-child(6)');
            if (noCell) {
                noCell.innerHTML = `
                    <div class="nomor-surat-copy" onclick="copyNomorSurat('${noSurat}')" title="Klik untuk menyalin nomor surat">
                        <span>${noSurat}</span>
                        <i class='bx bx-copy'></i>
                    </div>
                `;
            }

            // Kolom Status (Index 6 / nth-child(7))
            var statusCell = row.querySelector('td:nth-child(7)');
            if (statusCell) {
                statusCell.innerHTML = `<span class="badge-status-surat badge-status-surat--disetujui">Disetujui</span>`;
            }

            // Kolom Aksi (Index 7 / nth-child(8))
            var aksiCell = row.querySelector('td:nth-child(8)');
            if (aksiCell) {
                aksiCell.innerHTML = `
                    <div class="surat-row-actions">
                        <a href="/admin/surat/preview/${id}" class="btn-surat-cetak" title="Cetak Surat Pengantar Resmi">
                            <i class='bx bx-printer'></i> Preview &amp; Cetak
                        </a>
                    </div>
                `;
            }
        }

        // Simpan ke localStorage agar sinkron dengan halaman preview
        localStorage.setItem('surat_status_' + id, 'disetujui');
        localStorage.setItem('surat_nomor_' + id, noSurat);

        // Update Stat Cards Counters
        var elMenunggu = document.getElementById('stat-count-menunggu');
        var elDisetujui = document.getElementById('stat-count-disetujui');
        if (elMenunggu && elDisetujui) {
            var valMenunggu = Math.max(0, parseInt(elMenunggu.textContent, 10) - 1);
            var valDisetujui = parseInt(elDisetujui.textContent, 10) + 1;
            elMenunggu.textContent = valMenunggu;
            elDisetujui.textContent = valDisetujui;
        }

        tutupModalSetujui();
        renderSuratPagination();
    }

    // ── Modal Penolakan Surat (Form Tolak) ──
    function bukaModalTolak(id, pemohon, tujuan) {
        document.getElementById('modal-tolak-id').value = id;
        document.getElementById('modal-tolak-pemohon').textContent = pemohon;
        document.getElementById('modal-tolak-tujuan').textContent = tujuan || pemohon;
        document.getElementById('modal-tolak-tanggal').value = getFormattedToday();
        document.getElementById('modal-tolak-alasan').value = 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.';
        document.getElementById('modal-tolak-surat').style.display = 'flex';
    }

    function tutupModalTolak() {
        document.getElementById('modal-tolak-surat').style.display = 'none';
    }

    function simpanPenolakanSurat() {
        var id = document.getElementById('modal-tolak-id').value;
        var alasan = (document.getElementById('modal-tolak-alasan').value || 'Lokasi permohonan belum melampirkan berkas dan persetujuan tertulis dari tetangga sekitar.').trim();
        var pemohon = document.getElementById('modal-tolak-pemohon').textContent;

        var row = document.getElementById('row-surat-' + id);
        if (row) {
            row.dataset.status = 'ditolak';
            row.dataset.alasan = alasan;

            // Kolom Nomor Surat (Index 5 / nth-child(6))
            var noCell = row.querySelector('td:nth-child(6)');
            if (noCell) {
                noCell.innerHTML = `<span style="color: #dc3545; font-size: 11.5px; font-weight: 600;">Dibatalkan</span>`;
            }

            // Kolom Status (Index 6 / nth-child(7))
            var statusCell = row.querySelector('td:nth-child(7)');
            if (statusCell) {
                statusCell.innerHTML = `<span class="badge-status-surat badge-status-surat--ditolak">Ditolak</span>`;
            }

            // Kolom Aksi (Index 7 / nth-child(8))
            var aksiCell = row.querySelector('td:nth-child(8)');
            if (aksiCell) {
                var safeAlasan = alasan.replace(/'/g, "\\'");
                var safeNama = pemohon.replace(/'/g, "\\'");
                aksiCell.innerHTML = `
                    <div class="surat-row-actions">
                        <button type="button" class="btn-surat-alasan" onclick="lihatAlasanTolak(${id}, '${safeNama}', '${safeAlasan}')" title="Lihat Catatan Alasan Penolakan">
                            <i class='bx bx-info-circle'></i> Detail Alasan
                        </button>
                    </div>
                `;
            }
        }

        // Simpan status ditolak ke localStorage
        localStorage.setItem('surat_status_' + id, 'ditolak');
        localStorage.setItem('surat_alasan_' + id, alasan);

        // Update Stat Cards Counters
        var elMenunggu = document.getElementById('stat-count-menunggu');
        var elDitolak = document.getElementById('stat-count-ditolak');
        if (elMenunggu && elDitolak) {
            var valMenunggu = Math.max(0, parseInt(elMenunggu.textContent, 10) - 1);
            var valDitolak = parseInt(elDitolak.textContent, 10) + 1;
            elMenunggu.textContent = valMenunggu;
            elDitolak.textContent = valDitolak;
        }

        tutupModalTolak();
        renderSuratPagination();
    }

    function tolakSurat(id, nama) {
        bukaModalTolak(id, nama, nama);
    }

    // ── Modal Catatan Alasan Penolakan (View Only) ──
    function lihatAlasanTolak(id, nama, alasan) {
        document.getElementById('modal-alasan-pemohon').textContent = nama;
        document.getElementById('modal-alasan-text').textContent = alasan || 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.';
        document.getElementById('modal-alasan-tolak').style.display = 'flex';
    }

    function tutupModalAlasan() {
        document.getElementById('modal-alasan-tolak').style.display = 'none';
    }

    function cetakSurat(id, noSurat) {
        window.location.href = '/admin/surat/preview/' + id;
    }

    function bukaModalSurat(aksi, id, nama) {
        window.location.href = '/admin/surat/preview/' + id;
    }

    function lihatProfilPemohon(nama, nik) {
        alert('Profil Pemohon: ' + nama + '\nNIK: ' + nik + '\nWilayah: RT 04 / RW 08');
    }

    // Sinkronisasi status dari localStorage pada halaman daftar
    function sinkronkanStatusDariStorage() {
        var rows = document.querySelectorAll('#tbody-surat tr.row-surat-item');
        var deltaMenunggu = 0;
        var deltaDisetujui = 0;
        var deltaDitolak = 0;

        rows.forEach(function(row) {
            var id = row.dataset.id;
            if (!id) return;

            var st = localStorage.getItem('surat_status_' + id);
            var prevStatus = row.dataset.status;

            if (st === 'disetujui' && prevStatus !== 'disetujui') {
                var noSurat = localStorage.getItem('surat_nomor_' + id) || '05/SP/RT04/II/2026';
                row.dataset.status = 'disetujui';
                row.dataset.nomor = noSurat.toLowerCase();

                var noCell = row.querySelector('td:nth-child(6)');
                if (noCell) {
                    noCell.innerHTML = `
                        <div class="nomor-surat-copy" onclick="copyNomorSurat('${noSurat}')" title="Klik untuk menyalin nomor surat">
                            <span>${noSurat}</span>
                            <i class='bx bx-copy'></i>
                        </div>
                    `;
                }

                var statusCell = row.querySelector('td:nth-child(7)');
                if (statusCell) {
                    statusCell.innerHTML = `<span class="badge-status-surat badge-status-surat--disetujui">Disetujui</span>`;
                }

                var aksiCell = row.querySelector('td:nth-child(8)');
                if (aksiCell) {
                    aksiCell.innerHTML = `
                        <div class="surat-row-actions">
                            <a href="/admin/surat/preview/${id}" class="btn-surat-cetak" title="Cetak Surat Pengantar Resmi">
                                <i class='bx bx-printer'></i> Preview &amp; Cetak
                            </a>
                        </div>
                    `;
                }

                if (prevStatus === 'menunggu') {
                    deltaMenunggu--;
                    deltaDisetujui++;
                }
            } else if (st === 'ditolak' && prevStatus !== 'ditolak') {
                var alasan = localStorage.getItem('surat_alasan_' + id) || 'Lokasi tenda melewati batas jalan utama dan belum melampirkan persetujuan tertulis dari tetangga kanan kiri Blok D3.';
                var nama = row.dataset.pemohonRaw || row.dataset.pemohon;
                row.dataset.status = 'ditolak';
                row.dataset.alasan = alasan;

                var noCell = row.querySelector('td:nth-child(6)');
                if (noCell) noCell.innerHTML = `<span style="color: #dc3545; font-size: 11.5px; font-weight: 600;">Dibatalkan</span>`;

                var statusCell = row.querySelector('td:nth-child(7)');
                if (statusCell) statusCell.innerHTML = `<span class="badge-status-surat badge-status-surat--ditolak">Ditolak</span>`;

                var aksiCell = row.querySelector('td:nth-child(8)');
                if (aksiCell) {
                    var safeAlasan = alasan.replace(/'/g, "\\'");
                    var safeNama = (nama || '').replace(/'/g, "\\'");
                    aksiCell.innerHTML = `
                        <div class="surat-row-actions">
                            <button type="button" class="btn-surat-alasan" onclick="lihatAlasanTolak(${id}, '${safeNama}', '${safeAlasan}')" title="Lihat Catatan Alasan Penolakan">
                                <i class='bx bx-info-circle'></i> Detail Alasan
                            </button>
                        </div>
                    `;
                }

                if (prevStatus === 'menunggu') {
                    deltaMenunggu--;
                    deltaDitolak++;
                }
            }
        });

        if (deltaMenunggu !== 0 || deltaDisetujui !== 0 || deltaDitolak !== 0) {
            var elMenunggu = document.getElementById('stat-count-menunggu');
            var elDisetujui = document.getElementById('stat-count-disetujui');
            var elDitolak = document.getElementById('stat-count-ditolak');
            if (elMenunggu) elMenunggu.textContent = Math.max(0, parseInt(elMenunggu.textContent, 10) + deltaMenunggu);
            if (elDisetujui) elDisetujui.textContent = parseInt(elDisetujui.textContent, 10) + deltaDisetujui;
            if (elDitolak) elDitolak.textContent = parseInt(elDitolak.textContent, 10) + deltaDitolak;
        }
    }

    // Inisialisasi awal pagination & sinkronisasi
    document.addEventListener('DOMContentLoaded', function() {
        sinkronkanStatusDariStorage();
        renderSuratPagination();
    });
    sinkronkanStatusDariStorage();
    renderSuratPagination();
</script>
@endpush
