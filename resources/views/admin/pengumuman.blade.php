@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')
@section('meta_description', 'Kelola seluruh pengumuman RT 04 / RW 08 — WargaKu')

@section('breadcrumb')
    <span class="admin-topbar__breadcrumb-current">Kelola Pengumuman</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-pengumuman.css') }}">
@endpush

@section('content')

{{-- Page Header --}}
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-header__title">Kelola Pengumuman</h1>
        <p class="admin-page-header__sub">Daftar publikasi informasi dan pengumuman untuk seluruh warga RT 04 / RW 08</p>
    </div>
    <a href="{{ route('admin.pengumuman.create') }}" class="btn-tambah-pengumuman">
        <i class='bx bx-plus'></i> Tambah Pengumuman Baru
    </a>
</div>

{{-- Stat Mini Row --}}
<div class="kelola-stat-row">
    <div class="kelola-stat-mini">
        <div class="kelola-stat-mini__info">
            <p class="kelola-stat-mini__label">TOTAL PENGUMUMAN</p>
            <p class="kelola-stat-mini__number">12</p>
        </div>
        <div class="kelola-stat-mini__icon kelola-stat-mini__icon--total">
            <i class='bx bx-bullhorn'></i>
        </div>
    </div>
    <div class="kelola-stat-mini">
        <div class="kelola-stat-mini__info">
            <p class="kelola-stat-mini__label">TAYANG (PUBLISH)</p>
            <div style="display:flex;align-items:center;gap:8px">
                <p class="kelola-stat-mini__number">9</p>
                <span class="badge-publish-sm">Aktif</span>
            </div>
        </div>
        <div class="kelola-stat-mini__icon kelola-stat-mini__icon--pub">
            <i class='bx bx-check-circle'></i>
        </div>
    </div>
    <div class="kelola-stat-mini">
        <div class="kelola-stat-mini__info">
            <p class="kelola-stat-mini__label">DRAF (DRAFT)</p>
            <div style="display:flex;align-items:center;gap:8px">
                <p class="kelola-stat-mini__number">3</p>
                <span class="badge-draft-sm">Draft</span>
            </div>
        </div>
        <div class="kelola-stat-mini__icon kelola-stat-mini__icon--draft">
            <i class='bx bx-edit'></i>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="kelola-card">

    {{-- Toolbar --}}
    <div class="kelola-toolbar">
        <div class="kelola-toolbar__search-box">
            <span class="kelola-toolbar__search-icon"><i class='bx bx-search'></i></span>
            <input type="text" id="search-pengumuman" class="kelola-toolbar__search-input" placeholder="Cari judul pengumuman atau kata kunci...">
            <button type="button" class="kelola-toolbar__filter-btn" title="Filter Pencarian">
                <i class='bx bx-slider-alt'></i>
            </button>
        </div>

        <div class="kelola-toolbar__right">
            <div class="kelola-filter-group">
                <label for="filter-status">Status:</label>
                <select id="filter-status" class="kelola-select">
                    <option value="">Semua Status</option>
                    <option value="publish">Publish</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="kelola-filter-group">
                <label for="filter-urut">Urutkan:</label>
                <select id="filter-urut" class="kelola-select">
                    <option value="terbaru">Terbaru</option>
                    <option value="terlama">Terlama</option>
                    <option value="az">A–Z</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    @php
        use App\Data\DummyData;
        $pengumuman = DummyData::pengumuman();
    @endphp

    <div class="kelola-table-wrap">
        <table class="kelola-table" id="table-pengumuman">
            <thead>
                <tr>
                    <th style="width: 45px; text-align: center;">NO</th>
                    <th style="width: 80px;">THUMBNAIL</th>
                    <th>JUDUL PENGUMUMAN</th>
                    <th style="width: 140px;">TANGGAL PUBLISH</th>
                    <th style="width: 150px;">PEMBUAT / PENULIS</th>
                    <th style="width: 100px;">STATUS</th>
                    <th style="min-width: 180px; width: 190px; text-align: right; padding-right: 20px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengumuman as $i => $p)
                <tr data-index="{{ $i + 1 }}">
                    <td style="text-align: center; font-weight: 500; color: #495057;">{{ $i + 1 }}</td>
                    <td>
                        @if($p['gambar'])
                            <img src="{{ asset($p['gambar']) }}" alt="Thumbnail" class="kelola-thumb-img">
                        @else
                            <div class="kelola-thumb-img" style="background:#e9ecef;display:flex;align-items:center;justify-content:center;color:#adb5bd;font-size:18px;">
                                <i class='bx bx-image'></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="kelola-title-text">{{ $p['judul'] }}</div>
                        <div class="kelola-sub-text">{{ $p['sub'] ?? Str::limit($p['isi'], 85) }}</div>
                    </td>
                    <td style="white-space: nowrap; color: #495057; font-size: 12.5px;">
                        {{ \App\Data\DummyData::formatTanggal($p['created_at'], true) }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:5px;font-size:12.5px;color:#495057;font-weight:500;">
                            <i class='bx bx-user' style="color:#6c757d;font-size:14px;"></i>
                            Admin RT
                        </div>
                    </td>
                    <td>
                        @if($p['status_publikasi'] === 'publish')
                            <span class="badge-publish-sm">Publish</span>
                        @else
                            <span class="badge-draft-sm">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div class="kelola-actions">
                            <a href="{{ route('admin.pengumuman.edit', $p['id']) }}" class="btn-action-edit">
                                <i class='bx bx-edit'></i> Edit
                            </a>
                            <button type="button" class="btn-action-delete" onclick="konfirmasiHapus({{ $p['id'] }}, '{{ addslashes($p['judul']) }}')">
                                <i class='bx bx-trash'></i> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination Bar --}}
    <div class="kelola-pagination-bar">
        <div id="pagination-info">Menampilkan <strong>1</strong> sampai <strong>6</strong> dari <strong>12</strong> pengumuman</div>
        <div class="kelola-page-buttons">
            <button type="button" class="kelola-page-btn kelola-page-btn--disabled" id="btn-prev" disabled>Previous</button>
            <div id="pagination-numbers" style="display:flex;align-items:center;gap:5px;"></div>
            <button type="button" class="kelola-page-btn" id="btn-next">Next</button>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="modal-hapus" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:6px;padding:24px;max-width:420px;width:90%;box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:54px;height:54px;background:#fde8e8;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#dc3545;font-size:26px;">
                <i class='bx bx-trash'></i>
            </div>
            <h3 style="font-size:17px;font-weight:700;margin:0 0 8px;color:#212529">Hapus Pengumuman?</h3>
            <p style="color:#6c757d;font-size:13px;margin:0;line-height:1.4" id="modal-hapus-judul"></p>
        </div>
        <div style="display:flex;gap:12px;justify-content:center">
            <button onclick="tutupModal()" style="flex:1;background:#ffffff;border:1px solid #ced4da;color:#495057;padding:10px 18px;border-radius:4px;font-size:14px;font-weight:600;cursor:pointer;">Batal</button>
            <button onclick="tutupModal()" style="flex:1;background:#dc3545;border:none;color:#ffffff;padding:10px 18px;border-radius:4px;font-size:14px;font-weight:600;cursor:pointer;">Ya, Hapus</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const PAGE_SIZE = 6;
    let currentPage = 1;

    function getFilteredRows() {
        const q = (document.getElementById('search-pengumuman').value || '').toLowerCase().trim();
        const statusVal = (document.getElementById('filter-status').value || '').toLowerCase().trim();
        const allRows = Array.from(document.querySelectorAll('#table-pengumuman tbody tr'));

        return allRows.filter(function(row) {
            const judul = (row.querySelector('.kelola-title-text')?.textContent || '').toLowerCase();
            const sub = (row.querySelector('.kelola-sub-text')?.textContent || '').toLowerCase();
            const matchSearch = !q || judul.includes(q) || sub.includes(q);

            const badge = row.querySelector('.badge-publish-sm, .badge-draft-sm');
            const status = (badge ? badge.textContent : '').trim().toLowerCase();
            const matchStatus = !statusVal || status === statusVal;

            return matchSearch && matchStatus;
        });
    }

    function updatePagination() {
        const allRows = Array.from(document.querySelectorAll('#table-pengumuman tbody tr'));
        const matchedRows = getFilteredRows();
        const totalItems = matchedRows.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Sembunyikan semua row
        allRows.forEach(function(row) {
            row.style.display = 'none';
        });

        // Tampilkan row sesuai halaman aktif
        const startIndex = (currentPage - 1) * PAGE_SIZE;
        const endIndex = Math.min(startIndex + PAGE_SIZE, totalItems);

        for (let i = startIndex; i < endIndex; i++) {
            matchedRows[i].style.display = '';
        }

        // Update info counter
        const infoEl = document.getElementById('pagination-info');
        if (totalItems === 0) {
            infoEl.innerHTML = 'Menampilkan <strong>0</strong> pengumuman';
        } else {
            infoEl.innerHTML = 'Menampilkan <strong>' + (startIndex + 1) + '</strong> sampai <strong>' + endIndex + '</strong> dari <strong>' + totalItems + '</strong> pengumuman';
        }

        // Update tombol Previous
        const btnPrev = document.getElementById('btn-prev');
        if (currentPage <= 1) {
            btnPrev.disabled = true;
            btnPrev.classList.add('kelola-page-btn--disabled');
        } else {
            btnPrev.disabled = false;
            btnPrev.classList.remove('kelola-page-btn--disabled');
        }

        // Update tombol Next
        const btnNext = document.getElementById('btn-next');
        if (currentPage >= totalPages || totalItems === 0) {
            btnNext.disabled = true;
            btnNext.classList.add('kelola-page-btn--disabled');
        } else {
            btnNext.disabled = false;
            btnNext.classList.remove('kelola-page-btn--disabled');
        }

        // Render tombol nomor halaman
        const pageContainer = document.getElementById('pagination-numbers');
        pageContainer.innerHTML = '';

        for (let p = 1; p <= totalPages; p++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kelola-page-btn' + (p === currentPage ? ' kelola-page-btn--active' : '');
            btn.textContent = p;
            btn.addEventListener('click', (function(pageNumber) {
                return function() {
                    currentPage = pageNumber;
                    updatePagination();
                };
            })(p));
            pageContainer.appendChild(btn);
        }
    }

    // Tombol Previous & Next click events
    document.getElementById('btn-prev').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    });

    document.getElementById('btn-next').addEventListener('click', function() {
        const matchedRows = getFilteredRows();
        const totalPages = Math.ceil(matchedRows.length / PAGE_SIZE);
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
        }
    });

    // Search filter event
    document.getElementById('search-pengumuman').addEventListener('input', function() {
        currentPage = 1;
        updatePagination();
    });

    // Status filter event
    document.getElementById('filter-status').addEventListener('change', function() {
        currentPage = 1;
        updatePagination();
    });

    // Urutkan event
    document.getElementById('filter-urut').addEventListener('change', function() {
        const tbody = document.querySelector('#table-pengumuman tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const val = this.value;

        rows.sort(function(a, b) {
            if (val === 'az') {
                const titleA = a.querySelector('.kelola-title-text').textContent.trim();
                const titleB = b.querySelector('.kelola-title-text').textContent.trim();
                return titleA.localeCompare(titleB);
            }
            const idxA = parseInt(a.dataset.index || 0);
            const idxB = parseInt(b.dataset.index || 0);
            return val === 'terlama' ? (idxB - idxA) : (idxA - idxB);
        });

        rows.forEach(function(r) { tbody.appendChild(r); });
        currentPage = 1;
        updatePagination();
    });

    // Modal konfirmasi hapus
    function konfirmasiHapus(id, judul) {
        document.getElementById('modal-hapus-judul').textContent = '"' + judul + '"';
        var modal = document.getElementById('modal-hapus');
        modal.style.display = 'flex';
    }

    function tutupModal() {
        document.getElementById('modal-hapus').style.display = 'none';
    }

    document.getElementById('modal-hapus').addEventListener('click', function(e) {
        if (e.target === this) tutupModal();
    });

    // Inisialisasi awal pagination
    updatePagination();
</script>
@endpush