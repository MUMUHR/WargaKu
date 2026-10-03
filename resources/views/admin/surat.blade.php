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
        <div class="surat-stat-box__val">3</div>
        <div class="surat-stat-box__label">MENUNGGU VERIFIKASI</div>
        <div class="surat-stat-box__sub">Memerlukan tindakan persetujuan RT</div>
        <i class='bx bx-time-five surat-stat-box__icon'></i>
    </div>
    <div class="surat-stat-box surat-stat-box--green" style="cursor: pointer;" onclick="filterByBox('Disetujui')" title="Klik untuk filter status Disetujui">
        <div class="surat-stat-box__val">28</div>
        <div class="surat-stat-box__label">SURAT DISETUJUI</div>
        <div class="surat-stat-box__sub">Nomor registrasi resmi terbit</div>
        <i class='bx bx-envelope-open surat-stat-box__icon'></i>
    </div>
    <div class="surat-stat-box surat-stat-box--red" style="cursor: pointer;" onclick="filterByBox('Ditolak')" title="Klik untuk filter status Ditolak">
        <div class="surat-stat-box__val">2</div>
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
        <div class="sync-indicator-text">
            <span class="sync-dot-green">●</span>
            <span>Auto-sinkronisasi Database Aktif</span>
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
                    data-status="{{ strtolower($s['status']) }}"
                    data-pemohon="{{ strtolower($s['pemohon_nama']) }}"
                    data-nik="{{ strtolower($s['pemohon_nik']) }}"
                    data-tujuan="{{ strtolower($s['warga_tujuan']) }}"
                    data-keperluan="{{ strtolower($s['keperluan']) }}"
                    data-nomor="{{ strtolower($s['nomor_surat'] ?? '') }}">
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
                        @elseif($s['status'] === 'Ditolak')
                            <span style="color: #dc3545; font-size: 11.5px; font-weight: 600;">Dibatalkan</span>
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
                                <button type="button" class="btn-surat-preview-draft" onclick="bukaModalSurat('preview', {{ $s['id'] }}, '{{ addslashes($s['pemohon_nama']) }}')" title="Pratinjau Draf Surat">
                                    <i class='bx bx-show'></i> Preview Draft
                                </button>
                                <button type="button" class="btn-surat-setujui" onclick="setujuiSurat({{ $s['id'] }}, '{{ addslashes($s['pemohon_nama']) }}')" title="Setujui dan Terbitkan Nomor">
                                    <i class='bx bx-check'></i> Setujui
                                </button>
                                <button type="button" class="btn-surat-tolak" onclick="tolakSurat({{ $s['id'] }}, '{{ addslashes($s['pemohon_nama']) }}')" title="Tolak Permohonan">
                                    <i class='bx bx-x'></i> Tolak
                                </button>
                            </div>
                        @elseif($s['status'] === 'Disetujui')
                            <div class="surat-row-actions">
                                <button type="button" class="btn-surat-cetak" onclick="cetakSurat({{ $s['id'] }}, '{{ $s['nomor_surat'] }}')" title="Cetak Surat Pengantar Resmi">
                                    <i class='bx bx-printer'></i> Preview &amp; Cetak
                                </button>
                            </div>
                        @else
                            <div class="surat-row-actions">
                                <button type="button" class="btn-surat-alasan" onclick="lihatAlasanTolak({{ $s['id'] }}, '{{ addslashes($s['pemohon_nama']) }}')" title="Lihat Catatan Alasan Penolakan">
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
    {{-- Pagination Bar --}}
    <div class="surat-pagination-bar">
        <div id="info-surat-count">Showing <strong>1 to 5</strong> of <strong>8</strong> entries</div>
        <div class="surat-pagination" id="pagination-surat"></div>
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
        var allRows = Array.from(document.querySelectorAll('#tbody-surat tr.row-surat-item'));

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

    // Aksi Surat
    function bukaModalSurat(aksi, id, nama) {
        alert('Membuka pratinjau draf surat untuk pemohon: ' + nama + ' (ID #' + id + ')');
    }

    function setujuiSurat(id, nama) {
        var noSuratBaru = '05/SP/RT04/II/2026';
        if (confirm('Setujui pengajuan surat dari ' + nama + '?\nNomor registrasi resmi ' + noSuratBaru + ' akan diterbitkan.')) {
            alert('Pengajuan surat ' + nama + ' berhasil disetujui.\nNomor Surat: ' + noSuratBaru);
        }
    }

    function tolakSurat(id, nama) {
        var alasan = prompt('Masukkan catatan alasan penolakan permohonan surat dari ' + nama + ':', 'Data pendukung belum lengkap.');
        if (alasan) {
            alert('Permohonan surat dari ' + nama + ' telah ditolak dengan alasan:\n"' + alasan + '"');
        }
    }

    function cetakSurat(id, noSurat) {
        alert('Membuka pratinjau dan dialog cetak dokumen resmi Nomor: ' + (noSurat || 'Surat Pengantar') + '...');
        window.print();
    }

    function lihatAlasanTolak(id, nama) {
        alert('Catatan Alasan Penolakan (' + nama + '):\n\n"Berkas belum memenuhi ketentuan administrasi RT dan kelengkapan dokumen identitas belum diverifikasi."');
    }

    function lihatProfilPemohon(nama, nik) {
        alert('Profil Pemohon: ' + nama + '\nNIK: ' + nik + '\nWilayah: RT 04 / RW 08');
    }

    // Inisialisasi awal pagination
    document.addEventListener('DOMContentLoaded', renderSuratPagination);
    renderSuratPagination();
</script>
@endpush
