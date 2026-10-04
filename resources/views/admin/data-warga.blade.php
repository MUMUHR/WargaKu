@extends('layouts.admin')

@section('title', 'Kelola Data Warga & Verifikasi Kependudukan')
@section('meta_description', 'Kelola data master kependudukan warga RT 04, status mutasi, dan validasi permohonan perubahan data dari warga.')

@section('breadcrumb')
    <span class="admin-topbar__breadcrumb-current" id="topbar-breadcrumb-label">Kelola Data Warga</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-warga.css') }}">
@endpush

@section('content')

@php
    use App\Data\DummyData;
    $listKk = DummyData::listKartuKeluarga();
    $listVerifikasi = DummyData::listVerifikasiPengajuan();
    $anggotaKk = DummyData::anggotaKeluarga('3275010905120008');
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

{{-- ========================================================
     HEADER HALAMAN (DINAMIS SESUAI VIEW)
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <nav style="font-size:12px;color:#6c757d;margin-bottom:4px;display:flex;align-items:center;gap:4px;" id="header-breadcrumb">
            <i class='bx bx-home-alt'></i> <span>Portal RT 04</span>
        </nav>
        <h1 class="admin-page-header__title" id="page-title">Kelola Data Warga & Verifikasi Kependudukan</h1>
        <p class="admin-page-header__sub" id="page-subtitle">Kelola data master kependudukan warga RT 04, status mutasi, dan validasi permohonan perubahan data dari warga.</p>
    </div>
    <div id="header-actions">
        <button type="button" class="btn-cetak-induk" id="btn-cetak" onclick="window.print()">
            <i class='bx bx-printer'></i> Cetak Buku Induk RT
        </button>
        <button type="button" class="btn-kembali-kk" onclick="tutupDetailKK()" id="btn-kembali-kk" style="display:none;">
            <i class='bx bx-arrow-back'></i> Kembali ke Daftar KK
        </button>
        <button type="button" class="btn-kembali-kk" onclick="kembaliKeDetailKK()" id="btn-kembali-ke-detail" style="display:none;">
            <i class='bx bx-arrow-back'></i> Kembali ke Detail KK
        </button>
    </div>
</div>

{{-- ========================================================
     TOP STAT CARDS: VIEW UTAMA (3 BOXES)
     ======================================================== --}}
<div class="warga-stats-grid" id="stats-main">
    <div class="warga-stat-box warga-stat-box--cyan">
        <div class="warga-stat-box__val">78 <small>KK</small></div>
        <div class="warga-stat-box__label">Total Kartu Keluarga Aktif</div>
        <i class='bx bx-home-alt warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--green">
        <div class="warga-stat-box__val">312 <small>Jiwa</small></div>
        <div class="warga-stat-box__label">Warga Terdaftar (Hidup)</div>
        <div class="warga-stat-box__sub">158 Laki-laki • 154 Perempuan</div>
        <i class='bx bx-group warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--amber">
        <div class="warga-stat-box__val">5 <small>Pengajuan</small></div>
        <div class="warga-stat-box__label">Menunggu Verifikasi RT</div>
        <i class='bx bx-clipboard warga-stat-box__icon'></i>
    </div>
</div>

{{-- ========================================================
     TOP STAT CARDS: VIEW DETAIL KK (3 BOXES)
     ======================================================== --}}
<div class="warga-stats-grid" id="stats-detail" style="display:none;">
    <div class="warga-stat-box warga-stat-box--blue">
        <div class="warga-stat-box__label">NOMOR KARTU KELUARGA</div>
        <div class="warga-stat-box__val" id="detail-stat-no-kk" style="font-size:22px;margin-top:6px;">3275010905120008</div>
        <i class='bx bx-id-card warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--green">
        <div class="warga-stat-box__label">TOTAL JIWA TERDAFTAR</div>
        <div class="warga-stat-box__val" id="detail-stat-jiwa" style="font-size:24px;margin-top:4px;">3 <small>Jiwa Aktif</small></div>
        <div class="warga-stat-box__sub" id="detail-stat-gender">1 Laki-laki • 2 Perempuan</div>
        <i class='bx bx-group warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--cyan">
        <div class="warga-stat-box__label">STATUS HUNIAN</div>
        <div class="warga-stat-box__val" id="detail-stat-hunian" style="font-size:22px;margin-top:6px;">Tetap (Milik Sendiri)</div>
        <i class='bx bx-building-house warga-stat-box__icon'></i>
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
                @if(count($listVerifikasi) > 0)
                    <span class="badge-tab-dot" id="badge-tab-dot" title="{{ count($listVerifikasi) }} Pengajuan Menunggu"></span>
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
                </select>
            </div>
            <div class="warga-filter-col warga-filter-col--select">
                <label class="warga-filter-label" for="filter-ekonomi">Status Ekonomi:</label>
                <select id="filter-ekonomi" class="warga-filter-input">
                    <option value="">Semua Ekonomi</option>
                    <option value="mampu">Mampu</option>
                    <option value="kurang">Kurang Mampu</option>
                </select>
            </div>
            <div class="warga-filter-col warga-filter-col--actions">
                <button type="button" class="btn-filter-submit" onclick="terapkanFilterKK()">
                    <i class='bx bx-filter-alt'></i> Filter
                </button>
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
                            <a href="javascript:void(0)" class="link-kk"
                               onclick="bukaDetailKK('{{ $k['no_kk'] }}', '{{ addslashes($k['nama_kepala']) }}', '{{ $k['nik_kepala'] }}', '{{ addslashes($k['alamat']) }}', '{{ $k['kontak'] }}', '{{ $k['ekonomi_kategori'] }}', '{{ $k['status_tinggal'] }}', {{ $k['jumlah_anggota'] }}, {{ $k['laki_laki'] }}, {{ $k['perempuan'] }})">
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
                                <button type="button" class="btn-act-detail"
                                        onclick="bukaDetailKK('{{ $k['no_kk'] }}', '{{ addslashes($k['nama_kepala']) }}', '{{ $k['nik_kepala'] }}', '{{ addslashes($k['alamat']) }}', '{{ $k['kontak'] }}', '{{ $k['ekonomi_kategori'] }}', '{{ $k['status_tinggal'] }}', {{ $k['jumlah_anggota'] }}, {{ $k['laki_laki'] }}, {{ $k['perempuan'] }})"
                                        title="Lihat Detail Kartu Keluarga & Anggota">
                                    <i class='bx bx-id-card'></i> Detail KK
                                </button>
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
            <div id="info-kk-count">Menampilkan <strong>1 - 5</strong> dari <strong>10</strong> Kepala Keluarga (Kartu Keluarga Aktif RT 04)</div>
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
                    <col style="width: 12%;">
                    <col style="width: 14%;">
                    <col style="width: 18%;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="padding-left: 18px;">PEMOHON / KEPALA KK</th>
                        <th>WAKTU</th>
                        <th>AKSI WARGA</th>
                        <th>WARGA TERDAMPAK</th>
                        <th>BUKTI LAMPIRAN</th>
                        <th>STATUS VALIDASI</th>
                        <th style="padding-right: 18px;">TINDAKAN PETUGAS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listVerifikasi as $v)
                    <tr id="row-verif-{{ $v['id'] }}">
                        <td style="padding-left: 18px;">
                            <div class="nama-kepala-text">{{ $v['pemohon'] }}</div>
                            <div class="alamat-sub-text">KK : {{ $v['no_kk'] }}</div>
                        </td>
                        <td style="color: #495057; font-size: 12px; white-space: nowrap;">
                            {{ $v['waktu'] }}
                        </td>
                        <td>
                            @if($v['aksi_badge'] === 'info')
                                <span class="badge-aksi-ubah">{{ $v['aksi_warga'] }}</span>
                            @elseif($v['aksi_badge'] === 'success')
                                <span class="badge-aksi-tambah">{{ $v['aksi_warga'] }}</span>
                            @else
                                <span class="badge-aksi-pindah">{{ $v['aksi_warga'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #212529; font-size: 13px;">{{ $v['warga_terdampak'] }}</div>
                        </td>
                        <td>
                            <span class="badge-lampiran-file">
                                <i class='{{ $v['bukti_icon'] ?? 'bx bx-file' }}' style="color: {{ $v['bukti_color'] ?? '#dc3545' }}; font-size: 14px;"></i>
                                <span>{{ $v['bukti'] }}</span>
                            </span>
                        </td>
                        <td id="status-cell-verif-{{ $v['id'] }}">
                            <span class="badge-status-menunggu">
                                <i class='bx bx-hourglass'></i>
                                <span>{{ $v['status_validasi'] }}</span>
                            </span>
                        </td>
                        <td style="padding-right: 18px;">
                            <div class="warga-row-actions" id="row-actions-verif-{{ $v['id'] }}">
                                <button type="button" class="btn-tindakan-setujui" onclick="bukaModalKonfirmasiSetujui('{{ $v['id'] }}', '{{ addslashes($v['pemohon']) }}', this)">
                                    <i class='bx bx-check'></i> Setujui
                                </button>
                                <button type="button" class="btn-tindakan-review" onclick="bukaModalReviewVerifikasi('{{ $v['id'] }}')">
                                    <i class='bx bx-show'></i> Review Perubahan
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ========================================================
     CONTAINER VIEW DETAIL KK (SCREENSHOT 2)
     ======================================================== --}}
<div class="detail-kk-wrapper" id="view-detail-kk" style="display:none;">

    {{-- Card 1: Profil Keluarga & Domisili Rumah --}}
    <div class="detail-section-card">
        <div class="detail-section-head" onclick="toggleCollapse('detail-profil-content', 'chevron-profil')">
            <h3 class="detail-section-title">
                <i class='bx bx-home'></i>
                Profil Keluarga & Domisili Rumah
            </h3>
            <i class='bx bx-chevron-up' id="chevron-profil" style="font-size:20px;color:#6c757d;"></i>
        </div>
        <div id="detail-profil-content">
            <div class="detail-profil-grid">
                <div>
                    <div class="detail-profil-item__label">KEPALA KELUARGA</div>
                    <div class="detail-profil-item__value" id="detail-kepala-nama">Bambang Santoso, S.T.</div>
                    <div class="detail-profil-item__sub">NIK : <span id="detail-kepala-nik">3275012304800001</span></div>
                </div>
                <div>
                    <div class="detail-profil-item__label">ALAMAT RUMAH LENGKAP</div>
                    <div class="detail-profil-item__value" id="detail-alamat">Blok B4 No. 12, RT 04 / RW 08</div>
                </div>
                <div>
                    <div class="detail-profil-item__label">KONTAK TELEPON / WHATSAPP</div>
                    <div class="detail-profil-item__value" style="display:flex;align-items:center;gap:6px;">
                        <i class='bx bxl-whatsapp' style="color:#25d366;font-size:16px;"></i>
                        <span id="detail-kontak" style="color:#007bff;font-weight:700;">0812-8901-2345</span>
                    </div>
                    <div class="detail-profil-item__sub">Terhubung ke WhatsApp Grup Warga RT</div>
                </div>
                <div>
                    <div class="detail-profil-item__label">STATUS EKONOMI</div>
                    <div style="margin-top:4px;">
                        <span class="badge-ekonomi-detail" id="detail-ekonomi">Ekonomi: Mampu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Susunan Anggota Keluarga --}}
    <div class="detail-section-card">
        <div class="detail-section-head" style="cursor:default;">
            <h3 class="detail-section-title">
                <i class='bx bx-group'></i>
                Susunan Anggota Keluarga
                <span class="badge-tab-kk" id="detail-badge-count">3 Orang</span>
            </h3>
            <div class="detail-anggota-actions">
                <button type="button" class="btn-reset-password" onclick="konfirmasiResetPassword(document.getElementById('detail-kepala-nama').textContent)">
                    <i class='bx bx-shield-quarter'></i> Reset Password Akun
                </button>
                <a href="{{ route('admin.data-warga.tambah-anggota') }}" class="btn-tambah-anggota">
                    <i class='bx bx-user-plus'></i> Tambah Anggota
                </a>
            </div>
        </div>

        <div class="warga-table-wrap">
            <table class="warga-table" id="table-anggota">
                <colgroup>
                    <col style="width: 3.5%;">
                    <col style="width: 21%;">
                    <col style="width: 14%;">
                    <col style="width: 13.5%;">
                    <col style="width: 19%;">
                    <col style="width: 11%;">
                    <col style="width: 18%;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">NO</th>
                        <th>NIK &amp; NAMA LENGKAP</th>
                        <th>HUBUNGAN KK</th>
                        <th>JK &amp; AGAMA</th>
                        <th>PENDIDIKAN TERAKHIR &amp; PEKERJAAN</th>
                        <th>STATUS WARGA</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody id="detail-anggota-tbody">
                    @foreach($anggotaKk as $m)
                    <tr>
                        <td style="text-align: center; color: #495057; font-weight: 500;">{{ $m['no'] }}</td>
                        <td>
                            <div style="font-weight: 700; color: #212529; font-size: 13px;">{{ $m['nama'] }}</div>
                            <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">NIK : {{ $m['nik'] }}</div>
                        </td>
                        <td>
                            @if($m['hubungan_badge'] === 'primary')
                                <span class="badge-hub-kepala">{{ $m['hubungan'] }}</span>
                            @elseif($m['hubungan_badge'] === 'teal')
                                <span class="badge-hub-istri">{{ $m['hubungan'] }}</span>
                            @else
                                <span class="badge-hub-anak">{{ $m['hubungan'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="color: #212529; font-weight: 600; font-size: 13px;">{{ $m['jk'] }}</div>
                            <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">{{ $m['agama'] }}</div>
                        </td>
                        <td>
                            <div style="color: #212529; font-weight: 600; font-size: 13px;">{{ $m['pendidikan_terakhir'] }}</div>
                            <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">{{ $m['pekerjaan'] }}</div>
                        </td>
                        <td>
                            <span class="badge-warga-hidup">{{ $m['status_warga'] }}</span>
                        </td>
                        <td>
                            <div class="warga-row-actions">
                                <button type="button" class="btn-act-detail-blue" onclick="bukaModalDetailWarga('{{ $m['nik'] }}')" title="Lihat Detail Profil Warga">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <a href="{{ url('/admin/data-warga/edit-anggota/' . $m['nik']) }}" class="btn-act-edit-green" title="Edit Data Warga">
                                    <i class='bx bx-edit'></i> Edit
                                </a>
                                <button type="button" class="btn-act-delete" onclick="konfirmasiHapusAnggota('{{ $m['nik'] }}', '{{ addslashes($m['nama']) }}', this)" title="Hapus Warga dari KK">
                                    <i class='bx bx-trash'></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="detail-footer-sync">
            <div class="detail-footer-sync__left">
                <i class='bx bx-info-circle'></i>
                <span>Semua data anggota keluarga di atas telah disinkronkan dengan basis data RT 04 / RW 08.</span>
            </div>
            <div>
                Terakhir diupdate: 18 Feb 2025 • 09:42 WIB
            </div>
        </div>
    </div>

</div>

{{-- ========================================================
     MODAL POPUP KONFIRMASI HAPUS DATA (KK / WARGA)
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-hapus-warga" onclick="if(event.target === this) tutupModalHapus()">
    <div class="warga-modal-dialog">
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
            <button type="button" class="btn-warga-modal-action-red" id="btn-confirm-hapus" onclick="eksekusiHapusData()">
                <i class='bx bx-trash'></i> Ya, Hapus Data
            </button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL POPUP KONFIRMASI RESET PASSWORD AKUN
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-reset-password-warga" onclick="if(event.target === this) tutupModalReset()">
    <div class="warga-modal-dialog">
        <div class="warga-modal-header warga-modal-header--green">
            <div class="warga-modal-header-left">
                <div class="warga-modal-icon-circle">
                    <i class='bx bx-shield-quarter'></i>
                </div>
                <h4 class="warga-modal-title">Konfirmasi Reset Password Akun</h4>
            </div>
            <button type="button" class="warga-modal-close" onclick="tutupModalReset()">&times;</button>
        </div>
        <div class="warga-modal-body">
            <p class="warga-modal-question" id="modal-reset-question">
                Apakah Anda yakin ingin me-reset kata sandi akun kepala keluarga / warga ini?
            </p>
            <div class="warga-modal-alert-box">
                <i class='bx bx-info-circle warga-modal-alert-icon'></i>
                <div class="warga-modal-alert-text">
                    <strong>Perhatian:</strong> Warga harus menggunakan NIK sebagai password saat login berikutnya di aplikasi WargaKu.
                </div>
            </div>
        </div>
        <div class="warga-modal-footer">
            <button type="button" class="btn-warga-modal-batal" onclick="tutupModalReset()">Batal</button>
            <button type="button" class="btn-warga-modal-action-green" onclick="eksekusiResetPassword()">
                <i class='bx bx-check-circle'></i> Ya, Reset Password
            </button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL POPUP UBAH STATUS KELUARGA
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-edit-status-kk" onclick="if(event.target === this) tutupModalEditStatus()">
    <div class="warga-modal-dialog">
        <div class="warga-modal-header warga-modal-header--green">
            <h4 class="warga-modal-title">Ubah status keluarga</h4>
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
                    <select class="edit-status-select" id="edit-input-tinggal">
                        <option value="Tetap">Tetap</option>
                        <option value="Kontrak / Sewa">Kontrak / Sewa</option>
                    </select>
                </div>
                <div class="edit-status-field">
                    <label class="edit-status-label" for="edit-input-ekonomi">Status Ekonomi</label>
                    <select class="edit-status-select" id="edit-input-ekonomi">
                        <option value="Mampu">Mampu</option>
                        <option value="Kurang Mampu">Kurang Mampu</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="warga-modal-footer">
            <button type="button" class="btn-warga-modal-batal" onclick="tutupModalEditStatus()">Batal</button>
            <button type="button" class="btn-warga-modal-action-green" onclick="simpanEditStatusKK()">
                <i class='bx bx-check-double'></i> Simpan Data
            </button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL POPUP DETAIL BIODATA WARGA (SCREENSHOT 1)
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-detail-warga" onclick="if(event.target === this) tutupModalDetailWarga()">
    <div class="warga-modal-dialog" style="max-width: 820px; width: 92%;">
        <div class="warga-modal-header warga-modal-header--blue">
            <h4 class="warga-modal-title">Detail Biodata Kependudukan Warga</h4>
            <button type="button" class="warga-modal-close" onclick="tutupModalDetailWarga()">&times;</button>
        </div>
        <div class="warga-modal-body" style="padding: 20px 22px;">
            <div class="detail-warga-banner">
                <h3 class="detail-warga-banner-name" id="modal-dw-nama">Siti Rahmawati, S.Ak.</h3>
                <span class="badge-warga-hidup" id="modal-dw-status">● Hidup (Aktif)</span>
            </div>

            <div class="detail-warga-grid">
                <div class="detail-warga-subcard">
                    <h5 class="detail-warga-subcard-title">IDENTITAS UTAMA & BIODATA</h5>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">NIK:</span>
                        <span class="detail-warga-v" id="modal-dw-nik" style="font-weight:700;">3275015509010003</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Nama Lengkap:</span>
                        <span class="detail-warga-v" id="modal-dw-namalengkap">Siti Rahmawati, S.Ak.</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Tempat, Tanggal Lahir:</span>
                        <span class="detail-warga-v" id="modal-dw-ttl">Depok, 15 September 2001</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Jenis Kelamin:</span>
                        <span class="detail-warga-v" id="modal-dw-jk">Perempuan</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Agama:</span>
                        <span class="detail-warga-v" id="modal-dw-agama">Islam</span>
                    </div>
                </div>

                <div class="detail-warga-subcard">
                    <h5 class="detail-warga-subcard-title">HUBUNGAN & SOSIAL</h5>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">No. Kartu Keluarga:</span>
                        <span class="detail-warga-v" id="modal-dw-nokk" style="font-weight:700;">3275010905120008</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Hubungan Keluarga:</span>
                        <span class="detail-warga-v" id="modal-dw-hubungan">Anak Kandung</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Pendidikan Terakhir:</span>
                        <span class="detail-warga-v" id="modal-dw-pendidikan">S1 Akuntansi</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Pekerjaan:</span>
                        <span class="detail-warga-v" id="modal-dw-pekerjaan">Karyawan Swasta (Auditor)</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Status Warga:</span>
                        <span class="detail-warga-v">
                            <span class="badge-warga-hidup" id="modal-dw-status-badge">Hidup (Aktif)</span>
                        </span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Alamat Lengkap:</span>
                        <span class="detail-warga-v" id="modal-dw-alamat">Blok B4 No. 12, RT 04 / RW 08</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="warga-modal-footer" style="padding-top:0;">
            <button type="button" class="btn-warga-modal-batal" onclick="tutupModalDetailWarga()">
                <i class='bx bx-x'></i> Tutup
            </button>
            <button type="button" class="btn-warga-modal-action-orange" id="btn-modal-edit-warga-action" onclick="editDariModalDetail()">
                <i class='bx bx-edit'></i> Edit Data Warga Ini
            </button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL REVIEW VERIFIKASI PENGAJUAN (FOTO 1)
     ======================================================== --}}
<div class="modal-review-verifikasi-overlay" id="modal-review-verifikasi" style="display: none;">
    <div class="modal-review-verifikasi-dialog">
        {{-- Header Dark --}}
        <div class="rev-modal-header">
            <div>
                <div class="rev-modal-header__title-row">
                    <h4 class="rev-modal-header__title">Review Verifikasi Pengajuan</h4>
                    <span class="rev-modal-header__badge-status" id="rev-badge-status">Menunggu Persetujuan RT</span>
                </div>
                <div class="rev-modal-header__subtitle">
                    Pemohon: <strong id="rev-pemohon-nama">Bambang Santoso</strong> • No. KK: <span id="rev-pemohon-nokk">3275010905120008</span> • <span id="rev-pemohon-blok">Blok B4 No. 12</span>
                </div>
            </div>
            <button type="button" class="rev-modal-header__close" onclick="tutupModalReviewVerifikasi()" title="Tutup">
                ✕
            </button>
        </div>

        {{-- Body --}}
        <div class="rev-modal-body">
            {{-- Target Data Banner --}}
            <div class="rev-target-banner">
                <div>
                    <div class="rev-target-title">Target Data: <strong id="rev-target-nama">Siti Rahmawati (Anak)</strong></div>
                    <div class="rev-target-sub" id="rev-target-desc">NIK: 3275015509010003 • Perempuan • Lahir: Bekasi, 06-09-2001</div>
                </div>
                <div class="rev-target-date">
                    Diajukan pada: <span id="rev-target-waktu">18 Feb 2025, 09:15 WIB</span>
                </div>
            </div>

            {{-- 2 Columns Grid --}}
            <div class="rev-columns-grid">
                {{-- Kolom Kiri: Komparasi Data --}}
                <div class="rev-col-left">
                    <div class="rev-col-title-bar">
                        <h5 class="rev-col-heading">
                            <i class='bx bx-git-compare' style="color: #0284c7; font-size: 16px;"></i>
                            Komparasi Data Lama vs Usulan Baru
                        </h5>
                        <span class="rev-badge-count" id="rev-compare-count">3 Atribut Diperbarui</span>
                    </div>

                    <div class="rev-compare-table-wrap">
                        <table class="rev-compare-table">
                            <thead>
                                <tr>
                                    <th style="width: 32%;">FIELD / ATRIBUT</th>
                                    <th style="width: 34%;">DATA LAMA</th>
                                    <th style="width: 34%;">USULAN BARU WARGA</th>
                                </tr>
                            </thead>
                            <tbody id="rev-compare-tbody">
                                <tr>
                                    <td class="rev-field-label">Pekerjaan Utama</td>
                                    <td class="rev-val-old">Pelajar/Mahasiswa</td>
                                    <td>
                                        <div class="rev-val-new">
                                            <span>Karyawan Swasta</span>
                                            <span class="rev-badge-diubah">Diubah</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="rev-field-label">Pendidikan Terakhir</td>
                                    <td class="rev-val-old">SLTA / Sederajat</td>
                                    <td>
                                        <div class="rev-val-new">
                                            <span>D4/S1</span>
                                            <span class="rev-badge-diubah">Diubah</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="rev-field-label">Status Perkawinan</td>
                                    <td class="rev-val-old">Belum Kawin</td>
                                    <td>
                                        <div class="rev-val-new">
                                            <span>Kawin Tercatat</span>
                                            <span class="rev-badge-diubah">Diubah</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="rev-field-label">Agama</td>
                                    <td class="rev-val-old">Islam</td>
                                    <td class="rev-val-old">Islam (Tetap)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Kanan: Pratinjau Dokumen Pendukung --}}
                <div class="rev-col-right">
                    <div class="rev-col-title-bar">
                        <h5 class="rev-col-heading">
                            <i class='bx bx-check-shield' style="color: #16a34a; font-size: 16px;"></i>
                            Pratinjau Dokumen Pendukung
                        </h5>
                        <span class="rev-badge-count" id="rev-doc-count">2 Dokumen</span>
                    </div>

                    {{-- Tab List Dokumen (Dibuat Dinamis Berdasarkan Upload Warga) --}}
                    <div class="rev-doc-tabs" id="rev-doc-tabs-container">
                        {{-- Diisi secara dinamis oleh JavaScript --}}
                    </div>

                    {{-- Preview Container Canvas Gambar Dokumen --}}
                    <div class="rev-doc-preview-box">
                        <div class="rev-doc-display-container">
                            <img id="rev-doc-preview-img" src="{{ asset('contoh_Ijazah.png') }}" alt="Dokumen Pratinjau" class="rev-doc-display-img" onclick="perbesarDokumenAktif()" title="Klik untuk memperbesar gambar">
                        </div>
                        <div class="rev-doc-info-bar">
                            <span id="rev-doc-caption" style="font-size: 12px; color: #475569; font-weight: 600;">1. Ijazah_S1_Siti.png</span>
                            <span style="font-size: 11.5px; color: #16a34a; font-weight: 600;"><i class='bx bx-check-circle'></i> Dokumen Asli Terverifikasi</span>
                        </div>

                        {{-- Action Buttons below preview --}}
                        <div class="rev-doc-actions">
                            <button type="button" class="rev-btn-action" id="rev-btn-perbesar" onclick="perbesarDokumenAktif()">
                                <i class='bx bx-zoom-in'></i> Perbesar Berkas
                            </button>
                            <a id="rev-btn-download" href="{{ asset('contoh_Ijazah.png') }}" download="contoh_Ijazah.png" class="rev-btn-action" style="text-decoration: none;">
                                <i class='bx bx-download'></i> Unduh File Asli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="rev-modal-footer">
            <button type="button" class="btn-rev-footer-cancel" onclick="tutupModalReviewVerifikasi()">
                Tutup / Batal
            </button>
            <div class="rev-modal-footer__right">
                <button type="button" class="btn-rev-footer-reject" onclick="tutupModalReviewVerifikasi()">
                    Tutup / Batal
                </button>
                <button type="button" class="btn-rev-footer-approve" onclick="konfirmasiDariModalReview()">
                    <i class='bx bx-check-circle'></i> Setujui &amp; Perbarui Database
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL KONFIRMASI SETUJUI VERIFIKASI (FOTO 2)
     ======================================================== --}}
