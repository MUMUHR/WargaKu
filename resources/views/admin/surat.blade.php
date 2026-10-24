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

{{-- ========================================================
     HEADER HALAMAN
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-header__title" id="page-title" style="margin:0;">Proses Pengajuan Surat Pengantar Warga</h1>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 18px; border-radius: 6px; margin-bottom: 16px; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
        <i class='bx bx-check-circle' style="font-size: 20px; color: #10b981;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 6px; margin-bottom: 16px; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
        <i class='bx bx-error-circle' style="font-size: 20px; color: #ef4444;"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if(session('info'))
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 18px; border-radius: 6px; margin-bottom: 16px; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
        <i class='bx bx-info-circle' style="font-size: 20px; color: #3b82f6;"></i>
        <span>{{ session('info') }}</span>
    </div>
@endif

{{-- ========================================================
     TOP STAT CARDS: 3 ADMINLTE SMALL BOXES
     ======================================================== --}}
<div class="surat-stats-grid">
    <div class="surat-stat-box surat-stat-box--amber" style="cursor: pointer;" onclick="filterByBox('menunggu')" title="Klik untuk filter status Menunggu">
        <div class="surat-stat-box__val" id="stat-count-menunggu">{{ $statMenunggu ?? 0 }}</div>
        <div class="surat-stat-box__label">MENUNGGU VERIFIKASI</div>
        <div class="surat-stat-box__sub">Memerlukan tindakan persetujuan RT</div>
        <i class='bx bx-time-five surat-stat-box__icon'></i>
    </div>
    <div class="surat-stat-box surat-stat-box--green" style="cursor: pointer;" onclick="filterByBox('disetujui')" title="Klik untuk filter status Disetujui">
        <div class="surat-stat-box__val" id="stat-count-disetujui">{{ $statDisetujui ?? 0 }}</div>
        <div class="surat-stat-box__label">SURAT DISETUJUI</div>
        <div class="surat-stat-box__sub">Nomor registrasi resmi terbit</div>
        <i class='bx bx-envelope-open surat-stat-box__icon'></i>
    </div>
    <div class="surat-stat-box surat-stat-box--red" style="cursor: pointer;" onclick="filterByBox('ditolak')" title="Klik untuk filter status Ditolak">
        <div class="surat-stat-box__val" id="stat-count-ditolak">{{ $statDitolak ?? 0 }}</div>
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
            <option value="menunggu">Menunggu</option>
            <option value="disetujui">Disetujui</option>
            <option value="ditolak">Ditolak</option>
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
            <span class="badge-count-surat" id="badge-total-surat">Menampilkan {{ count($surats ?? []) }} permohonan</span>
        </div>
    </div>

    <div class="surat-table-wrap">
        <table class="surat-table" id="table-pengajuan-surat">
            <colgroup>
                <col style="width: 45px;">
                <col style="width: 140px;">
                <col style="width: 155px;">
                <col style="width: 155px;">
                <col style="width: 200px;">
                <col style="width: 215px;">
                <col style="width: 105px;">
                <col style="width: 240px;">
            </colgroup>
            <thead>
                <tr>
                    <th style="text-align: center;">NO</th>
                    <th>TANGGAL PENGAJUAN</th>
                    <th>PEMOHON</th>
                    <th>DATA WARGA TUJUAN</th>
                    <th>KEPERLUAN</th>
                    <th>NOMOR SURAT</th>
                    <th style="text-align: center;">STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="tbody-surat">
                @forelse($surats as $idx => $s)
                @php
                    $statusStr = is_object($s->status) ? $s->status->value : $s->status;
                    $statusLabel = is_object($s->status) ? $s->status->label() : ucfirst($s->status);
                    $tglStr = $s->created_at ? $s->created_at->format('d M Y') : '-';
                    $jamStr = $s->created_at ? $s->created_at->format('H:i') . ' WIB' : '-';
                    $pemohonNama = $s->pemohon?->nama ?? 'Warga RT';
                    $pemohonNik = $s->pemohon?->nik ?? '-';
                    $tujuanNama = $s->wargaTujuan?->nama ?? $pemohonNama;
                    $tujuanNik = $s->wargaTujuan?->nik ?? '-';
                @endphp
                <tr class="row-surat-item"
                    id="row-surat-{{ $s->id }}"
                    data-id="{{ $s->id }}"
                    data-status="{{ strtolower($statusStr) }}"
                    data-pemohon="{{ strtolower($pemohonNama) }}"
                    data-pemohon-raw="{{ $pemohonNama }}"
                    data-nik="{{ strtolower($pemohonNik) }}"
                    data-tujuan="{{ strtolower($tujuanNama) }}"
                    data-tujuan-raw="{{ $tujuanNama }}"
                    data-keperluan="{{ strtolower($s->keperluan) }}"
                    data-nomor="{{ strtolower($s->nomor_surat ?? '') }}"
                    data-alasan="{{ $s->alasan ?? '-' }}">
                    <td style="text-align: center; color: #495057; font-weight: 500;">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight: 700; color: #212529; font-size: 12.5px;">{{ $tglStr }}</div>
                        <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">{{ $jamStr }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #212529; font-size: 13px;">
                            {{ $pemohonNama }}
                            @if($s->pemohon?->status_warga === 'meninggal')
                                <span style="display:inline-block; font-size:10px; font-weight:700; color:#dc2626; background:#fee2e2; padding:1px 5px; border-radius:4px; margin-left:3px;">(Alm.)</span>
                            @endif
                        </div>
                        <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">NIK: {{ $pemohonNik }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #212529; font-size: 13px;">
                            {{ $tujuanNama }}
                            @if($s->wargaTujuan?->status_warga === 'meninggal')
                                <span style="display:inline-block; font-size:10px; font-weight:700; color:#dc2626; background:#fee2e2; padding:1px 5px; border-radius:4px; margin-left:3px;">(Alm.)</span>
                            @endif
                        </div>
                        <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">NIK: {{ $tujuanNik }}</div>
                    </td>
                    <td>
                        <div style="color: #495057; font-size: 12px; line-height: 1.4;">{{ $s->keperluan }}</div>
                    </td>
                    <td>
                        @if($statusStr === 'disetujui' && $s->nomor_surat)
                            <div class="nomor-surat-copy" onclick="copyNomorSurat('{{ $s->nomor_surat }}')" title="Klik untuk menyalin nomor surat">
                                <span>{{ $s->nomor_surat }}</span>
                                <i class='bx bx-copy'></i>
                            </div>
                        @else
                            <span style="color: #adb5bd; font-size: 13px;">-</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($statusStr === 'menunggu')
                            <span class="badge-status-surat badge-status-surat--menunggu">Menunggu</span>
                        @elseif($statusStr === 'disetujui')
                            <span class="badge-status-surat badge-status-surat--disetujui">Disetujui</span>
                        @else
                            <span class="badge-status-surat badge-status-surat--ditolak">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        @if($statusStr === 'menunggu')
                            <div class="surat-row-actions">
                                <a href="{{ route('admin.surat.preview', $s->id) }}" class="btn-surat-preview-draft" title="Pratinjau Draf Surat">
                                    <i class='bx bx-show'></i> Preview
                                </a>
                                <button type="button" class="btn-surat-setujui" onclick="bukaModalSetujui({{ $s->id }}, '{{ addslashes($pemohonNama) }}', '{{ addslashes($tujuanNama) }}', '{{ $rekomendasiNomorSurat ?? '' }}')" title="Setujui dan Terbitkan Nomor">
                                    <i class='bx bx-check'></i> Setujui
                                </button>
                                <button type="button" class="btn-surat-tolak" onclick="bukaModalTolak({{ $s->id }}, '{{ addslashes($pemohonNama) }}', '{{ addslashes($tujuanNama) }}')" title="Tolak Permohonan">
                                    <i class='bx bx-x'></i> Tolak
                                </button>
                            </div>
                        @elseif($statusStr === 'disetujui')
                            <div class="surat-row-actions">
                                <a href="{{ route('admin.surat.preview', $s->id) }}" class="btn-surat-cetak" title="Cetak Surat Pengantar Resmi">
                                    <i class='bx bx-printer'></i> Preview &amp; Cetak
                                </a>
                            </div>
                        @else
                            <div class="surat-row-actions">
                                <button type="button" class="btn-surat-alasan" onclick="lihatAlasanTolak({{ $s->id }}, '{{ addslashes($pemohonNama) }}', '{{ addslashes($s->alasan ?? 'Dokumen belum memenuhi syarat.') }}')" title="Lihat Catatan Alasan Penolakan">
                                    <i class='bx bx-info-circle'></i> Detail Alasan
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 36px 16px; color: #64748b;">
                        <i class='bx bx-envelope' style="font-size: 38px; color: #cbd5e1; display: block; margin-bottom: 6px;"></i>
                        <div style="font-weight: 600; font-size: 14px; color: #334155;">Belum Ada Pengajuan Surat Pengantar</div>
                        <div style="font-size: 12px; margin-top: 4px;">Warga belum mengajukan permohonan surat pengantar lingkungan RT.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Bar --}}
    <div class="surat-pagination-bar">
        <div id="info-surat-count">Showing <strong>1 to {{ min(5, count($surats ?? [])) }}</strong> of <strong>{{ count($surats ?? []) }}</strong> entries</div>
        <div class="surat-pagination" id="pagination-surat"></div>
    </div>
