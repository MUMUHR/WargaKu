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
    </div>
</div>

{{-- ========================================================
     TOP STAT CARDS: VIEW UTAMA (3 BOXES)
     ======================================================== --}}
<div class="warga-stats-grid" id="stats-main">
    <div class="warga-stat-box warga-stat-box--cyan">
        <div class="warga-stat-box__val">78 <small>KK</small></div>
        <div class="warga-stat-box__label">Total Kartu Keluarga Aktif</div>
        <div class="warga-stat-box__sub">Tersebar di Blok A, B, & C</div>
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
        <div class="warga-stat-box__sub">3 Ubah Data • 2 Tambah Warga</div>
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
                Daftar Warga & Kartu Keluarga
                <span class="badge-tab-kk">78 KK</span>
            </button>
            <button type="button" class="warga-tab-btn" id="tab-btn-verifikasi" onclick="switchWargaTab('verifikasi')">
                <i class='bx bx-slider-alt'></i>
                Verifikasi Pengajuan Perubahan Data
                <span class="badge-tab-verif">5 Pending</span>
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
            <div class="warga-filter-col warga-filter-col--select">
                <label class="warga-filter-label" for="filter-blok">Blok Rumah:</label>
                <select id="filter-blok" class="warga-filter-input">
                    <option value="">Semua Blok RT 04</option>
                    <option value="blok a">Blok A</option>
                    <option value="blok b">Blok B</option>
                    <option value="blok c">Blok C</option>
                    <option value="blok d">Blok D</option>
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
                    <col style="width: 14%;">
                    <col style="width: 19%;">
                    <col style="width: 9.5%;">
                    <col style="width: 13.5%;">
                    <col style="width: 13.5%;">
                    <col style="width: 27%;">
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
                                    <i class='bx bx-id-card'></i> Lihat Detail / Anggota KK
                                </button>
                                <button type="button" class="btn-act-edit" onclick="alert('Form Edit Status KK {{ $k['no_kk'] }} siap dibuka.')" title="Edit Data KK">
                                    <i class='bx bx-edit'></i> Edit Data / Ubah Status
                                </button>
                                <button type="button" class="btn-act-delete" onclick="if(confirm('Yakin ingin menghapus data KK {{ $k['no_kk'] }}?')) alert('Data KK berhasil dihapus.')" title="Hapus KK">
                                    Hapus
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
                    <tr>
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
                        <td>
                            <span class="badge-status-menunggu">
                                <i class='bx bx-hourglass'></i>
                                <span>{{ $v['status_validasi'] }}</span>
                            </span>
                        </td>
                        <td style="padding-right: 18px;">
                            <div class="warga-row-actions">
                                <button type="button" class="btn-tindakan-setujui" onclick="alert('Pengajuan dari {{ $v['pemohon'] }} telah disetujui.')">
                                    <i class='bx bx-check'></i> Setujui
                                </button>
                                <button type="button" class="btn-tindakan-review" onclick="alert('Membuka rincian review berkas permohonan {{ $v['warga_terdampak'] }}...')">
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
                <button type="button" class="btn-reset-password" onclick="alert('Tautan reset password telah dikirim ke nomor WhatsApp kepala keluarga.')">
                    <i class='bx bx-shield-quarter'></i> Reset Password Akun
                </button>
                <button type="button" class="btn-tambah-anggota" onclick="alert('Form Tambah Anggota Keluarga baru siap dibuka.')">
                    <i class='bx bx-user-plus'></i> Tambah Anggota ke KK Ini
                </button>
            </div>
        </div>

        <div class="warga-table-wrap">
            <table class="warga-table" id="table-anggota">
                <colgroup>
                    <col style="width: 4%;">
                    <col style="width: 22%;">
                    <col style="width: 14%;">
                    <col style="width: 14%;">
                    <col style="width: 20%;">
                    <col style="width: 12%;">
                    <col style="width: 14%;">
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
                                <button type="button" class="btn-act-detail-blue" onclick="alert('Membuka detail lengkap warga {{ $m['nama'] }}...')" title="Lihat Detail Profil Warga">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <button type="button" class="btn-act-edit-green" onclick="alert('Form edit data warga {{ $m['nama'] }} siap dibuka.')" title="Edit Data Warga">
                                    <i class='bx bx-edit'></i> Edit
                                </button>
                                <button type="button" class="btn-act-delete" onclick="if(confirm('Hapus anggota {{ $m['nama'] }} dari KK?')) alert('Data berhasil dihapus.')" title="Hapus Warga dari KK">
                                    Hapus
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
        document.getElementById('header-breadcrumb').innerHTML = '<i class=\'bx bx-home-alt\'></i> <span>Portal RT 04</span>';
        document.getElementById('topbar-breadcrumb-label').textContent = 'Kelola Data Warga';

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
        var blok = (document.getElementById('filter-blok').value || '').toLowerCase().trim();

        var allRows = Array.from(document.querySelectorAll('#table-kk tbody tr.row-kk-item'));

        var matchedRows = allRows.filter(function(row) {
            var nokk = (row.dataset.nokk || '').toLowerCase();
            var kepala = (row.dataset.kepala || '').toLowerCase();
            var alamat = (row.dataset.alamat || '').toLowerCase();
            var rowTinggal = (row.dataset.tinggal || '').toLowerCase();
            var rowEkonomi = (row.dataset.ekonomi || '').toLowerCase();
            var rowBlok = (row.dataset.blok || '').toLowerCase();

            var matchSearch = !q || nokk.includes(q) || kepala.includes(q) || alamat.includes(q);
            var matchTinggal = !tinggal || rowTinggal.includes(tinggal);
            var matchEkonomi = !ekonomi || rowEkonomi.includes(ekonomi);
            var matchBlok = !blok || rowBlok.includes(blok);

            return matchSearch && matchTinggal && matchEkonomi && matchBlok;
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
        document.getElementById('filter-blok').value = '';
        terapkanFilterKK();
    }

    // Event listener input search langsung realtime
    document.getElementById('search-kk').addEventListener('input', terapkanFilterKK);
    document.getElementById('filter-tinggal').addEventListener('change', terapkanFilterKK);
    document.getElementById('filter-ekonomi').addEventListener('change', terapkanFilterKK);
    document.getElementById('filter-blok').addEventListener('change', terapkanFilterKK);

    // Initial render
    document.addEventListener('DOMContentLoaded', renderKkPagination);
    renderKkPagination();

    // Cek parameter URL untuk tab aktif
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'verifikasi' || window.location.hash === '#verifikasi') {
        switchWargaTab('verifikasi');
    }
</script>
@endpush