<div class="modal-konfirmasi-overlay" id="modal-konfirmasi-setujui" style="display: none;">
    <div class="modal-konfirmasi-setujui-dialog">
        <h4 class="modal-konfirmasi-setujui-title">Apakah Kamu yakin Menyetujuinya</h4>
        <div class="modal-konfirmasi-setujui-actions">
            <button type="button" class="btn-konfirm-batal" onclick="tutupKonfirmasiSetujui()">
                Batal
            </button>
            <button type="button" class="btn-konfirm-setuju" id="btn-eksekusi-konfirmasi" onclick="eksekusiKonfirmasiSetujui()">
                Konfirmasi
            </button>
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

    // Buka tampilan Detail KK
    function bukaDetailKK(noKk, namaKepala, nikKepala, alamat, kontak, ekonomi, tinggal, jmlAnggota, jmlLaki, jmlPerempuan) {
        // Sembunyikan main view & tampilkan detail view
        document.getElementById('stats-main').style.display = 'none';
        document.getElementById('view-main').style.display = 'none';
        document.getElementById('stats-detail').style.display = 'grid';
        document.getElementById('view-detail-kk').style.display = 'flex';

        // Update header & tombol
        document.getElementById('page-title').innerHTML = 'Detail Kartu Keluarga (KK): <span style="color:#007bff;font-weight:700">' + noKk + '</span>';
        document.getElementById('page-subtitle').textContent = 'Informasi lengkap kepala keluarga, susunan seluruh anggota keluarga terdaftar, dokumen KK, dan status kependudukan.';
        document.getElementById('btn-cetak').style.display = 'none';
        document.getElementById('btn-kembali-kk').style.display = 'inline-flex';
        document.getElementById('btn-kembali-ke-detail').style.display = 'none';
        document.getElementById('header-breadcrumb').innerHTML = '<i class=\'bx bx-home-alt\'></i> Portal RT 04 / Kelola Data Warga / <span style="color:#007bff;font-weight:600">Detail Kartu Keluarga</span>';
        document.getElementById('topbar-breadcrumb-label').textContent = 'Detail Kartu Keluarga';

        // Update Stat boxes detail
        document.getElementById('detail-stat-no-kk').textContent = noKk;
        document.getElementById('detail-stat-jiwa').innerHTML = jmlAnggota + ' <small>Jiwa Aktif</small>';
        document.getElementById('detail-stat-gender').textContent = jmlLaki + ' Laki-laki • ' + jmlPerempuan + ' Perempuan';
        document.getElementById('detail-stat-hunian').textContent = tinggal || 'Tetap (Milik Sendiri)';

        // Update Card Profil Keluarga
        document.getElementById('detail-kepala-nama').textContent = namaKepala;
        document.getElementById('detail-kepala-nik').textContent = nikKepala || '3275012304800001';
        document.getElementById('detail-alamat').textContent = alamat;
        document.getElementById('detail-kontak').textContent = kontak;
        document.getElementById('detail-ekonomi').textContent = 'Ekonomi: ' + (ekonomi || 'Mampu');
        document.getElementById('detail-badge-count').textContent = jmlAnggota + ' Orang';

        // Sinkronisasi URL agar jika di-refresh tetap di Detail KK
        if (window.history && window.history.pushState) {
            history.pushState(null, '', '{{ route('admin.data-warga') }}?view=detail');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Tutup tampilan Detail KK & kembali ke Daftar KK
    function tutupDetailKK() {
        document.getElementById('stats-detail').style.display = 'none';
        document.getElementById('view-detail-kk').style.display = 'none';
        document.getElementById('stats-main').style.display = 'grid';
        document.getElementById('view-main').style.display = 'block';

        document.getElementById('page-title').textContent = 'Kelola Data Warga & Verifikasi Kependudukan';
        document.getElementById('page-subtitle').textContent = 'Kelola data master kependudukan warga RT 04, status mutasi, dan validasi permohonan perubahan data dari warga.';
        document.getElementById('btn-cetak').style.display = 'inline-flex';
        document.getElementById('btn-kembali-kk').style.display = 'none';
        document.getElementById('btn-kembali-ke-detail').style.display = 'none';
        document.getElementById('header-breadcrumb').innerHTML = '<i class=\'bx bx-home-alt\'></i> <span>Portal RT 04</span>';
        document.getElementById('topbar-breadcrumb-label').textContent = 'Kelola Data Warga';

        // Kembalikan URL ke rute utama
        if (window.history && window.history.pushState) {
            history.pushState(null, '', '{{ route('admin.data-warga') }}');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Toggle collapse profil rumah
    function toggleCollapse(contentId, chevronId) {
        var el = document.getElementById(contentId);
        var ch = document.getElementById(chevronId);
        if (el.style.display === 'none') {
            el.style.display = 'block';
            ch.className = 'bx bx-chevron-up';
        } else {
            el.style.display = 'none';
            ch.className = 'bx bx-chevron-down';
        }
    }

    // ── Pagination KK ──
    const KK_PAGE_SIZE = 5;
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
            if (currentKkPage > 1) {
                currentKkPage--;
                renderKkPagination();
            }
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
            if (currentKkPage < totalPages) {
                currentKkPage++;
                renderKkPagination();
            }
        });
        container.appendChild(btnNext);
    }

    function createWargaPageBtn(label, isDisabled, isActive, onClickHandler) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'warga-page-btn' + (isActive ? ' warga-page-btn--active' : '') + (isDisabled ? ' warga-page-btn--disabled' : '');
        btn.innerHTML = label;
        btn.disabled = !!isDisabled;
        if (!isDisabled && !isActive) {
            btn.onclick = onClickHandler;
        }
        return btn;
    }

    // Filter KK pencarian
    function terapkanFilterKK() {
        currentKkPage = 1;
        renderKkPagination();
    }

    // Reset filter KK
    function resetFilterKK() {
        document.getElementById('search-kk').value = '';
        document.getElementById('filter-tinggal').value = '';
        document.getElementById('filter-ekonomi').value = '';
        terapkanFilterKK();
    }

    // Event listener input search langsung realtime
    document.getElementById('search-kk').addEventListener('input', terapkanFilterKK);
    document.getElementById('filter-tinggal').addEventListener('change', terapkanFilterKK);
    document.getElementById('filter-ekonomi').addEventListener('change', terapkanFilterKK);

    // Initial render
    document.addEventListener('DOMContentLoaded', renderKkPagination);
    renderKkPagination();

    // Cek parameter URL untuk tab aktif atau view detail
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'verifikasi' || window.location.hash === '#verifikasi') {
        switchWargaTab('verifikasi');
    } else if (urlParams.get('view') === 'detail') {
        bukaDetailKK('3275010905120008', 'Bambang Santoso, S.T.', '3275012304800001', 'Blok B4 No. 12, RT 04 / RW 08', '0812-8901-2345', 'Mampu', 'Tetap (Milik Sendiri)', 3, 1, 2);
    }

    // ── Logic Modal Hapus Data (KK & Anggota Warga) ──
    var hapusState = {
        tipe: 'kk',
        id: '',
        nama: '',
        rowElement: null
    };

    function konfirmasiHapusKK(noKk, namaKepala, btnElement) {
        hapusState.tipe = 'kk';
        hapusState.id = noKk;
        hapusState.nama = namaKepala;
        hapusState.rowElement = btnElement ? btnElement.closest('tr') : null;

        document.getElementById('modal-hapus-title').textContent = 'Konfirmasi Hapus Data Kartu Keluarga';
        document.getElementById('modal-hapus-question').innerHTML = 'Apakah Anda yakin ingin menghapus data kartu keluarga atas nama <strong>' + namaKepala + '</strong> (No. KK: ' + noKk + ')?';
        document.getElementById('modal-hapus-warga').style.display = 'flex';
    }

    function konfirmasiHapusAnggota(nik, namaWarga, btnElement) {
        hapusState.tipe = 'anggota';
        hapusState.id = nik;
        hapusState.nama = namaWarga;
        hapusState.rowElement = btnElement ? btnElement.closest('tr') : null;

        document.getElementById('modal-hapus-title').textContent = 'Konfirmasi Hapus Data Warga';
        document.getElementById('modal-hapus-question').innerHTML = 'Apakah Anda yakin ingin menghapus anggota keluarga <strong>' + namaWarga + '</strong> (NIK: ' + nik + ') dari Kartu Keluarga ini?';
        document.getElementById('modal-hapus-warga').style.display = 'flex';
    }

    function tutupModalHapus() {
        document.getElementById('modal-hapus-warga').style.display = 'none';
        hapusState = { tipe: 'kk', id: '', nama: '', rowElement: null };
    }

    function eksekusiHapusData() {
        var targetRow = hapusState.rowElement;
        var namaTarget = hapusState.nama;
        var tipeTarget = hapusState.tipe;

        tutupModalHapus();

        if (targetRow) {
            targetRow.style.transition = 'all 0.3s ease';
            targetRow.style.opacity = '0';
            targetRow.style.transform = 'scale(0.96)';
            setTimeout(function() {
                targetRow.remove();
                if (tipeTarget === 'kk') {
                    renderKkPagination();
                } else if (tipeTarget === 'anggota') {
                    var remainingAnggota = document.querySelectorAll('#detail-anggota-tbody tr').length;
                    var badgeEl = document.getElementById('detail-badge-count');
                    var statJiwaEl = document.getElementById('detail-stat-jiwa');
                    if (badgeEl) badgeEl.textContent = remainingAnggota + ' Orang';
                    if (statJiwaEl) statJiwaEl.innerHTML = remainingAnggota + ' <small>Jiwa Aktif</small>';
                }
            }, 250);
        }
    }

    // ── Logic Modal Reset Password ──
    var resetTargetNama = '';

    function konfirmasiResetPassword(nama) {
        resetTargetNama = nama || 'Bambang Santoso, S.T.';
        document.getElementById('modal-reset-question').innerHTML = 'Apakah Anda yakin ingin me-reset kata sandi akun kepala keluarga / warga <strong>' + resetTargetNama + '</strong> ini?';
        document.getElementById('modal-reset-password-warga').style.display = 'flex';
    }

    function tutupModalReset() {
        document.getElementById('modal-reset-password-warga').style.display = 'none';
    }

    function eksekusiResetPassword() {
        tutupModalReset();
        alert('Kata sandi akun ' + resetTargetNama + ' berhasil di-reset. Warga dapat login kembali menggunakan NIK sebagai password.');
    }

    // ── Logic Modal Ubah Status Keluarga ──
    var editStatusState = {
        noKk: '',
        namaKepala: '',
        rowElement: null
    };

    function bukaModalEditStatus(noKk, namaKepala, statusTinggal, statusEkonomi, btnElement) {
        editStatusState.noKk = noKk;
        editStatusState.namaKepala = namaKepala;
        editStatusState.rowElement = btnElement ? btnElement.closest('tr') : null;

        document.getElementById('edit-status-nokk').textContent = noKk;
        
        var selectTinggal = document.getElementById('edit-input-tinggal');
        var selectEkonomi = document.getElementById('edit-input-ekonomi');
        
        if (selectTinggal) {
            selectTinggal.value = (statusTinggal && statusTinggal.toLowerCase().includes('kontrak')) ? 'Kontrak / Sewa' : 'Tetap';
        }
        if (selectEkonomi) {
            selectEkonomi.value = (statusEkonomi && statusEkonomi.toLowerCase().includes('kurang')) ? 'Kurang Mampu' : 'Mampu';
        }

        document.getElementById('modal-edit-status-kk').style.display = 'flex';
    }

    function tutupModalEditStatus() {
        document.getElementById('modal-edit-status-kk').style.display = 'none';
        editStatusState = { noKk: '', namaKepala: '', rowElement: null };
    }

    function simpanEditStatusKK() {
        var row = editStatusState.rowElement;
        var tinggalVal = document.getElementById('edit-input-tinggal').value;
        var ekonomiVal = document.getElementById('edit-input-ekonomi').value;
        var noKk = editStatusState.noKk;
        var namaKepala = editStatusState.namaKepala;

        if (row) {
            // Update data attributes pada tr agar live search/filter cocok
            row.dataset.tinggal = tinggalVal.toLowerCase();
            row.dataset.ekonomi = ekonomiVal.toLowerCase();

            // Update badge di dalam baris
            var tagsWrap = row.querySelector('.status-tags-wrap');
            if (tagsWrap) {
                var badgeTinggalClass = (tinggalVal === 'Tetap') ? 'badge-status-tetap' : 'badge-status-sewa';
                tagsWrap.innerHTML = '<span class="' + badgeTinggalClass + '">' + tinggalVal + '</span> ' +
                                     '<span class="badge-ekonomi-gray">' + ekonomiVal + '</span>';
            }

            // Update onclick tombol edit agar menyimpan state terbaru
            var btnEdit = row.querySelector('.btn-act-edit');
            if (btnEdit) {
                btnEdit.onclick = function() {
                    bukaModalEditStatus(noKk, namaKepala, tinggalVal, ekonomiVal, btnEdit);
                };
            }
        }

        tutupModalEditStatus();
    }

    // ── DATA STORE ANGGOTA KELUARGA ──
    var anggotaData = {
        '3275012304800001': {
            no: 1,
            nik: '3275012304800001',
            nama: 'Bambang Santoso, S.T.',
            tempat_lahir: 'Surabaya',
            tanggal_lahir: '23 April 1980',
            tanggal_lahir_raw: '1980-04-23',
            jk: 'Laki-laki',
            agama: 'Islam',
            no_kk: '3275010905120008',
            hubungan: 'Kepala Keluarga',
            shdk: 'Kepala Keluarga',
            pendidikan: 'Sarjana (S1)',
            pekerjaan: 'Pegawai BUMN (PT PLN)',
            status_kawin: 'Kawin',
            status_warga: 'Hidup (Aktif)',
            alamat: 'Blok B4 No. 12, RT 04 / RW 08'
        },
        '3275016508910004': {
            no: 2,
            nik: '3275016508910004',
            nama: 'Ratna Dewi Puspita, M.Pd.',
            tempat_lahir: 'Bandung',
            tanggal_lahir: '15 Agustus 1982',
            tanggal_lahir_raw: '1982-08-15',
            jk: 'Perempuan',
            agama: 'Islam',
            no_kk: '3275010905120008',
            hubungan: 'Istri',
            shdk: 'Istri',
            pendidikan: 'Magister (S2)',
            pekerjaan: 'Guru SMA Negeri 4',
            status_kawin: 'Kawin',
            status_warga: 'Hidup (Aktif)',
            alamat: 'Blok B4 No. 12, RT 04 / RW 08'
        },
        '3275015509010003': {
            no: 3,
            nik: '3275015509010003',
            nama: 'Siti Rahmawati, S.Ak.',
            tempat_lahir: 'Depok',
            tanggal_lahir: '15 September 2001',
            tanggal_lahir_raw: '2001-09-15',
            jk: 'Perempuan',
            agama: 'Islam',
            no_kk: '3275010905120008',
            hubungan: 'Anak Kandung',
            shdk: 'Anak',
            pendidikan: 'Sarjana (S1)',
            pekerjaan: 'Karyawan Swasta (Auditor)',
            status_kawin: 'Belum Kawin',
            status_warga: 'Hidup (Aktif)',
            alamat: 'Blok B4 No. 12, RT 04 / RW 08'
        }
    };

    function formatTglIndo(dateStr) {
        if (!dateStr) return '';
        var parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        var bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        var day = parseInt(parts[2], 10);
        var month = parseInt(parts[1], 10);
        var year = parts[0];
        return day + ' ' + (bulan[month] || '') + ' ' + year;
    }

    // ── MODAL DETAIL BIODATA WARGA (SCREENSHOT 1) ──
    var activeDetailNik = '';

    function bukaModalDetailWarga(nik) {
        var w = anggotaData[nik];
        if (!w) {
            // Fallback cari di baris tabel jika data baru
            var rows = document.querySelectorAll('#detail-anggota-tbody tr');
            rows.forEach(function(r) {
                var text = r.textContent;
                if (text.includes(nik)) {
                    w = {
                        nik: nik,
                        nama: r.querySelector('div[style*="font-weight: 700"]')?.textContent || 'Warga RT 04',
                        tempat_lahir: 'Depok',
                        tanggal_lahir: '15 September 2001',
                        jk: 'Perempuan',
                        agama: 'Islam',
                        no_kk: document.getElementById('detail-stat-no-kk').textContent || '3275010905120008',
                        hubungan: 'Anak Kandung',
                        pendidikan: 'Sarjana (S1)',
                        pekerjaan: 'Karyawan Swasta',
                        status_warga: 'Hidup (Aktif)',
                        alamat: document.getElementById('detail-alamat').textContent || 'Blok B4 No. 12, RT 04 / RW 08'
                    };
                }
            });
        }
        if (!w) return;
        activeDetailNik = nik;

        document.getElementById('modal-dw-nama').textContent = w.nama;
        document.getElementById('modal-dw-nik').textContent = w.nik;
        document.getElementById('modal-dw-namalengkap').textContent = w.nama;
        document.getElementById('modal-dw-ttl').textContent = (w.tempat_lahir || 'Depok') + ', ' + (w.tanggal_lahir || '15 September 2001');
        document.getElementById('modal-dw-jk').textContent = w.jk;
        document.getElementById('modal-dw-agama').textContent = w.agama;
        document.getElementById('modal-dw-nokk').textContent = w.no_kk || document.getElementById('detail-stat-no-kk').textContent;
        document.getElementById('modal-dw-hubungan').textContent = w.hubungan || w.shdk || 'Anak Kandung';
        document.getElementById('modal-dw-pendidikan').textContent = w.pendidikan || 'Sarjana (S1)';
        document.getElementById('modal-dw-pekerjaan').textContent = w.pekerjaan || 'Karyawan Swasta (Auditor)';
        document.getElementById('modal-dw-status').innerHTML = '● ' + (w.status_warga || 'Hidup (Aktif)');
        document.getElementById('modal-dw-status-badge').textContent = w.status_warga || 'Hidup (Aktif)';
        document.getElementById('modal-dw-alamat').textContent = w.alamat || document.getElementById('detail-alamat').textContent;

        document.getElementById('modal-detail-warga').style.display = 'flex';
    }

    function tutupModalDetailWarga() {
        document.getElementById('modal-detail-warga').style.display = 'none';
        activeDetailNik = '';
    }

    function editDariModalDetail() {
        var nik = activeDetailNik || '3275015509010003';
        tutupModalDetailWarga();
        window.location.href = "{{ url('/admin/data-warga/edit-anggota') }}/" + nik;
    }

    // ── DATA & LOGIC VERIFIKASI PENGAJUAN (FOTO 1 & FOTO 2) ──
    var verifikasiDataMap = {
        1: {
            id: 1,
            pemohon: 'Bambang Santoso',
            no_kk: '3275010905120008',
            blok: 'Blok B4 No. 12',
            target_nama: 'Siti Rahmawati (Anak)',
            target_sub: 'NIK: 3275015509010003 • Perempuan • Lahir: Bekasi, 06-09-2001',
            waktu: '18 Feb 2025, 09:15 WIB',
            count_attr: '3 Atribut Diperbarui',
            komparasi: [
                { field: 'Pekerjaan Utama', lama: 'Pelajar/Mahasiswa', baru: 'Karyawan Swasta', diubah: true },
                { field: 'Pendidikan Terakhir', lama: 'SLTA / Sederajat', baru: 'D4/S1', diubah: true },
                { field: 'Status Perkawinan', lama: 'Belum Kawin', baru: 'Kawin Tercatat', diubah: true },
                { field: 'Agama', lama: 'Islam', baru: 'Islam (Tetap)', diubah: false }
            ],
            dokumen: [
                { id: 1, nama: '1. Ijazah_S1_Siti.png', file_name: 'contoh_Ijazah.png', url: "{{ asset('contoh_Ijazah.png') }}", icon: 'bx bxs-file-image' },
                { id: 2, nama: '2. Buku_Nikah_Hal1.png', file_name: 'contoh_buku_nikah.png', url: "{{ asset('contoh_buku_nikah.png') }}", icon: 'bx bxs-file-image' }
            ]
        },
        2: {
            id: 2,
            pemohon: 'Dedi Kusnadi',
            no_kk: '3275011406180002',
            blok: 'Blok A2 No. 05',
            target_nama: 'Ahmad Rayyan Kusnadi (Anak)',
            target_sub: 'NIK: 3275011202250001 • Laki-laki • Lahir: Depok, 12-02-2025',
            waktu: '17 Feb 2025, 14:20 WIB',
            count_attr: '3 Atribut Diperbarui',
            komparasi: [
                { field: 'Nama Lengkap', lama: '-', baru: 'Ahmad Rayyan Kusnadi', diubah: true },
                { field: 'Hubungan Keluarga', lama: '-', baru: 'Anak Kandung', diubah: true },
                { field: 'Status Warga', lama: '-', baru: 'Hidup (Baru Lahir)', diubah: true }
            ],
            dokumen: [
                { id: 1, nama: '1. Surat_Keterangan_Lahir_RS.png', file_name: 'contoh_Ijazah.png', url: "{{ asset('contoh_Ijazah.png') }}", icon: 'bx bxs-file-image' }
            ]
        },
        3: {
            id: 3,
            pemohon: 'Agus Wicaksono',
            no_kk: '3275010203100010',
            blok: 'Blok C1 No. 08',
            target_nama: 'Agus Wicaksono (Kepala Keluarga)',
            target_sub: 'NIK: 3275010203810019 • Laki-laki • Lahir: Solo, 02-03-1981',
            waktu: '15 Feb 2025, 11:04 WIB',
            count_attr: '2 Atribut Diperbarui',
            komparasi: [
                { field: 'Status Kependudukan', lama: 'Warga Tetap RT 04', baru: 'Pindah Keluar Kota', diubah: true },
                { field: 'Alamat Tujuan', lama: 'Blok C1 No. 08 RT 04', baru: 'Jl. Kenari No. 14, Solo, Jateng', diubah: true }
            ],
            dokumen: [
                { id: 1, nama: '1. SKPWNI_Kelurahan.png', file_name: 'contoh_buku_nikah.png', url: "{{ asset('contoh_buku_nikah.png') }}", icon: 'bx bxs-file-image' }
            ]
        }
    };

    var activeVerifikasiId = 1;
    var currentVerifDocs = [];
    var currentActiveDocIdx = 0;

    function bukaModalReviewVerifikasi(id) {
        var data = verifikasiDataMap[id] || verifikasiDataMap[1];
        activeVerifikasiId = data.id;
        currentVerifDocs = data.dokumen || [];

        document.getElementById('rev-pemohon-nama').textContent = data.pemohon;
        document.getElementById('rev-pemohon-nokk').textContent = data.no_kk;
        document.getElementById('rev-pemohon-blok').textContent = data.blok;
        document.getElementById('rev-target-nama').textContent = data.target_nama;
        document.getElementById('rev-target-desc').textContent = data.target_sub;
        document.getElementById('rev-target-waktu').textContent = data.waktu;
        document.getElementById('rev-compare-count').textContent = data.count_attr;

        // Render tabel komparasi
        var tbody = document.getElementById('rev-compare-tbody');
        tbody.innerHTML = '';
        data.komparasi.forEach(function(k) {
            var tr = document.createElement('tr');
            var tdField = document.createElement('td');
            tdField.className = 'rev-field-label';
            tdField.textContent = k.field;

            var tdOld = document.createElement('td');
            tdOld.className = 'rev-val-old';
            tdOld.textContent = k.lama;

            var tdNew = document.createElement('td');
            if (k.diubah) {
                tdNew.innerHTML = '<div class="rev-val-new"><span>' + k.baru + '</span> <span class="rev-badge-diubah">Diubah</span></div>';
            } else {
                tdNew.className = 'rev-val-old';
                tdNew.textContent = k.baru;
            }

            tr.appendChild(tdField);
            tr.appendChild(tdOld);
            tr.appendChild(tdNew);
            tbody.appendChild(tr);
        });

        // Render tab dokumen secara dinamis berdasarkan data upload warga
        var tabContainer = document.getElementById('rev-doc-tabs-container');
        tabContainer.innerHTML = '';
        document.getElementById('rev-doc-count').textContent = currentVerifDocs.length + ' Dokumen';

        currentVerifDocs.forEach(function(doc, idx) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'rev-doc-tab-btn' + (idx === 0 ? ' rev-doc-tab-btn--active' : ' rev-doc-tab-btn--inactive');
            btn.id = 'rev-tab-btn-' + idx;
            btn.onclick = function() { switchRevDocIndex(idx); };
            btn.innerHTML = '<i class=\'' + (doc.icon || 'bx bxs-file-image') + '\' style="color: ' + (idx === 0 ? '#ef4444' : '#0284c7') + ';"></i> <span>' + doc.nama + '</span>';
            tabContainer.appendChild(btn);
        });

        // Tampilkan dokumen pertama
        switchRevDocIndex(0);

        document.getElementById('modal-review-verifikasi').style.display = 'flex';
    }

    function tutupModalReviewVerifikasi() {
        document.getElementById('modal-review-verifikasi').style.display = 'none';
    }

    function switchRevDocIndex(idx) {
        if (!currentVerifDocs || currentVerifDocs.length === 0) return;
        currentActiveDocIdx = idx;
        var doc = currentVerifDocs[idx];

        // Update active class pada tab buttons
        currentVerifDocs.forEach(function(_, i) {
            var btn = document.getElementById('rev-tab-btn-' + i);
            if (btn) {
                btn.className = 'rev-doc-tab-btn' + (i === idx ? ' rev-doc-tab-btn--active' : ' rev-doc-tab-btn--inactive');
            }
        });

        // Update gambar pratinjau
        var imgEl = document.getElementById('rev-doc-preview-img');
        if (imgEl) {
            imgEl.src = doc.url;
            imgEl.alt = doc.nama;
        }

        // Update caption nama berkas
        var captionEl = document.getElementById('rev-doc-caption');
        if (captionEl) {
            captionEl.textContent = doc.nama;
        }

        // Update link download asli
        var downloadEl = document.getElementById('rev-btn-download');
        if (downloadEl) {
            downloadEl.href = doc.url;
            downloadEl.setAttribute('download', doc.file_name || 'dokumen.png');
        }
    }

    function perbesarDokumenAktif() {
        if (currentVerifDocs && currentVerifDocs[currentActiveDocIdx]) {
            var url = currentVerifDocs[currentActiveDocIdx].url;
            window.open(url, '_blank');
        }
    }

    // ── MODAL KONFIRMASI SETUJUI (FOTO 2) ──
    var verifikasiToApprove = null;

    function bukaModalKonfirmasiSetujui(id, pemohon, btnElement) {
        verifikasiToApprove = {
            id: id || activeVerifikasiId || 1,
            pemohon: pemohon || (verifikasiDataMap[id] ? verifikasiDataMap[id].pemohon : 'Pemohon')
        };
        document.getElementById('modal-konfirmasi-setujui').style.display = 'flex';
    }

    function konfirmasiDariModalReview() {
        verifikasiToApprove = {
            id: activeVerifikasiId || 1,
            pemohon: verifikasiDataMap[activeVerifikasiId] ? verifikasiDataMap[activeVerifikasiId].pemohon : 'Pemohon'
        };
        document.getElementById('modal-konfirmasi-setujui').style.display = 'flex';
    }

    function tutupKonfirmasiSetujui() {
        document.getElementById('modal-konfirmasi-setujui').style.display = 'none';
    }

    function eksekusiKonfirmasiSetujui() {
        tutupKonfirmasiSetujui();
        tutupModalReviewVerifikasi();

        var targetId = verifikasiToApprove ? verifikasiToApprove.id : 1;
        var targetPemohon = verifikasiToApprove ? verifikasiToApprove.pemohon : 'Warga';

        // Update status cell di baris tabel
        var statusCell = document.getElementById('status-cell-verif-' + targetId);
        if (statusCell) {
            statusCell.innerHTML = '<span class="badge-status-disetujui"><i class=\'bx bx-check-double\'></i> Disetujui RT</span>';
        }

        // Update tombol aksi di baris tabel
        var actionsContainer = document.getElementById('row-actions-verif-' + targetId);
        if (actionsContainer) {
            actionsContainer.innerHTML = '<span class="btn-tindakan-disetujui-label"><i class=\'bx bx-check-circle\'></i> Telah Disetujui</span>';
        }

        // Tandai baris data yang disetujui
        var rowEl = document.getElementById('row-verif-' + targetId);
        if (rowEl) {
            rowEl.setAttribute('data-disetujui', '1');
        }

        // Cek sisa pengajuan yang belum disetujui untuk indikator bulatan
        var remainingPending = Array.from(document.querySelectorAll('#table-verifikasi tbody tr')).filter(function(r) {
            return r.getAttribute('data-disetujui') !== '1';
        }).length;

        var dotBadge = document.getElementById('badge-tab-dot');
        if (dotBadge && remainingPending <= 0) {
            dotBadge.style.display = 'none';
        }

        alert('Pengajuan dari ' + targetPemohon + ' telah disetujui.');
    }

    // Listener klik overlay luar untuk menutup modal
    window.addEventListener('click', function(e) {
        var modalReview = document.getElementById('modal-review-verifikasi');
        var modalKonfirm = document.getElementById('modal-konfirmasi-setujui');
        if (e.target === modalReview) {
            tutupModalReviewVerifikasi();
        }
        if (e.target === modalKonfirm) {
            tutupKonfirmasiSetujui();
        }
    });

    // Escape listener untuk menutup modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalHapus();
            tutupModalReset();
            tutupModalEditStatus();
            tutupModalDetailWarga();
            tutupKonfirmasiSetujui();
            tutupModalReviewVerifikasi();
        }
    });
</script>
@endpush