</div>

{{-- ═════════════════════════════════════════════════════════
     MODAL 1: PERSETUJUAN & PENERBITAN SURAT PENGANTAR (2.2.14)
     ═════════════════════════════════════════════════════════ --}}
<div class="surat-modal-overlay" id="modal-persetujuan-surat" onclick="if(event.target===this) tutupModalSetujui()">
    <div class="surat-modal-dialog">
        <form method="POST" id="form-setujui-surat" action="">
            @csrf
            <div class="surat-modal-header surat-modal-header--green">
                <h3 class="surat-modal-title">
                    <i class='bx bx-check-shield' style="font-size: 19px;"></i>
                    Persetujuan &amp; Penerbitan Surat Pengantar
                </h3>
                <button type="button" class="surat-modal-close-btn" onclick="tutupModalSetujui()">&times;</button>
            </div>
            <div class="surat-modal-body">
                <div class="surat-modal-info-block">
                    <div class="surat-modal-info-row">
                        <span class="surat-modal-info-label">Pemohon:</span>
                        <span class="surat-modal-info-val surat-modal-info-val--blue" id="modal-setujui-pemohon">-</span>
                    </div>
                    <div class="surat-modal-info-row">
                        <span class="surat-modal-info-label">Warga Tujuan:</span>
                        <span class="surat-modal-info-val surat-modal-info-val--dark" id="modal-setujui-tujuan">-</span>
                    </div>
                </div>

                <div class="surat-modal-grid-2">
                    <div class="surat-modal-field">
                        <label class="surat-modal-label" for="modal-setujui-nomor">
                            <i class='bx bx-id-card'></i> Nomor Surat Resmi RT
                            <span style="font-size: 10.5px; background: #e0f2fe; color: #0369a1; padding: 2px 7px; border-radius: 4px; font-weight: 600; margin-left: 6px;">
                                <i class='bx bx-bot'></i> Otomatis Sistem
                            </span>
                        </label>
                        <input type="text" name="nomor_surat" id="modal-setujui-nomor" class="surat-modal-input" readonly style="background: #f8fafc; font-weight: 700; color: #0f172a; cursor: not-allowed;" value="{{ $rekomendasiNomorSurat ?? '' }}">
                        <div class="surat-modal-subtext">Dibuat otomatis berurutan sesuai aturan BR-05 (reset tiap tahun)</div>
                    </div>

                    <div class="surat-modal-field">
                        <label class="surat-modal-label" for="modal-setujui-tanggal">
                            <i class='bx bx-calendar'></i> Tanggal Penerbitan <span class="req">*</span>
                        </label>
                        <input type="text" id="modal-setujui-tanggal" class="surat-modal-input" readonly value="{{ now()->translatedFormat('d F Y') }}">
                        <div class="surat-modal-subtext">Tanggal tertera pada surat pengantar</div>
                    </div>
                </div>
            </div>
            <div class="surat-modal-footer">
                <button type="button" class="btn-modal-batal" onclick="tutupModalSetujui()">Batal</button>
                <button type="submit" class="btn-modal-simpan">
                    <i class='bx bx-check'></i> Simpan &amp; Terbitkan Surat
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═════════════════════════════════════════════════════════
     MODAL 2: PENOLAKAN SURAT PENGANTAR (2.2.15)
     ═════════════════════════════════════════════════════════ --}}
