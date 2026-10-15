@extends('layouts.admin')

@section('title', 'Kelola Data Warga & Verifikasi Kependudukan')
@section('meta_description', 'Kelola data master kependudukan warga RT 04, status mutasi, dan validasi permohonan perubahan data dari warga.')

@section('breadcrumb')
    <span class="admin-topbar__breadcrumb-current" id="topbar-breadcrumb-label">Kelola Data Warga</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-warga.css') }}">
    <link rel="stylesheet" href="{{ asset('css/warga-keluarga.css') }}">
@endpush

@section('content')

@php
    if (isset($keluargas) && $keluargas->count() > 0) {
        $listKk = [];
        foreach ($keluargas as $k) {
            $kepala = $k->kepalaKeluarga;
            $wargasHidup = $k->wargas->where('status_warga', 'hidup');
            $laki = $wargasHidup->where('jenis_kelamin', 'laki_laki')->count();
            $perempuan = $wargasHidup->where('jenis_kelamin', 'perempuan')->count();
            $alamat = $kepala?->alamat ?? $wargasHidup->first(fn($w) => !empty($w->alamat))?->alamat ?? 'RT 04 / RW 08';

            preg_match('/Blok\s+[A-Za-z0-9]+/i', $alamat, $mBlok);
            $blok = $mBlok[0] ?? 'RT 04';

            $kontak = $k->kepalaKeluarga?->user?->no_telp 
                ?? $wargasHidup->first(fn($w) => $w->user && $w->user->no_telp)?->user?->no_telp 
                ?? '-';

            $tinggalBadge = match($k->status_tinggal) {
                'tetap' => 'Tetap',
                'kontrak' => 'Kontrak / Sewa',
                'kos' => 'Kos',
                'menumpang' => 'Menumpang',
                default => ucfirst($k->status_tinggal)
            };

            $ekonomiBadge = match($k->status_ekonomi) {
                'kurang_mampu' => 'Kurang Mampu',
                default => 'Mampu'
            };

            $listKk[] = [
                'id' => $k->id,
                'no_kk' => $k->no_kk,
                'nama_kepala' => $kepala?->nama ?? 'Belum ditentukan',
                'nik_kepala' => $kepala?->nik ?? '-',
                'alamat' => $alamat,
                'blok' => $blok,
                'jumlah_anggota' => $wargasHidup->count(),
                'laki_laki' => $laki,
                'perempuan' => $perempuan,
                'status_tinggal' => $tinggalBadge,
                'status_tinggal_badge' => $tinggalBadge,
                'status_ekonomi' => $ekonomiBadge,
                'ekonomi_kategori' => $ekonomiBadge,
                'kontak' => $kontak,
            ];
        }
    } else {
        $listKk = $listKk ?? [
            [
                'no_kk' => '3275010905120008',
                'nama_kepala' => 'Bambang Santoso, S.T.',
                'nik_kepala' => '3275012304800001',
                'alamat' => 'Blok B4 No. 12, RT 04 / RW 08',
                'blok' => 'Blok B',
                'jumlah_anggota' => 3,
                'laki_laki' => 1,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Kurang Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0812-8901-2345',
            ],
            [
                'no_kk' => '3275012010150012',
                'nama_kepala' => 'Hendro Gunawan',
                'nik_kepala' => '3275011005820002',
                'alamat' => 'Blok A1 No. 05, RT 04 / RW 08',
                'blok' => 'Blok A',
                'jumlah_anggota' => 4,
                'laki_laki' => 2,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0813-5678-9012',
            ],
            [
                'no_kk' => '3275011802110003',
                'nama_kepala' => 'Achmad Fauzi, S.Pd.',
                'nik_kepala' => '3275011802790003',
                'alamat' => 'Blok C2 No. 08, RT 04 / RW 08',
                'blok' => 'Blok C',
                'jumlah_anggota' => 5,
                'laki_laki' => 2,
                'perempuan' => 3,
                'status_tinggal' => 'Kontrak / Sewa',
                'status_tinggal_badge' => 'Kontrak / Sewa',
                'status_ekonomi' => 'Kurang Mampu',
                'ekonomi_kategori' => 'Kurang Mampu',
                'kontak' => '0856-7788-9901',
            ],
            [
                'no_kk' => '3275012408190021',
                'nama_kepala' => 'Ibu Sumarti',
                'nik_kepala' => '3275016408650005',
                'alamat' => 'Blok D3 No. 02, RT 04 / RW 08',
                'blok' => 'Blok D',
                'jumlah_anggota' => 2,
                'laki_laki' => 0,
                'perempuan' => 2,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0878-1122-3344',
            ],
            [
                'no_kk' => '3275010503220035',
                'nama_kepala' => 'Raden Dimas Arya',
                'nik_kepala' => '3275010503900004',
                'alamat' => 'Blok B1 No. 17, RT 04 / RW 08',
                'blok' => 'Blok B',
                'jumlah_anggota' => 3,
                'laki_laki' => 2,
                'perempuan' => 1,
                'status_tinggal' => 'Tetap (Milik Sendiri)',
                'status_tinggal_badge' => 'Tetap',
                'status_ekonomi' => 'Mampu',
                'ekonomi_kategori' => 'Mampu',
                'kontak' => '0811-2233-4455',
            ],
        ];
    }

@endphp

@if(session('success'))
    <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 18px;border-radius:6px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class='bx bx-check-circle' style="font-size:20px;color:#28a745;"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#155724;line-height:1;">&times;</button>
    </div>
@endif

@if(session('error'))
    <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 18px;border-radius:6px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class='bx bx-error-circle' style="font-size:20px;color:#dc3545;"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#721c24;line-height:1;">&times;</button>
    </div>
@endif