<div class="surat-modal-overlay" id="modal-tolak-surat" onclick="if(event.target===this) tutupModalTolak()">
    <div class="surat-modal-dialog">
        <form method="POST" id="form-tolak-surat" action="">
            @csrf
            <div class="surat-modal-header surat-modal-header--red">
                <h3 class="surat-modal-title">
                    <i class='bx bx-x-circle' style="font-size: 19px;"></i>
                    Penolakan Permohonan Surat Pengantar
                </h3>
                <button type="button" class="surat-modal-close-btn" onclick="tutupModalTolak()">&times;</button>
            </div>
            <div class="surat-modal-body">
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
                            <i class='bx bx-message-error' style="color: #dc3545;"></i> Alasan Penolakan RT
                        </label>
                        <input type="text" name="alasan" id="modal-tolak-alasan" class="surat-modal-input" placeholder="Masukkan alasan penolakan..." value="">
                        <div class="surat-modal-subtext">Catatan ini akan tersimpan pada detail riwayat permohonan</div>
                    </div>

                    <div class="surat-modal-field">
                        <label class="surat-modal-label" for="modal-tolak-tanggal">
                            <i class='bx bx-calendar'></i> Tanggal Penolakan
                        </label>
                        <input type="text" id="modal-tolak-tanggal" class="surat-modal-input" readonly value="{{ now()->translatedFormat('d F Y') }}">
                        <div class="surat-modal-subtext">Tanggal tercatat dalam sistem administrasi</div>
                    </div>
                </div>
            </div>
            <div class="surat-modal-footer">
                <button type="button" class="btn-modal-batal" onclick="tutupModalTolak()">Batal</button>
                <button type="submit" class="btn-modal-tolak">
                    <i class='bx bx-x'></i> Tolak Permohonan Surat
                </button>
            </div>
        </form>
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
            <div id="modal-alasan-pemohon" style="font-size: 15px; font-weight: 700; color: #212529; margin-bottom: 12px;">-</div>

            <div class="surat-alasan-box">
                <div class="surat-alasan-box__title">Keterangan Pengurus RT:</div>
                <div class="surat-alasan-box__desc" id="modal-alasan-text">
                    -
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

        if (allRows.length === 0) return;

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
            badgeTotal.textContent = 'Menampilkan ' + totalItems + ' dari ' + allRows.length + ' permohonan';
        }

        var infoEl = document.getElementById('info-surat-count');
        if (infoEl) {
            if (totalItems === 0) {
                infoEl.innerHTML = 'Tidak ada permohonan yang sesuai kriteria filter';
            } else {
                infoEl.innerHTML = 'Showing <strong>' + (startIndex + 1) + ' to ' + endIndex + '</strong> of <strong>' + totalItems + '</strong> entries';
            }
        }

        var container = document.getElementById('pagination-surat');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1 && totalItems <= SURAT_PAGE_SIZE) {
            var bPrev = createSuratPageBtn('« Sebelumnya', true, false, function(){});
            var b1 = createSuratPageBtn('1', false, true, function(){});
            var bNext = createSuratPageBtn('Berikutnya »', true, false, function(){});
            container.appendChild(bPrev);
            container.appendChild(b1);
            container.appendChild(bNext);
            return;
        }

        // Previous
        var prevDisabled = (currentSuratPage <= 1);
        var btnPrev = createSuratPageBtn('« Sebelumnya', prevDisabled, false, function() {
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
        var btnNext = createSuratPageBtn('Berikutnya »', nextDisabled, false, function() {
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
    function bukaModalSetujui(id, pemohon, tujuan, rekomendasiNomor) {
        var form = document.getElementById('form-setujui-surat');
        if (form) {
            form.action = '/admin/surat/' + id + '/setujui';
        }
        document.getElementById('modal-setujui-pemohon').textContent = pemohon;
        document.getElementById('modal-setujui-tujuan').textContent = tujuan || pemohon;

        var noInput = document.getElementById('modal-setujui-nomor');
        if (noInput && rekomendasiNomor) {
            noInput.value = rekomendasiNomor;
        }

        document.getElementById('modal-persetujuan-surat').style.display = 'flex';
    }

    function tutupModalSetujui() {
        document.getElementById('modal-persetujuan-surat').style.display = 'none';
    }

    // ── Modal Penolakan Surat ──
    function bukaModalTolak(id, pemohon, tujuan) {
        var form = document.getElementById('form-tolak-surat');
        if (form) {
            form.action = '/admin/surat/' + id + '/tolak';
        }
        document.getElementById('modal-tolak-pemohon').textContent = pemohon;
        document.getElementById('modal-tolak-tujuan').textContent = tujuan || pemohon;
        document.getElementById('modal-tolak-alasan').value = '';
        document.getElementById('modal-tolak-surat').style.display = 'flex';
    }

    function tutupModalTolak() {
        document.getElementById('modal-tolak-surat').style.display = 'none';
    }

    // ── Modal Catatan Alasan Penolakan ──
    function lihatAlasanTolak(id, nama, alasan) {
        document.getElementById('modal-alasan-pemohon').textContent = nama;
        document.getElementById('modal-alasan-text').textContent = alasan || 'Tidak ada catatan alasan yang dilampirkan.';
        document.getElementById('modal-alasan-tolak').style.display = 'flex';
    }

    function tutupModalAlasan() {
        document.getElementById('modal-alasan-tolak').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderSuratPagination();
    });
</script>
@endpush