{{-- ========================================================
     HEADER HALAMAN DAFTAR WARGA
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <nav style="font-size:12px;color:#6c757d;margin-bottom:4px;display:flex;align-items:center;gap:4px;" id="header-breadcrumb">
            <i class='bx bx-home-alt'></i> <span>Portal RT 04</span>
            <span style="margin: 0 2px;">/</span>
            <span style="color:#007bff;font-weight:600">Kelola Data Warga</span>
        </nav>
        <h1 class="admin-page-header__title" id="page-title">Kelola Data Warga &amp; Verifikasi Kependudukan</h1>
        <p class="admin-page-header__sub" id="page-subtitle">Kelola data master kependudukan warga RT 04, status mutasi, dan validasi permohonan perubahan data dari warga.</p>
    </div>
</div>

{{-- ========================================================
     TOP STAT CARDS: VIEW UTAMA (3 BOXES)
     ======================================================== --}}
<div class="warga-stats-grid" id="stats-main">
    <div class="warga-stat-box warga-stat-box--cyan">
        <div class="warga-stat-box__val">{{ $totalKkAktif ?? count($listKk) }} <small>KK</small></div>
        <div class="warga-stat-box__label">Total Kartu Keluarga Aktif</div>
        <i class='bx bx-home-alt warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--green">
        <div class="warga-stat-box__val">{{ $totalWargaHidup ?? 312 }} <small>Jiwa</small></div>
        <div class="warga-stat-box__label">Warga Terdaftar (Hidup)</div>
        <div class="warga-stat-box__sub">{{ $totalLakiLaki ?? 158 }} Laki-laki • {{ $totalPerempuan ?? 154 }} Perempuan</div>
        <i class='bx bx-group warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--amber">
        <div class="warga-stat-box__val">{{ $totalMenungguVerifikasi ?? (isset($daftarPengajuan) ? $daftarPengajuan->total() : 5) }} <small>Pengajuan</small></div>
        <div class="warga-stat-box__label">Menunggu Verifikasi RT</div>
        <i class='bx bx-clipboard warga-stat-box__icon'></i>
    </div>
</div>

{{-- ========================================================
     CONTAINER VIEW UTAMA (2 TABS)
     ======================================================== --}}
<div class="warga-card-container" id="view-main">

    {{-- Tab Navigation Bar --}}
    <div class="warga-tabs-nav">
        <div class="warga-tabs-left">
            <button type="button" class="warga-tab-btn warga-tab-btn--active" id="tab-btn-kk" onclick="switchWargaTab('kk')">
                <i class='bx bx-id-card'></i>
                Daftar Warga &amp; Kartu Keluarga
            </button>
            <button type="button" class="warga-tab-btn" id="tab-btn-verifikasi" onclick="switchWargaTab('verifikasi')" style="position: relative;">
                <i class='bx bx-slider-alt'></i>
                Verifikasi Pengajuan Perubahan Data
                @if(($totalMenungguVerifikasi ?? 0) > 0)
                    <span class="badge-tab-dot" id="badge-tab-dot" title="{{ $totalMenungguVerifikasi }} Pengajuan Menunggu"></span>
                @endif
            </button>
        </div>
        <div class="wilayah-info-text">
            <span class="wilayah-dot">●</span>
            <span>Wilayah: <strong>RT 04 / RW 08</strong> (Kelurahan Sukamaju)</span>
        </div>
    </div>

    {{-- ═══ TAB 1: LIST SEMUA KARTU KELUARGA ═══ --}}
    <div id="panel-tab-kk">

        {{-- Filter Toolbar --}}
        <div class="warga-filter-bar">
            <div class="warga-filter-col warga-filter-col--search">
                <label class="warga-filter-label" for="search-kk">Pencarian KK:</label>
                <div style="position:relative;">
                    <i class='bx bx-search' style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:15px;"></i>
                    <input type="text" id="search-kk" class="warga-filter-input" style="padding-left:32px;" placeholder="Cari No. KK, Nama Kepala Keluarga, atau Blok...">
                </div>
            </div>
            <div class="warga-filter-col warga-filter-col--select">
                <label class="warga-filter-label" for="filter-tinggal">Status Tinggal:</label>
                <select id="filter-tinggal" class="warga-filter-input">
                    <option value="">Semua Status Tinggal</option>
                    <option value="tetap">Tetap</option>
                    <option value="kontrak">Kontrak / Sewa</option>
                    <option value="kos">Kos</option>
                    <option value="menumpang">Menumpang</option>
                </select>
            </div>
            <div class="warga-filter-col warga-filter-col--select">
                <label class="warga-filter-label" for="filter-ekonomi">Status Ekonomi:</label>
                <select id="filter-ekonomi" class="warga-filter-input">
                    <option value="">Semua Ekonomi</option>
                    <option value="mampu">Mampu</option>
                    <option value="kurang_mampu">Kurang Mampu</option>
                </select>
            </div>
            <div class="warga-filter-col warga-filter-col--actions">
                <button type="button" class="btn-filter-reset" onclick="resetFilterKK()" title="Reset Filter">
                    <i class='bx bx-refresh'></i>
                </button>
            </div>
        </div>

        {{-- Tabel Kartu Keluarga --}}
        <div class="warga-table-wrap">
            <table class="warga-table" id="table-kk">
                <colgroup>
                    <col style="width: 3.5%;">
                    <col style="width: 15%;">
                    <col style="width: 22%;">
                    <col style="width: 12%;">
                    <col style="width: 16%;">
                    <col style="width: 13.5%;">
                    <col style="width: 18%;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">NO</th>
                        <th>NO. KARTU KELUARGA</th>
                        <th>KEPALA KELUARGA &amp; ALAMAT</th>
                        <th>JUMLAH ANGGOTA</th>
                        <th>STATUS TINGGAL &amp; EKONOMI</th>
                        <th>KONTAK / NO. HP</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listKk as $idx => $k)
                    <tr class="row-kk-item"
                        data-nokk="{{ $k['no_kk'] }}"
                        data-kepala="{{ strtolower($k['nama_kepala']) }}"
                        data-alamat="{{ strtolower($k['alamat']) }}"
                        data-tinggal="{{ strtolower($k['status_tinggal_badge']) }}"
                        data-ekonomi="{{ strtolower($k['status_ekonomi']) }}"
                        data-blok="{{ strtolower($k['blok']) }}">
                        <td style="text-align: center; color: #495057; font-weight: 500;">{{ $idx + 1 }}</td>
                        <td>
                            <a href="{{ route('admin.data-warga.detail', $k['no_kk']) }}" class="link-kk">
                                {{ $k['no_kk'] }}
                            </a>
                        </td>
                        <td>
                            <div class="nama-kepala-text">{{ $k['nama_kepala'] }}</div>
                            <div class="alamat-sub-text">{{ $k['alamat'] }}</div>
                        </td>
                        <td>
                            <span class="badge-anggota-count">
                                <i class='bx bx-user'></i> {{ $k['jumlah_anggota'] }} Jiwa
                            </span>
                        </td>
                        <td>
                            <div class="status-tags-wrap">
                                @if(str_contains(strtolower($k['status_tinggal_badge']), 'kontrak') || str_contains(strtolower($k['status_tinggal_badge']), 'sewa'))
                                    <span class="badge-status-sewa">{{ $k['status_tinggal_badge'] }}</span>
                                @else
                                    <span class="badge-status-tetap">{{ $k['status_tinggal_badge'] }}</span>
                                @endif

                                @if($k['status_ekonomi'])
                                    <span class="badge-ekonomi-gray">{{ $k['status_ekonomi'] }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $k['kontak']) }}" target="_blank" class="kontak-wa-link">
                                <i class='bx bxl-whatsapp'></i> {{ $k['kontak'] }}
                            </a>
                        </td>
                        <td>
                            <div class="warga-row-actions">
                                <a href="{{ route('admin.data-warga.detail', $k['no_kk']) }}" class="btn-act-detail" style="text-decoration:none;" title="Lihat Detail Kartu Keluarga & Anggota">
                                    <i class='bx bx-id-card'></i> Detail KK
                                </a>
                                <button type="button" class="btn-act-edit" onclick="bukaModalEditStatus('{{ $k['no_kk'] }}', '{{ addslashes($k['nama_kepala']) }}', '{{ $k['status_tinggal_badge'] }}', '{{ $k['status_ekonomi'] }}', this)" title="Edit Status KK">
                                    <i class='bx bx-edit'></i> Edit Data
                                </button>
                                <button type="button" class="btn-act-delete" onclick="konfirmasiHapusKK('{{ $k['no_kk'] }}', '{{ addslashes($k['nama_kepala']) }}', this)" title="Hapus KK">
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
        <div class="warga-pagination-bar">
            <div id="info-kk-count">Menampilkan <strong>1 - {{ min(10, count($listKk)) }}</strong> dari <strong>{{ count($listKk) }}</strong> Kepala Keluarga (Kartu Keluarga Aktif RT 04)</div>
            <div class="warga-page-buttons" id="pagination-kk"></div>
        </div>
    </div>

    {{-- ═══ TAB 2: VERIFIKASI PENGAJUAN PERUBAHAN DATA ═══ --}}
    <div id="panel-tab-verifikasi" style="display:none;">
        <div class="warga-table-wrap">
            <table class="warga-table" id="table-verifikasi">
                <colgroup>
                    <col style="width: 17%;">
                    <col style="width: 14%;">
                    <col style="width: 10%;">
                    <col style="width: 15%;">
                    <col style="width: 8%;">
                    <col style="width: 12%;">
                    <col style="width: 19%;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="padding-left: 18px;">PEMOHON / KEPALA KK</th>
                        <th>WAKTU</th>
                        <th>JENIS PENGAJUAN</th>
                        <th>WARGA TERDAMPAK</th>
                        <th>BERKAS</th>
                        <th>STATUS</th>
                        <th style="padding-right: 18px;">TINDAKAN PETUGAS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarPengajuan ?? [] as $v)
                    @php
                        $jenisLabel = match($v->jenis_pengajuan ?? '') {
                            'ubah_data'      => ['label' => 'Ubah Data',     'cls' => 'badge-aksi-ubah'],
                            'tambah_anggota' => ['label' => 'Tambah Anggota','cls' => 'badge-aksi-tambah'],
                            'pindah'         => ['label' => 'Pindah',         'cls' => 'badge-aksi-pindah'],
                            default          => ['label' => ucfirst(str_replace('_',' ', $v->jenis_pengajuan ?? 'Pengajuan')), 'cls' => 'badge-aksi-ubah']
                        };
                        $pemohonNama = $v->keluarga?->kepalaKeluarga?->nama ?? ($v->warga?->nama ?? 'Pemohon');
                        $noKkPemohon = $v->keluarga?->no_kk ?? '-';
                        $wargaTerdampak = $v->warga?->nama ?? ($v->data_baru['nama'] ?? '-');
                        $hasBukti = !empty($v->bukti_path);
                        $statusVerif = match($v->status) {
                            'menunggu'  => ['label' => 'Menunggu RT',  'cls' => 'badge-status-menunggu',   'icon' => 'bx bx-hourglass'],
                            'disetujui' => ['label' => 'Disetujui',    'cls' => 'badge-status-disetujui',  'icon' => 'bx bx-check-double'],
                            'ditolak'   => ['label' => 'Ditolak',      'cls' => 'badge-status-kos',        'icon' => 'bx bx-x-circle'],
                            default     => ['label' => ucfirst($v->status ?? '-'), 'cls' => 'badge-status-menunggu', 'icon' => 'bx bx-question-mark']
                        };
                    @endphp
                    <tr id="row-verif-{{ $v->id }}">
                        <td style="padding-left: 18px;">
                            <div class="nama-kepala-text">{{ $pemohonNama }}</div>
                            <div class="alamat-sub-text">KK : {{ $noKkPemohon }}</div>
                        </td>
                        <td style="color: #495057; font-size: 12px; white-space: nowrap;">
                            {{ $v->created_at ? \Carbon\Carbon::parse($v->created_at)->isoFormat('D MMM YYYY • HH:mm') . ' WIB' : '-' }}
                        </td>
                        <td>
                            <span class="{{ $jenisLabel['cls'] }}">{{ $jenisLabel['label'] }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #212529; font-size: 13px;">{{ $wargaTerdampak }}</div>
                        </td>
                        <td>
                            @if($hasBukti)
                                <span class="badge-lampiran-file">
                                    <i class='bx bxs-file-pdf' style="color: #dc3545; font-size: 14px;"></i>
                                    <span>Ada Berkas</span>
                                </span>
                            @else
                                <span style="color: #adb5bd; font-size: 12px;">—</span>
                            @endif
                        </td>
                        <td id="status-cell-verif-{{ $v->id }}">
                            <span class="{{ $statusVerif['cls'] }}">
                                <i class='{{ $statusVerif['icon'] }}'></i>
                                <span>{{ $statusVerif['label'] }}</span>
                            </span>
                        </td>
                        @php
                            $docsListAdmin = [];
                            $buktiList = $v->bukti_list;
                            foreach ($buktiList as $bIdx => $bPath) {
                                $bUrl = '';
                                if (str_starts_with($bPath, 'http://') || str_starts_with($bPath, 'https://')) {
                                    $bUrl = $bPath;
                                } elseif (file_exists(public_path($bPath))) {
                                    $bUrl = asset($bPath);
                                } elseif (file_exists(public_path('storage/' . $bPath))) {
                                    $bUrl = asset('storage/' . $bPath);
                                } else {
                                    $bUrl = asset($bPath);
                                }
                                $bName = basename($bPath);
                                $isPdf = str_ends_with(strtolower($bName), '.pdf');
                                $docsListAdmin[] = [
                                    'label' => ($bIdx + 1) . '. ' . $bName,
                                    'filename' => $bName,
                                    'url' => $bUrl,
                                    'isPdf' => $isPdf,
                                ];
                            }
                            $hasBukti = !empty($docsListAdmin);
                            $buktiUrl = $docsListAdmin[0]['url'] ?? '';
                            $buktiNama = $docsListAdmin[0]['filename'] ?? '';
                        @endphp
                        @php
                            $targetWarga = $v->warga;
                            $targetNama = $targetWarga?->nama ?? ($v->data_baru['nama'] ?? $wargaTerdampak);
                            $targetHub = $targetWarga ? ucwords(str_replace('_', ' ', $targetWarga->status_hubungan ?? '')) : (ucwords(str_replace('_', ' ', $v->data_baru['hubungan_keluarga'] ?? 'Anggota Keluarga')));
                            $targetJk = $targetWarga ? ($targetWarga->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : 'Perempuan') : (isset($v->data_baru['jenis_kelamin']) ? ($v->data_baru['jenis_kelamin'] === 'laki_laki' ? 'Laki-Laki' : 'Perempuan') : '-');
                            $targetTempatLahir = $targetWarga?->tempat_lahir ?? ($v->data_baru['tempat_lahir'] ?? '-');
                            $targetTglLahir = $targetWarga?->tanggal_lahir ? \Carbon\Carbon::parse($targetWarga->tanggal_lahir)->format('d-m-Y') : (isset($v->data_baru['tanggal_lahir']) ? \Carbon\Carbon::parse($v->data_baru['tanggal_lahir'])->format('d-m-Y') : '-');
                            $targetSub = 'NIK: ' . ($targetWarga?->nik ?? $v->data_baru['nik'] ?? $noKkPemohon) . ' • ' . $targetJk . ' • Lahir: ' . $targetTempatLahir . ', ' . $targetTglLahir;
                            $tglDiajukan = $v->created_at ? \Carbon\Carbon::parse($v->created_at)->isoFormat('D MMM YYYY, HH:mm') . ' WIB' : '-';
                            $alamatLengkap = $v->keluarga?->alamat ?? $targetWarga?->alamat ?? 'RT 04 / RW 08';
                            $alasanTeks = $v->alasan ?? 'Pengajuan perubahan data kependudukan oleh warga.';
                        @endphp
                        <td style="padding-right: 18px;">
                            @if($v->status === 'menunggu')
                            <div class="warga-row-actions" id="row-actions-verif-{{ $v->id }}">
                                {{-- Form Setujui --}}
                                <form method="POST" action="{{ route('admin.data-warga.verifikasi', $v->id) }}" id="form-setujui-{{ $v->id }}" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="tindakan" value="setujui">
                                    <input type="hidden" name="alasan" value="Disetujui oleh Pengurus RT.">
                                </form>
                                {{-- Form Tolak --}}
                                <form method="POST" action="{{ route('admin.data-warga.verifikasi', $v->id) }}" id="form-tolak-{{ $v->id }}" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="tindakan" value="tolak">
                                    <input type="hidden" name="alasan" id="alasan-tolak-{{ $v->id }}" value="Pengajuan ditolak oleh Pengurus RT.">
                                </form>
                                <button type="button" class="btn-tindakan-review"
                                    onclick='bukaModalReviewVerifikasi({
                                        id: {{ $v->id }},
                                        pemohon: "{{ addslashes($pemohonNama) }}",
                                        noKk: "{{ addslashes($noKkPemohon) }}",
                                        alamat: "{{ addslashes($alamatLengkap) }}",
                                        warga: "{{ addslashes($targetNama) }}",
                                        targetTitle: "Target Data: {{ addslashes($targetNama) }} ({{ addslashes($targetHub) }})",
                                        targetSub: "{{ addslashes($targetSub) }}",
                                        tglDiajukan: "Diajukan pada: {{ addslashes($tglDiajukan) }}",
                                        status: "{{ $v->status }}",
                                        statusLabel: "{{ $statusVerif['label'] }}",
                                        alasan: "{{ addslashes($alasanTeks) }}",
                                        jenis: "{{ $v->jenis_pengajuan }}",
                                        jenisLabel: "{{ $jenisLabel['label'] }}",
                                        dataLama: @json($v->data_lama ?? []),
                                        dataBaru: @json($v->data_baru ?? []),
                                        docs: @json($docsListAdmin),
                                        buktiUrl: "{{ $buktiUrl }}",
                                        buktiNama: "{{ addslashes($buktiNama) }}",
                                        hasBukti: {{ $hasBukti ? 'true' : 'false' }}
                                    })'>
                                    <i class='bx bx-show'></i> Review
                                </button>
                                <button type="button" class="btn-tindakan-setujui"
                                    onclick="konfirmasiSetujuiPengajuan({{ $v->id }}, '{{ addslashes($pemohonNama) }}')">
                                    <i class='bx bx-check'></i> Setujui
                                </button>
                                <button type="button" class="btn-act-delete" style="height:33px;padding:6px 10px;"
                                    onclick="bukaModalTolakPengajuan({{ $v->id }}, '{{ addslashes($pemohonNama) }}')">
                                    <i class='bx bx-x'></i> Tolak
                                </button>
                            </div>
                            @else
                            <div class="warga-row-actions" id="row-actions-verif-{{ $v->id }}">
                                <button type="button" class="btn-tindakan-review" style="margin-right: 6px;"
                                    onclick='bukaModalReviewVerifikasi({
                                        id: {{ $v->id }},
                                        pemohon: "{{ addslashes($pemohonNama) }}",
                                        noKk: "{{ addslashes($noKkPemohon) }}",
                                        alamat: "{{ addslashes($alamatLengkap) }}",
                                        warga: "{{ addslashes($targetNama) }}",
                                        targetTitle: "Target Data: {{ addslashes($targetNama) }} ({{ addslashes($targetHub) }})",
                                        targetSub: "{{ addslashes($targetSub) }}",
                                        tglDiajukan: "Diajukan pada: {{ addslashes($tglDiajukan) }}",
                                        status: "{{ $v->status }}",
                                        statusLabel: "{{ $statusVerif['label'] }}",
                                        alasan: "{{ addslashes($alasanTeks) }}",
                                        jenis: "{{ $v->jenis_pengajuan }}",
                                        jenisLabel: "{{ $jenisLabel['label'] }}",
                                        dataLama: @json($v->data_lama ?? []),
                                        dataBaru: @json($v->data_baru ?? []),
                                        docs: @json($docsListAdmin),
                                        buktiUrl: "{{ $buktiUrl }}",
                                        buktiNama: "{{ addslashes($buktiNama) }}",
                                        hasBukti: {{ $hasBukti ? 'true' : 'false' }}
                                    })'>
                                    <i class='bx bx-show'></i> Review
                                </button>
                                @if($v->status === 'disetujui')
                                    <span class="btn-tindakan-disetujui-label"><i class='bx bx-check-circle'></i> Telah Disetujui</span>
                                @else
                                    <span style="color:#dc3545;font-size:12px;font-weight:600;"><i class='bx bx-x-circle'></i> Ditolak</span>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:36px;color:#6c757d;font-size:13px;">
                            <i class='bx bx-info-circle' style="font-size:28px;display:block;margin-bottom:8px;color:#ced4da;"></i>
                            Tidak ada pengajuan perubahan data kependudukan saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination Verifikasi --}}
        @if(isset($daftarPengajuan) && $daftarPengajuan->hasPages())
        <div class="warga-pagination-bar" style="margin-top:12px;">
            <div>{{ $daftarPengajuan->withQueryString()->links() }}</div>
        </div>
        @endif
    </div>

</div>

{{-- ========================================================
     MODAL POPUP KONFIRMASI HAPUS DATA KARTU KELUARGA
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-hapus-warga" onclick="if(event.target === this) tutupModalHapus()">
    <div class="warga-modal-dialog">
        <form id="form-hapus-kk" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="warga-modal-header warga-modal-header--red">
                <div class="warga-modal-header-left">
                    <div class="warga-modal-icon-circle">
                        <i class='bx bx-trash'></i>
                    </div>
                    <h4 class="warga-modal-title" id="modal-hapus-title">Konfirmasi Hapus Data Kartu Keluarga</h4>
                </div>
                <button type="button" class="warga-modal-close" onclick="tutupModalHapus()">&times;</button>
            </div>
            <div class="warga-modal-body">
                <p class="warga-modal-question" id="modal-hapus-question">
                    Apakah Anda yakin ingin menghapus data kartu keluarga ini?
                </p>
                <div class="warga-modal-alert-box warga-modal-alert-box--danger">
                    <i class='bx bx-info-circle warga-modal-alert-icon'></i>
                    <div class="warga-modal-alert-text">
                        <strong>Perhatian:</strong> Data kependudukan, anggota keluarga, serta riwayat iuran dan permohonan surat terkait akan ikut terhapus dari sistem WargaKu. Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
            </div>
            <div class="warga-modal-footer">
                <button type="button" class="btn-warga-modal-batal" onclick="tutupModalHapus()">Batal</button>
                <button type="submit" class="btn-warga-modal-action-red" id="btn-confirm-hapus">
                    <i class='bx bx-trash'></i> Ya, Hapus Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL POPUP UBAH STATUS KELUARGA
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-edit-status-kk" onclick="if(event.target === this) tutupModalEditStatus()">
    <div class="warga-modal-dialog">
        <form id="form-edit-status-kk" method="POST" action="">
            @csrf
            <div class="warga-modal-header warga-modal-header--green">
                <h4 class="warga-modal-title">Ubah Status Keluarga</h4>
                <button type="button" class="warga-modal-close" onclick="tutupModalEditStatus()">&times;</button>
            </div>
            <div class="warga-modal-body">
                <div class="edit-status-target-card">
                    <span class="edit-status-target-label">KK Tujuan:</span>
                    <span class="edit-status-target-val" id="edit-status-nokk">3275010905120008</span>
                </div>

                <div class="edit-status-grid">
                    <div class="edit-status-field">
                        <label class="edit-status-label" for="edit-input-tinggal">Status Tinggal</label>
                        <select class="edit-status-select" id="edit-input-tinggal" name="status_tinggal">
                            <option value="tetap">Tetap</option>
                            <option value="kontrak">Kontrak / Sewa</option>
                            <option value="kos">Kos</option>
                            <option value="menumpang">Menumpang</option>
                        </select>
                    </div>
                    <div class="edit-status-field">
                        <label class="edit-status-label" for="edit-input-ekonomi">Status Ekonomi</label>
                        <select class="edit-status-select" id="edit-input-ekonomi" name="status_ekonomi">
                            <option value="mampu">Mampu</option>
                            <option value="kurang_mampu">Kurang Mampu</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="warga-modal-footer">
                <button type="button" class="btn-warga-modal-batal" onclick="tutupModalEditStatus()">Batal</button>
                <button type="submit" class="btn-warga-modal-action-green">
                    <i class='bx bx-check-double'></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL KONFIRMASI SETUJUI VERIFIKASI
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-konfirmasi-setujui" onclick="if(event.target===this) tutupKonfirmasiSetujui()">
    <div class="warga-modal-dialog">
        <div class="warga-modal-header warga-modal-header--green">
            <div class="warga-modal-header-left">
                <div class="warga-modal-icon-circle"><i class='bx bx-check-double'></i></div>
                <h4 class="warga-modal-title">Konfirmasi Setujui Pengajuan</h4>
            </div>
            <button type="button" class="warga-modal-close" onclick="tutupKonfirmasiSetujui()">&times;</button>
        </div>
        <div class="warga-modal-body">
            <p class="warga-modal-question" id="modal-setujui-question">
                Apakah Anda yakin ingin menyetujui pengajuan ini?
            </p>
            <div class="warga-modal-alert-box">
                <i class='bx bx-info-circle warga-modal-alert-icon'></i>
                <div class="warga-modal-alert-text">
                    <strong>Perhatian:</strong> Menyetujui pengajuan akan otomatis memperbarui data kependudukan warga di sistem.
                </div>
            </div>
        </div>
        <div class="warga-modal-footer">
            <button type="button" class="btn-warga-modal-batal" onclick="tutupKonfirmasiSetujui()">Batal</button>
            <button type="button" class="btn-warga-modal-action-green" id="btn-eksekusi-setujui"
                onclick="eksekusiSetujuiPengajuan()">
                <i class='bx bx-check-double'></i> Ya, Setujui
            </button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL TOLAK PENGAJUAN VERIFIKASI
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-tolak-pengajuan" onclick="if(event.target===this) tutupModalTolak()">
    <div class="warga-modal-dialog">
        <div class="warga-modal-header warga-modal-header--red">
            <div class="warga-modal-header-left">
                <div class="warga-modal-icon-circle"><i class='bx bx-x-circle'></i></div>
                <h4 class="warga-modal-title">Tolak Pengajuan Verifikasi</h4>
            </div>
            <button type="button" class="warga-modal-close" onclick="tutupModalTolak()">&times;</button>
        </div>
        <div class="warga-modal-body">
            <p class="warga-modal-question" id="modal-tolak-question">
                Tolak pengajuan ini?
            </p>
            <div style="margin-top:12px;">
                <label style="font-size:12px;font-weight:600;color:#495057;display:block;margin-bottom:4px;"
                    for="input-alasan-tolak">Catatan Alasan Penolakan (opsional):</label>
                <textarea id="input-alasan-tolak" name="alasan" rows="3"
                    style="width:100%;padding:8px 10px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;resize:vertical;box-sizing:border-box;"
                    placeholder="Tulis alasan penolakan untuk warga..."></textarea>
            </div>
        </div>
        <div class="warga-modal-footer">
            <button type="button" class="btn-warga-modal-batal" onclick="tutupModalTolak()">Batal</button>
            <button type="button" class="btn-warga-modal-action-red" id="btn-eksekusi-tolak"
                onclick="eksekusiTolakPengajuan()">
                <i class='bx bx-x-circle'></i> Ya, Tolak Pengajuan
            </button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL POPUP REVIEW VERIFIKASI PENGAJUAN (IDENTIK DENGAN SISI WARGA + 2 TOMBOL ADMIN)
     ======================================================== --}}
<div class="kk-modal-overlay" id="modal-review-verifikasi" onclick="tutupReviewViaOverlay(event)">
    <div class="kk-pengajuan-modal" id="modal-admin-pengajuan-container" role="dialog" aria-modal="true" aria-labelledby="review-modal-title">
        <!-- Header -->
        <div class="kk-pengajuan-modal__header">
            <div>
                <div class="kk-pengajuan-modal__title-row">
                    <h2 class="kk-pengajuan-modal__title" id="review-modal-title">Review Verifikasi Pengajuan</h2>
                    <span class="kk-pengajuan-modal__status-badge kk-pengajuan-modal__status-badge--menunggu" id="review-status-badge">Menunggu Persetujuan RT</span>
                </div>
                <div class="kk-pengajuan-modal__subtitle">
                    Pemohon: <strong id="review-header-pemohon">-</strong> &bull; No. KK: <strong id="review-header-nokk">-</strong> &bull; <span id="review-header-alamat">-</span>
                </div>
            </div>
            <button type="button" class="kk-pengajuan-modal__close" onclick="tutupModalReviewVerifikasi()" aria-label="Tutup">
                <i class='bx bx-x'></i>
            </button>
        </div>

        <!-- Body -->
        <div class="kk-pengajuan-modal__body">
            <!-- Strip Target Data -->
            <div class="kk-target-strip">
                <div class="kk-target-strip__left">
                    <div class="kk-target-strip__title" id="review-target-title">Target Data: -</div>
                    <div class="kk-target-strip__sub" id="review-target-sub">-</div>
                </div>
                <div class="kk-target-strip__date" id="review-target-date">
                    Diajukan pada: -
                </div>
            </div>

            <!-- 2-Columns Grid: Komparasi vs Pratinjau Dokumen -->
            <div class="kk-pengajuan-grid">
                <!-- Kolom Kiri: Komparasi Data -->
                <div class="kk-pengajuan-card">
                    <div class="kk-pengajuan-card__header">
                        <div class="kk-pengajuan-card__title">
                            <i class='bx bx-git-compare' style="color:#0284c7; font-size:16px;"></i>
                            <span>Komparasi Data Lama vs Usulan Baru</span>
                        </div>
                        <span class="kk-pengajuan-card__badge-green" id="review-attr-count">0 Atribut Diperbarui</span>
                    </div>

                    <div class="kk-ptable-wrap">
                        <table class="kk-ptable">
                            <thead>
                                <tr>
                                    <th style="width: 32%;">FIELD / ATRIBUT</th>
                                    <th style="width: 34%;">DATA LAMA</th>
                                    <th style="width: 34%;">USULAN BARU WARGA</th>
                                </tr>
                            </thead>
                            <tbody id="review-komparasi-tbody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Alasan Pengajuan -->
                    <div class="kk-alasan-box">
                        <div class="kk-alasan-box__label">Alasan Pengajuan Perubahan:</div>
                        <div class="kk-alasan-box__val" id="review-alasan-val">-</div>
                    </div>
                </div>

                <!-- Kolom Kanan: Pratinjau Dokumen Pendukung -->
                <div class="kk-pengajuan-card">
                    <div class="kk-pengajuan-card__header">
                        <div class="kk-pengajuan-card__title">
                            <i class='bx bx-check-shield' style="color:#16a34a; font-size:16px;"></i>
                            <span>Pratinjau Dokumen Pendukung</span>
                        </div>
                        <span class="kk-pengajuan-card__badge-green" id="review-doc-count">0 Dokumen</span>
                    </div>

                    <!-- Tabs Dokumen -->
                    <div class="kk-doc-tabs" id="review-doc-tabs">
                        <!-- Dynamic tabs -->
                    </div>

                    <!-- Visual Document Box Serupa Warga -->
                    <div class="kk-doc-preview-box">
                        <div class="kk-doc-display-container">
                            <img id="review-doc-img" src="" alt="Pratinjau Dokumen" class="kk-doc-preview-img" onclick="bukaZoomDokumenAdmin()" title="Klik untuk memperbesar berkas" style="display:none;">
                            <div id="review-doc-fallback" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:36px 16px;color:#94a3b8;width:100%;">
                                <i class='bx bxs-file-pdf' id="review-doc-pdf-icon" style="font-size:52px;color:#dc2626;margin-bottom:8px;display:none;"></i>
                                <i class='bx bx-file' id="review-doc-none-icon" style="font-size:52px;color:#cbd5e1;margin-bottom:8px;"></i>
                                <span id="review-doc-fallback-text" style="font-size:13px;font-weight:600;">Tidak ada berkas yang diunggah</span>
                            </div>
                        </div>

                        <div class="kk-doc-info-bar">
                            <span id="review-doc-filename" style="font-size: 12px; color: #475569; font-weight: 600;">Dokumen</span>
                            <span id="review-doc-verified" style="font-size: 11.5px; color: #16a34a; font-weight: 600;">
                                <i class='bx bx-check-circle'></i> Dokumen Terlampir
                            </span>
                        </div>

                        <!-- Action buttons for document -->
                        <div class="kk-doc-actions">
                            <button type="button" class="kk-doc-btn" onclick="bukaZoomDokumenAdmin()" id="btn-admin-perbesar-berkas">
                                <i class='bx bx-zoom-in'></i> Perbesar Berkas
                            </button>
                            <a id="btn-admin-unduh-berkas" href="#" download class="kk-doc-btn">
                                <i class='bx bx-download'></i> Unduh File Asli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer: Sisi Admin dengan Tombol Sejajar Rapi 1 Baris -->
        <div class="kk-pengajuan-modal__footer" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:nowrap; gap:16px; padding:14px 26px;">
            <button type="button" class="kk-pbtn-close" onclick="tutupModalReviewVerifikasi()" style="height:38px; display:inline-flex; align-items:center; justify-content:center; padding:0 18px; border-radius:6px; font-size:13px; font-weight:600; background:#ffffff; border:1px solid #cbd5e1; color:#475569; cursor:pointer; flex-shrink:0;">
                Tutup / Batal
            </button>
            <div class="kk-pengajuan-modal__footer-status" id="review-footer-status" style="flex:1; min-width:0; display:flex; align-items:center; gap:6px; font-size:12.5px; color:#64748b; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <i class='bx bx-time-five' style="color:#d97706; font-size:16px; flex-shrink:0;"></i>
                <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">Pengajuan ini sedang dalam antrean verifikasi oleh Pengurus RT 04.</span>
            </div>
            <div class="kk-pengajuan-modal__footer-btns" id="review-admin-actions" style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                <button type="button" class="btn-warga-modal-action-red" onclick="alihkanKeTolakDariReview()" style="height:38px; display:inline-flex; align-items:center; gap:6px; padding:0 18px; border-radius:6px; font-size:13px; font-weight:600; background:#dc2626; color:#ffffff; border:none; cursor:pointer; box-shadow:none;">
                    <i class='bx bx-x' style="font-size:16px;"></i> Tolak Pengajuan
                </button>
                <button type="button" class="btn-warga-modal-action-green" onclick="alihkanKeSetujuiDariReview()" style="height:38px; display:inline-flex; align-items:center; gap:6px; padding:0 18px; border-radius:6px; font-size:13px; font-weight:600; background:#16a34a; color:#ffffff; border:none; cursor:pointer; box-shadow:none;">
                    <i class='bx bx-check-double' style="font-size:16px;"></i> Setujui &amp; Perbarui Database
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tab switching antara Daftar KK dan Verifikasi
    function switchWargaTab(tabName) {
        var btnKk = document.getElementById('tab-btn-kk');
        var btnVerif = document.getElementById('tab-btn-verifikasi');
        var panelKk = document.getElementById('panel-tab-kk');
        var panelVerif = document.getElementById('panel-tab-verifikasi');

        if (tabName === 'kk') {
            btnKk.classList.add('warga-tab-btn--active');
            btnVerif.classList.remove('warga-tab-btn--active');
            panelKk.style.display = 'block';
            panelVerif.style.display = 'none';
        } else {
            btnVerif.classList.add('warga-tab-btn--active');
            btnKk.classList.remove('warga-tab-btn--active');
            panelVerif.style.display = 'block';
            panelKk.style.display = 'none';
        }
    }

    // ── Pagination KK ──
    const KK_PAGE_SIZE = 10;
    let currentKkPage = 1;

    function renderKkPagination() {
        var q = (document.getElementById('search-kk').value || '').toLowerCase().trim();
        var tinggal = (document.getElementById('filter-tinggal').value || '').toLowerCase().trim();
        var ekonomi = (document.getElementById('filter-ekonomi').value || '').toLowerCase().trim();

        var allRows = Array.from(document.querySelectorAll('#table-kk tbody tr.row-kk-item'));

        var matchedRows = allRows.filter(function(row) {
            var nokk = (row.dataset.nokk || '').toLowerCase();
            var kepala = (row.dataset.kepala || '').toLowerCase();
            var alamat = (row.dataset.alamat || '').toLowerCase();
            var rowTinggal = (row.dataset.tinggal || '').toLowerCase();
            var rowEkonomi = (row.dataset.ekonomi || '').toLowerCase();

            var matchSearch = !q || nokk.includes(q) || kepala.includes(q) || alamat.includes(q);
            var matchTinggal = !tinggal || rowTinggal.includes(tinggal);
            var matchEkonomi = !ekonomi || rowEkonomi.includes(ekonomi);

            return matchSearch && matchTinggal && matchEkonomi;
        });

        var totalItems = matchedRows.length;
        var totalPages = Math.max(1, Math.ceil(totalItems / KK_PAGE_SIZE));

        if (currentKkPage > totalPages) currentKkPage = totalPages;
        if (currentKkPage < 1) currentKkPage = 1;

        allRows.forEach(function(row) { row.style.display = 'none'; });

        var startIndex = (currentKkPage - 1) * KK_PAGE_SIZE;
        var endIndex = Math.min(startIndex + KK_PAGE_SIZE, totalItems);

        for (var i = startIndex; i < endIndex; i++) {
            matchedRows[i].style.display = '';
        }

        var infoEl = document.getElementById('info-kk-count');
        if (infoEl) {
            if (totalItems === 0) {
                infoEl.innerHTML = 'Tidak ada Kartu Keluarga yang cocok dengan filter';
            } else {
                infoEl.innerHTML = 'Menampilkan <strong>' + (startIndex + 1) + ' - ' + endIndex + '</strong> dari <strong>' + totalItems + '</strong> Kepala Keluarga (Kartu Keluarga Aktif RT 04)';
            }
        }

        var container = document.getElementById('pagination-kk');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1 && totalItems <= KK_PAGE_SIZE) {
            var bPrev = createWargaPageBtn('« Sebelumnya', true, false, function(){});
            var b1 = createWargaPageBtn('1', false, true, function(){});
            var bNext = createWargaPageBtn('Berikutnya »', true, false, function(){});
            container.appendChild(bPrev);
            container.appendChild(b1);
            container.appendChild(bNext);
            return;
        }

        // Prev
        var prevDisabled = (currentKkPage <= 1);
        var btnPrev = createWargaPageBtn('« Sebelumnya', prevDisabled, false, function() {
            if (currentKkPage > 1) { currentKkPage--; renderKkPagination(); }
        });
        container.appendChild(btnPrev);

        // Pages
        for (var p = 1; p <= totalPages; p++) {
            (function(pageNum) {
                var isActive = (pageNum === currentKkPage);
                var btn = createWargaPageBtn(pageNum, false, isActive, function() {
                    currentKkPage = pageNum;
                    renderKkPagination();
                });
                container.appendChild(btn);
            })(p);
        }

        // Next
        var nextDisabled = (currentKkPage >= totalPages);
        var btnNext = createWargaPageBtn('Berikutnya »', nextDisabled, false, function() {
            if (currentKkPage < totalPages) { currentKkPage++; renderKkPagination(); }
        });
        container.appendChild(btnNext);
    }

    function createWargaPageBtn(label, isDisabled, isActive, onClickHandler) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'warga-page-btn' + (isActive ? ' warga-page-btn--active' : '') + (isDisabled ? ' warga-page-btn--disabled' : '');
        btn.innerHTML = label;
        btn.disabled = !!isDisabled;
        if (!isDisabled && !isActive) { btn.onclick = onClickHandler; }
        return btn;
    }

    function terapkanFilterKK() { currentKkPage = 1; renderKkPagination(); }

    function resetFilterKK() {
        document.getElementById('search-kk').value = '';
        document.getElementById('filter-tinggal').value = '';
        document.getElementById('filter-ekonomi').value = '';
        terapkanFilterKK();
    }

    document.getElementById('search-kk').addEventListener('input', terapkanFilterKK);
    document.getElementById('filter-tinggal').addEventListener('change', terapkanFilterKK);
    document.getElementById('filter-ekonomi').addEventListener('change', terapkanFilterKK);

    document.addEventListener('DOMContentLoaded', renderKkPagination);
    renderKkPagination();

    // Cek parameter URL untuk tab aktif
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'verifikasi' || window.location.hash === '#verifikasi') {
        switchWargaTab('verifikasi');
    }

    // ── Modal Hapus Data Kartu Keluarga ──
    function konfirmasiHapusKK(noKk, namaKepala) {
        var form = document.getElementById('form-hapus-kk');
        if (form) form.action = "{{ url('/admin/data-warga') }}/" + noKk;
        document.getElementById('modal-hapus-title').textContent = 'Konfirmasi Hapus Data Kartu Keluarga';
        document.getElementById('modal-hapus-question').innerHTML = 'Apakah Anda yakin ingin menghapus data kartu keluarga atas nama <strong>' + namaKepala + '</strong> (No. KK: ' + noKk + ')?';
        document.getElementById('modal-hapus-warga').style.display = 'flex';
    }

    function tutupModalHapus() {
        document.getElementById('modal-hapus-warga').style.display = 'none';
    }

    // ── Modal Ubah Status Keluarga ──
    function bukaModalEditStatus(noKk, namaKepala, statusTinggal, statusEkonomi) {
        document.getElementById('edit-status-nokk').textContent = noKk;
        var form = document.getElementById('form-edit-status-kk');
        if (form) form.action = "{{ url('/admin/data-warga') }}/" + noKk + "/status";

        var selectTinggal = document.getElementById('edit-input-tinggal');
        var selectEkonomi = document.getElementById('edit-input-ekonomi');

        if (selectTinggal) {
            var stLower = (statusTinggal || '').toLowerCase();
            if (stLower.includes('kontrak') || stLower.includes('sewa')) selectTinggal.value = 'kontrak';
            else if (stLower.includes('kos')) selectTinggal.value = 'kos';
            else if (stLower.includes('menumpang') || stLower.includes('tumpang')) selectTinggal.value = 'menumpang';
            else selectTinggal.value = 'tetap';
        }
        if (selectEkonomi) {
            selectEkonomi.value = (statusEkonomi && statusEkonomi.toLowerCase().includes('kurang')) ? 'kurang_mampu' : 'mampu';
        }

        document.getElementById('modal-edit-status-kk').style.display = 'flex';
    }

    function tutupModalEditStatus() {
        document.getElementById('modal-edit-status-kk').style.display = 'none';
    }

    // ── Modal Konfirmasi Setujui Verifikasi ──
    var activeSetujuiId = null;

    function konfirmasiSetujuiPengajuan(id, pemohon) {
        activeSetujuiId = id;
        document.getElementById('modal-setujui-question').innerHTML =
            'Apakah Anda yakin ingin menyetujui pengajuan dari <strong>' + pemohon + '</strong>? Data kependudukan warga akan diperbarui secara otomatis.';
        document.getElementById('modal-konfirmasi-setujui').style.display = 'flex';
    }

    function tutupKonfirmasiSetujui() {
        document.getElementById('modal-konfirmasi-setujui').style.display = 'none';
        activeSetujuiId = null;
    }

    function eksekusiSetujuiPengajuan() {
        if (!activeSetujuiId) return;
        var form = document.getElementById('form-setujui-' + activeSetujuiId);
        if (form) {
            tutupKonfirmasiSetujui();
            form.submit();
        }
    }

    // ── Modal Tolak Pengajuan Verifikasi ──
    var activeTolakId = null;

    function bukaModalTolakPengajuan(id, pemohon) {
        activeTolakId = id;
        document.getElementById('modal-tolak-question').innerHTML =
            'Tolak pengajuan dari <strong>' + pemohon + '</strong>? Silakan isi catatan alasan penolakan di bawah.';
        document.getElementById('input-alasan-tolak').value = '';
        document.getElementById('modal-tolak-pengajuan').style.display = 'flex';
    }

    function tutupModalTolak() {
        document.getElementById('modal-tolak-pengajuan').style.display = 'none';
        activeTolakId = null;
    }

    function eksekusiTolakPengajuan() {
        if (!activeTolakId) return;
        var alasan = (document.getElementById('input-alasan-tolak').value || '').trim();
        var hiddenAlasan = document.getElementById('alasan-tolak-' + activeTolakId);
        if (hiddenAlasan && alasan) hiddenAlasan.value = alasan;
        var form = document.getElementById('form-tolak-' + activeTolakId);
        if (form) {
            tutupModalTolak();
            form.submit();
        }
    }

    // ── Modal Review Verifikasi Pengajuan (Identik Sisi Warga + 2 Tombol Admin) ──
    var activeReviewItem = null;

    var mapPendidikanReview = {
        'tidak_sekolah': 'Tidak / Belum Sekolah',
        'sd': 'SD / Sederajat',
        'smp': 'SMP / Sederajat',
        'sma': 'SMA / SMK / Sederajat',
        'diploma': 'Diploma (D1 - D3)',
        'd4_s1': 'Diploma IV / Strata 1 (S1)',
        's2': 'Strata 2 (S2)',
        's3': 'Strata 3 (S3)'
    };

    var fieldLabelsReview = {
        'nik': 'NIK',
        'nama': 'Nama Lengkap',
        'tempat_lahir': 'Tempat Lahir',
        'tanggal_lahir': 'Tanggal Lahir',
        'jenis_kelamin': 'Jenis Kelamin',
        'agama': 'Agama',
        'pendidikan': 'Pendidikan Terakhir',
        'pendidikan_terakhir': 'Pendidikan Terakhir',
        'pekerjaan': 'Pekerjaan Utama',
        'alamat': 'Alamat Domisili',
        'hubungan_keluarga': 'Hubungan Keluarga',
        'status_hubungan': 'Hubungan Keluarga',
        'status_warga': 'Status Warga',
        'golongan_darah': 'Golongan Darah',
        'status_perkawinan': 'Status Perkawinan'
    };

    function bersihkanDanFormatVal(k, val) {
        if (val === undefined || val === null || val === '') return '—';
        if (typeof val === 'string') {
            // Bersihkan format ISO datetime jika ada (misal 1992-09-08T00:00:00.000000Z -> 1992-09-08)
            if (/^\d{4}-\d{2}-\d{2}T/.test(val)) {
                val = val.substring(0, 10);
            }
            if (k === 'jenis_kelamin') {
                return val === 'laki_laki' ? 'Laki-Laki' : (val === 'perempuan' ? 'Perempuan' : val);
            }
            if (k === 'status_warga') {
                return val === 'hidup' ? 'Hidup (Aktif)' : (val === 'meninggal' ? 'Meninggal' : val);
            }
            if (k === 'pendidikan' || k === 'pendidikan_terakhir') {
                return mapPendidikanReview[val] || val;
            }
            if (k === 'hubungan_keluarga' || k === 'status_hubungan') {
                return val.replace(/_/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
            }
            if (k === 'agama') {
                return val.replace(/\b\w/g, function(l){ return l.toUpperCase(); });
            }
        }
        return val;
    }

    function bukaModalReviewVerifikasi(data) {
        activeReviewItem = data;

        // Header
        var badgeStatusEl = document.getElementById('review-status-badge');
        if (badgeStatusEl) {
            badgeStatusEl.className = 'kk-pengajuan-modal__status-badge kk-pengajuan-modal__status-badge--' + data.status;
            badgeStatusEl.textContent = data.statusLabel || (data.status === 'menunggu' ? 'Menunggu Persetujuan RT' : (data.status === 'disetujui' ? 'Telah Disetujui RT' : 'Ditolak oleh RT'));
        }

        document.getElementById('review-header-pemohon').textContent = data.pemohon || '-';
        document.getElementById('review-header-nokk').textContent = data.noKk || '-';
        document.getElementById('review-header-alamat').textContent = data.alamat || 'RT 04 / RW 08';

        // Strip Target Data
        document.getElementById('review-target-title').textContent = data.targetTitle || ('Target Data: ' + data.warga);
        document.getElementById('review-target-sub').textContent = data.targetSub || ('NIK: ' + data.noKk);
        document.getElementById('review-target-date').textContent = data.tglDiajukan || 'Diajukan pada: -';

        // Komparasi Table
        var tbody = document.getElementById('review-komparasi-tbody');
        tbody.innerHTML = '';

        var dataBaru = data.dataBaru || {};
        var dataLama = data.dataLama || {};

        // Urutan standar field yang biasa ditampilkan
        var orderedKeys = [
            'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'agama', 'pendidikan_terakhir', 'pendidikan', 'pekerjaan', 'alamat',
            'hubungan_keluarga', 'status_hubungan', 'status_warga'
        ];

        var rawKeys = Array.from(new Set(Object.keys(dataBaru).concat(Object.keys(dataLama))));
        var ignoredKeys = ['id', 'warga_id', 'keluarga_id', 'created_at', 'updated_at', 'deleted_at'];
        rawKeys = rawKeys.filter(function(k) { return !ignoredKeys.includes(k); });

        // Urutkan key berdasarkan orderedKeys terlebih dahulu
        var sortedKeys = [];
        orderedKeys.forEach(function(ok) {
            if (rawKeys.includes(ok)) {
                sortedKeys.push(ok);
            }
        });
        rawKeys.forEach(function(rk) {
            if (!sortedKeys.includes(rk)) {
                sortedKeys.push(rk);
            }
        });

        var changedRows = [];

        sortedKeys.forEach(function(k) {
            var rawOld = dataLama[k];
            var rawNew = dataBaru[k];

            var valLama = bersihkanDanFormatVal(k, rawOld);
            var valBaru = bersihkanDanFormatVal(k, rawNew);

            // Cek apakah berbeda
            var isChanged = false;
            if (rawNew !== undefined && rawNew !== null && rawNew !== '') {
                if (rawOld === undefined || rawOld === null || rawOld === '' || rawOld === '—') {
                    isChanged = true;
                } else if (String(valLama).trim().toLowerCase() !== String(valBaru).trim().toLowerCase()) {
                    isChanged = true;
                }
            }

            // HANYA MASUKKAN JIKA BERUBAH ATAU JIKA PENGAJUAN TAMBAH ANGGOTA
            if (isChanged || data.jenis === 'tambah_anggota') {
                changedRows.push({
                    key: k,
                    field: fieldLabelsReview[k] || k.replace(/_/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); }),
                    old: valLama,
                    new: valBaru,
                    isChanged: isChanged
                });
            }
        });

        if (changedRows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" style="padding:18px;text-align:center;color:#64748b;font-style:italic;">Tidak ada atribut yang mengalami perubahan.</td></tr>';
            document.getElementById('review-attr-count').textContent = '0 Atribut Diperbarui';
        } else {
            changedRows.forEach(function(item) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td class="kk-ptable-field">' + item.field + '</td>' +
                    '<td class="kk-ptable-old">' +
                        '<span class="kk-mobile-row-label">Data Lama:</span>' +
                        '<span>' + item.old + '</span>' +
                    '</td>' +
                    '<td class="kk-ptable-new">' +
                        '<span class="kk-mobile-row-label">Usulan Baru:</span>' +
                        (item.isChanged ?
                            ('<div class="kk-val-new"><span>' + item.new + '</span><span class="kk-badge-diubah">Diubah</span></div>') :
                            ('<span class="kk-ptable-old">' + item.new + '</span>')
                        ) +
                    '</td>';
                tbody.appendChild(tr);
            });
            document.getElementById('review-attr-count').textContent = changedRows.length + ' Atribut Diperbarui';
        }

        // Alasan pengajuan
        document.getElementById('review-alasan-val').textContent = data.alasan || 'Pengajuan perubahan data kependudukan oleh warga.';

        // Dokumen & Tabs (Mendukung Multi Dokumen)
        currentAdminDocs = (data.docs && data.docs.length > 0) ? data.docs : [];
        if (currentAdminDocs.length === 0 && data.hasBukti && data.buktiUrl) {
            currentAdminDocs = [{
                label: '1. ' + (data.buktiNama || 'Berkas Bukti'),
                filename: data.buktiNama || 'Berkas Bukti',
                url: data.buktiUrl,
                isPdf: /\.(pdf)$/i.test(data.buktiUrl)
            }];
        }

        var tabsContainer = document.getElementById('review-doc-tabs');
        var docCountBadge = document.getElementById('review-doc-count');

        if (currentAdminDocs.length > 0) {
            docCountBadge.textContent = currentAdminDocs.length + ' Dokumen';
            var tabsHtml = '';
            currentAdminDocs.forEach(function(doc, idx) {
                var activeClass = idx === 0 ? 'kk-doc-tab-btn--active' : '';
                var icon = doc.isPdf ? 'bxs-file-pdf' : 'bxs-file-png';
                var color = doc.isPdf ? '#dc2626' : '#0284c7';
                tabsHtml += '<button type="button" class="kk-doc-tab-btn ' + activeClass + '" onclick="switchAdminDocPreview(' + idx + ')">' +
                    "<i class='bx " + icon + "' style='color:" + color + ";'></i> " + doc.label +
                '</button>';
            });
            tabsContainer.innerHTML = tabsHtml;
            switchAdminDocPreview(0);
        } else {
            docCountBadge.textContent = '0 Dokumen';
            tabsContainer.innerHTML = '';
            var imgEl = document.getElementById('review-doc-img');
            var fallbackEl = document.getElementById('review-doc-fallback');
            var pdfIcon = document.getElementById('review-doc-pdf-icon');
            var noneIcon = document.getElementById('review-doc-none-icon');
            var fallbackText = document.getElementById('review-doc-fallback-text');
            var filenameEl = document.getElementById('review-doc-filename');
            var verifiedEl = document.getElementById('review-doc-verified');
            var btnPerbesar = document.getElementById('btn-admin-perbesar-berkas');
            var btnUnduh = document.getElementById('btn-admin-unduh-berkas');

            imgEl.style.display = 'none';
            fallbackEl.style.display = 'flex';
            pdfIcon.style.display = 'none';
            noneIcon.style.display = 'block';
            fallbackText.textContent = 'Tidak ada berkas bukti yang diunggah.';
            filenameEl.textContent = 'Tidak ada dokumen';
            verifiedEl.style.color = '#dc2626';
            verifiedEl.innerHTML = "<i class='bx bx-error-circle'></i> Belum Terlampir";
            btnPerbesar.style.display = 'none';
            btnUnduh.style.display = 'none';
        }

        // Footer status text & action buttons
        var footerStatusEl = document.getElementById('review-footer-status');
        var adminActionsEl = document.getElementById('review-admin-actions');

        if (data.status === 'disetujui') {
            footerStatusEl.innerHTML = "<i class='bx bx-check-circle' style='color:#16a34a; font-size:16px;'></i> <span>Pengajuan ini telah disetujui. Database warga telah resmi diperbarui.</span>";
            adminActionsEl.style.display = 'none';
        } else if (data.status === 'ditolak') {
            footerStatusEl.innerHTML = "<i class='bx bx-error-circle' style='color:#dc2626; font-size:16px;'></i> <span>Pengajuan ini telah ditolak oleh Pengurus RT.</span>";
            adminActionsEl.style.display = 'none';
        } else {
            footerStatusEl.innerHTML = "<i class='bx bx-time-five' style='color:#d97706; font-size:16px;'></i> <span>Pengajuan ini sedang dalam antrean verifikasi oleh Pengurus RT 04. Mohon periksa komparasi data di atas.</span>";
            adminActionsEl.style.display = 'flex';
        }

        // Tampilkan modal
        var modalEl = document.getElementById('modal-review-verifikasi');
        modalEl.classList.add('kk-modal-overlay--open');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalReviewVerifikasi() {
        var modalEl = document.getElementById('modal-review-verifikasi');
        if (modalEl) modalEl.classList.remove('kk-modal-overlay--open');
        document.body.style.overflow = '';
        activeReviewItem = null;
    }

    function tutupReviewViaOverlay(e) {
        if (e.target.id === 'modal-review-verifikasi') {
            tutupModalReviewVerifikasi();
        }
    }

    function switchAdminDocPreview(idx) {
        if (!currentAdminDocs || !currentAdminDocs[idx]) return;
        var doc = currentAdminDocs[idx];

        var tabBtns = document.querySelectorAll('#review-doc-tabs .kk-doc-tab-btn');
        tabBtns.forEach(function(btn, bIdx) {
            btn.classList.toggle('kk-doc-tab-btn--active', bIdx === idx);
        });

        var imgEl = document.getElementById('review-doc-img');
        var fallbackEl = document.getElementById('review-doc-fallback');
        var pdfIcon = document.getElementById('review-doc-pdf-icon');
        var noneIcon = document.getElementById('review-doc-none-icon');
        var fallbackText = document.getElementById('review-doc-fallback-text');
        var filenameEl = document.getElementById('review-doc-filename');
        var verifiedEl = document.getElementById('review-doc-verified');
        var btnPerbesar = document.getElementById('btn-admin-perbesar-berkas');
        var btnUnduh = document.getElementById('btn-admin-unduh-berkas');

        filenameEl.textContent = doc.filename || doc.label;
        verifiedEl.style.color = '#16a34a';
        verifiedEl.innerHTML = "<i class='bx bx-check-circle'></i> Dokumen Terlampir";

        btnPerbesar.style.display = 'inline-flex';
        btnUnduh.style.display = 'inline-flex';
        btnUnduh.href = doc.url;
        btnUnduh.setAttribute('download', doc.filename || 'berkas');

        if (activeReviewItem) {
            activeReviewItem.currentDocUrl = doc.url;
        }

        var isImg = /\.(jpg|jpeg|png|webp|gif)$/i.test(doc.url) && !doc.isPdf;
        if (isImg) {
            imgEl.src = doc.url;
            imgEl.style.display = 'block';
            fallbackEl.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            fallbackEl.style.display = 'flex';
            pdfIcon.style.display = 'block';
            noneIcon.style.display = 'none';
            fallbackText.textContent = doc.filename || 'Dokumen Terlampir (PDF/Berkas)';
        }
    }

    function bukaZoomDokumenAdmin() {
        if (activeReviewItem && (activeReviewItem.currentDocUrl || activeReviewItem.buktiUrl)) {
            window.open(activeReviewItem.currentDocUrl || activeReviewItem.buktiUrl, '_blank');
        }
    }

    function alihkanKeSetujuiDariReview() {
        if (!activeReviewItem) return;
        var item = activeReviewItem;
        tutupModalReviewVerifikasi();
        konfirmasiSetujuiPengajuan(item.id, item.pemohon);
    }

    function alihkanKeTolakDariReview() {
        if (!activeReviewItem) return;
        var item = activeReviewItem;
        tutupModalReviewVerifikasi();
        bukaModalTolakPengajuan(item.id, item.pemohon);
    }

    // Keyboard ESC listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalHapus();
            tutupModalEditStatus();
            tutupKonfirmasiSetujui();
            tutupModalTolak();
            tutupModalReviewVerifikasi();
        }
    });
</script>
@endpush