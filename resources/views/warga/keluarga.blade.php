@extends('layouts.warga')

@section('title', 'Data Keluarga')
@section('meta_description', 'Data Kartu Keluarga & Anggota Keluarga — WargaKu RT 04 / RW 08')

@section('topbar_section')
    <span class="admin-topbar__breadcrumb-current">Data Keluarga</span>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/warga-keluarga.css') }}">
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="warga-page-header">
    <div class="warga-page-header__left">
        <h1 class="warga-page-header__title">Data Keluarga</h1>
        <p class="warga-page-header__sub">Sistem Administrasi Kependudukan Terpadu RT 04 / RW 08</p>
    </div>
    <nav class="warga-page-header__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('warga.beranda') }}">Beranda</a>
        <span class="sep">/</span>
        <span class="current">Data Keluarga</span>
    </nav>
</div>

@if(session('success'))
<div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 18px;border-radius:6px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:600;">
        <i class='bx bx-check-circle' style="font-size:20px;color:#28a745;"></i>
        <span>{{ session('success') }}</span>
    </div>
    <button type="button" onclick="this.parentElement.remove()" style="background:transparent;border:none;color:#155724;font-size:18px;cursor:pointer;line-height:1;">
        &times;
    </button>
</div>
@endif

{{-- ── 2 Big Stat Cards ── --}}
<div class="kk-stat-grid">
    <div class="kk-stat-card kk-stat-card--teal">
        <div class="kk-stat-card__body">
            <div class="kk-stat-card__number">4</div>
            <div class="kk-stat-card__label">Total Jiwa Terdaftar</div>
            <i class='bx bxs-group kk-stat-card__watermark'></i>
        </div>
    </div>
    <div class="kk-stat-card kk-stat-card--gray">
        <div class="kk-stat-card__body">
            <div class="kk-stat-card__number">1</div>
            <div class="kk-stat-card__label">Menunggu Verifikasi RT</div>
            <i class='bx bx-file kk-stat-card__watermark'></i>
        </div>
    </div>
</div>

{{-- ── Informasi Kartu Keluarga (KK) ── --}}
<div class="kk-info-card">
    <div class="kk-info-card__header">
        <i class='bx bx-id-card'></i>
        <span>Informasi Kartu Keluarga (KK)</span>
    </div>
    <div class="kk-info-card__body">
        <div class="kk-info-item">
            <div class="kk-info-item__label">NOMOR KARTU KELUARGA</div>
            <div class="kk-info-item__value">
                3271048809920001
                <button type="button" class="kk-copy-btn" onclick="copyNoKK()" title="Salin Nomor KK" id="btn-copy-nkk">
                    <i class='bx bx-copy'></i>
                </button>
            </div>
        </div>
        <div class="kk-info-divider"></div>
        <div class="kk-info-item">
            <div class="kk-info-item__label">KEPALA KELUARGA</div>
            <div class="kk-info-item__value">
                <i class='bx bxs-user-check' style="color:#007bff; font-size:15px;"></i>
                Bpk. Bambang Pamungkas
            </div>
        </div>
        <div class="kk-info-divider"></div>
        <div class="kk-info-item">
            <div class="kk-info-item__label">ALAMAT &amp; DOMISILI</div>
            <div class="kk-info-item__value">
                Jl. Melati Blok B No. 14, RT 04 / RW 08
                <span class="kk-badge kk-badge--tetap">TETAP</span>
                <span class="kk-badge kk-badge--mampu">MAMPU</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Tab Panel ── --}}
<div class="kk-tabpanel">
    <div class="kk-tab-header">
        <button class="kk-tab-btn kk-tab-btn--active" id="tab-daftar" onclick="switchTab('daftar')" type="button">
            <i class='bx bxs-group'></i>
            Daftar Anggota Keluarga <span class="kk-tab-badge">4</span>
        </button>
        <button class="kk-tab-btn" id="tab-riwayat" onclick="switchTab('riwayat')" type="button">
            <i class='bx bx-history'></i>
            Riwayat Pengajuan Perubahan Data <span class="kk-tab-badge kk-tab-badge--orange">3</span>
        </button>
    </div>

    {{-- TAB 1: Daftar Anggota --}}
    <div class="kk-tab-content" id="panel-daftar">
        <div class="kk-toolbar">
            <div class="kk-toolbar__left">
                <div class="kk-search-wrap">
                    <i class='bx bx-search kk-search-wrap__icon'></i>
                    <input type="text" id="search-anggota" class="kk-search-input"
                           placeholder="Cari NIK, nama anggota keluarga..."
                           oninput="filterAnggota(this.value)">
                </div>
                <select class="kk-filter-select" id="filter-hubungan" onchange="filterAnggota(document.getElementById('search-anggota').value)">
                    <option value="">Semua Hubungan Keluarga</option>
                    <option value="kepala_keluarga">Kepala Keluarga</option>
                    <option value="istri">Istri</option>
                    <option value="anak">Anak</option>
                </select>
            </div>
            <a href="{{ route('warga.keluarga.tambah') }}" class="kk-btn-tambah" id="btn-tambah-anggota" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                <i class='bx bx-user-plus'></i>
                + Tambah Anggota Keluarga
            </a>
        </div>

        <div class="kk-table-wrap">
            <table class="kk-table" id="tabel-anggota">
                <thead>
                    <tr>
                        <th style="width:52px; text-align:center;">NO</th>
                        <th>NAMA LENGKAP &amp; NIK</th>
                        <th style="width:110px;">JENIS KELAMIN</th>
                        <th style="width:170px;">HUBUNGAN KK</th>
                        <th style="width:130px;">TGL LAHIR</th>
                        <th>PEKERJAAN</th>
                        <th style="width:130px;">STATUS WARGA</th>
                        <th style="width:180px; text-align:center;">AKSI</th>
                    </tr>
                </thead>
                <tbody id="tbody-anggota">
                    <tr class="anggota-row" data-hubungan="kepala_keluarga" data-nama="bambang pamungkas" data-nik="3271048809920001">
                        <td style="text-align:center; color:#495057; font-weight:600;">1</td>
                        <td>
                            <div class="kk-name">Bambang Pamungkas</div>
                            <div class="kk-nik">NIK: <a href="javascript:void(0)" class="kk-nik-link" onclick="bukaDetailWarga('bambang')">3271048809920001</a></div>
                        </td>
                        <td><span class="kk-gender-m">Laki-Laki</span></td>
                        <td><span class="kk-badge-hub kk-badge-hub--kk">Kepala Keluarga</span></td>
                        <td style="color:#495057;">15 Ags 1988</td>
                        <td style="color:#495057;">Karyawan Swasta</td>
                        <td><span class="kk-badge-status kk-badge-status--aktif">&#9679; Tetap / Aktif</span></td>
                        <td>
                            <div class="kk-aksi-wrap">
                                <button type="button" class="kk-btn-detail" onclick="bukaDetailWarga('bambang')" id="btn-detail-bambang">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <a href="{{ route('warga.keluarga.edit', ['nik' => '3271041203780002']) }}" class="kk-btn-ubah" id="btn-ubah-bambang" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
                                    <i class='bx bx-edit'></i> Ubah
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="anggota-row" data-hubungan="istri" data-nama="siti aminah" data-nik="3271046204830002">
                        <td style="text-align:center; color:#495057; font-weight:600;">2</td>
                        <td>
                            <div class="kk-name">Siti Aminah</div>
                            <div class="kk-nik">NIK: <a href="javascript:void(0)" class="kk-nik-link" onclick="bukaDetailWarga('siti')">3271046204830002</a></div>
                        </td>
                        <td><span class="kk-gender-f">Perempuan</span></td>
                        <td><span class="kk-badge-hub kk-badge-hub--istri">Istri</span></td>
                        <td style="color:#495057;">12 Apr 1983</td>
                        <td style="color:#495057;">Ibu Rumah Tangga</td>
                        <td><span class="kk-badge-status kk-badge-status--aktif">&#9679; Tetap / Aktif</span></td>
                        <td>
                            <div class="kk-aksi-wrap">
                                <button type="button" class="kk-btn-detail" onclick="bukaDetailWarga('siti')" id="btn-detail-siti">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <a href="{{ route('warga.keluarga.edit', ['nik' => '3271046204830002']) }}" class="kk-btn-ubah" id="btn-ubah-siti" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
                                    <i class='bx bx-edit'></i> Ubah
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="anggota-row" data-hubungan="anak" data-nama="dimas arya pratama" data-nik="3271042006060003">
                        <td style="text-align:center; color:#495057; font-weight:600;">3</td>
                        <td>
                            <div class="kk-name">Dimas Arya Pratama</div>
                            <div class="kk-nik">NIK: <a href="javascript:void(0)" class="kk-nik-link" onclick="bukaDetailWarga('dimas')">3271042006060003</a></div>
                        </td>
                        <td><span class="kk-gender-m">Laki-Laki</span></td>
                        <td><span class="kk-badge-hub kk-badge-hub--anak">Anak</span></td>
                        <td style="color:#495057;">20 Jun 2006</td>
                        <td style="color:#495057;">Mahasiswa / Pelajar</td>
                        <td><span class="kk-badge-status kk-badge-status--aktif">&#9679; Tetap / Aktif</span></td>
                        <td>
                            <div class="kk-aksi-wrap">
                                <button type="button" class="kk-btn-detail" onclick="bukaDetailWarga('dimas')" id="btn-detail-dimas">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <a href="{{ route('warga.keluarga.edit', ['nik' => '3271048809920003']) }}" class="kk-btn-ubah" id="btn-ubah-dimas" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
                                    <i class='bx bx-edit'></i> Ubah
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="anggota-row" data-hubungan="anak" data-nama="nabila putri kirani" data-nik="3271045011120004">
                        <td style="text-align:center; color:#495057; font-weight:600;">4</td>
                        <td>
                            <div class="kk-name">Nabila Putri Kirani</div>
                            <div class="kk-nik">NIK: <a href="javascript:void(0)" class="kk-nik-link" onclick="bukaDetailWarga('nabila')">3271045011120004</a></div>
                        </td>
                        <td><span class="kk-gender-f">Perempuan</span></td>
                        <td><span class="kk-badge-hub kk-badge-hub--anak">Anak</span></td>
                        <td style="color:#495057;">10 Nov 2012</td>
                        <td style="color:#495057;">Pelajar</td>
                        <td><span class="kk-badge-status kk-badge-status--aktif">&#9679; Tetap / Aktif</span></td>
                        <td>
                            <div class="kk-aksi-wrap">
                                <button type="button" class="kk-btn-detail" onclick="bukaDetailWarga('nabila')" id="btn-detail-nabila">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <a href="{{ route('warga.keluarga.edit', ['nik' => '3271045011120004']) }}" class="kk-btn-ubah" id="btn-ubah-nabila" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
                                    <i class='bx bx-edit'></i> Ubah
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr id="row-empty-anggota" style="display:none;">
                        <td colspan="8" style="text-align:center; padding:36px 18px; color:#6c757d; font-size:13px;">
                            <i class='bx bx-search' style="font-size:30px; display:block; margin-bottom:8px; opacity:0.35;"></i>
                            Tidak ada anggota yang sesuai pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="kk-table-footer">
            <span><i class='bx bx-info-circle' style="font-size:13px; vertical-align:middle;"></i>
                &nbsp;*) Perubahan data memerlukan verifikasi Ketua RT dengan melampirkan berkas KTP/Akta/Buku Nikah resmi.</span>
            <span>Menampilkan 4 dari 4 anggota keluarga terdaftar &nbsp;<span class="kk-pagination"><span class="kk-page-btn kk-page-btn--active">1</span></span></span>
        </div>
    </div>

    {{-- TAB 2: Riwayat Pengajuan --}}
    <div class="kk-tab-content" id="panel-riwayat" style="display:none;">
        <div class="kk-toolbar">
            <div class="kk-toolbar__left">
                <div class="kk-search-wrap">
                    <i class='bx bx-search kk-search-wrap__icon'></i>
                    <input type="text" class="kk-search-input" placeholder="Cari nomor tiket, target warga...">
                </div>
                <select class="kk-filter-select">
                    <option value="">Semua Jenis Pengajuan</option>
                    <option>Ubah Data</option>
                    <option>Tambah Anggota</option>
                </select>
                <select class="kk-filter-select">
                    <option value="">Semua Status</option>
                    <option>Menunggu</option>
                    <option>Disetujui</option>
                    <option>Ditolak</option>
                </select>
            </div>
            <a href="{{ route('warga.keluarga.edit') }}" class="kk-btn-tambah" id="btn-ajukan-perubahan" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
                <i class='bx bx-plus-circle'></i>
                + Ajukan Perubahan Baru
            </a>
        </div>

        <div class="kk-table-wrap">
            <table class="kk-table">
                <thead>
                    <tr>
                        <th style="width:52px; text-align:center;">NO</th>
                        <th style="width:145px;">TANGGAL PENGAJUAN</th>
                        <th style="width:160px;">JENIS PENGAJUAN</th>
                        <th style="width:180px;">TARGET WARGA</th>
                        <th>BUKTI PENDUKUNG</th>
                        <th style="width:115px;">STATUS</th>
                        <th>ALASAN / CATATAN RT</th>
                        <th style="width:155px; text-align:center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:center; color:#495057; font-weight:600;">1</td>
                        <td>
                            <div style="font-weight:700; color:#212529; font-size:12.5px;">14 Mei 2025</div>
                            <div style="font-size:11.5px; color:#6c757d; margin-top:2px;">14:20 WIB</div>
                        </td>
                        <td><span class="kk-badge-jenis kk-badge-jenis--ubah">Ubah Data</span></td>
                        <td style="font-weight:600; color:#212529;">Dimas Arya Pratama</td>
                        <td>
                            <a href="javascript:void(0)" class="kk-file-link" onclick="alert('Preview: KTP_Dimas.pdf')">
                                <i class='bx bx-file-blank'></i> KTP_Dimas.pdf
                            </a>
                        </td>
                        <td><span class="kk-badge-rstatus kk-badge-rstatus--menunggu">Menunggu</span></td>
                        <td style="color:#6c757d; font-size:12.5px;">-</td>
                        <td style="text-align:center;">
                            <button type="button" class="kk-btn-lihat" id="btn-lihat-1" onclick="alert('Detail pengajuan perubahan data Dimas Arya Pratama.')">
                                Lihat Perubahan
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:#495057; font-weight:600;">2</td>
                        <td>
                            <div style="font-weight:700; color:#212529; font-size:12.5px;">28 Apr 2025</div>
                            <div style="font-size:11.5px; color:#6c757d; margin-top:2px;">09:15 WIB</div>
                        </td>
                        <td><span class="kk-badge-jenis kk-badge-jenis--tambah">Tambah Anggota</span></td>
                        <td style="font-weight:600; color:#212529;">Nabila Putri Kirani</td>
                        <td>
                            <a href="javascript:void(0)" class="kk-file-link" onclick="alert('Preview: Akta_Kelahiran.jpg')">
                                <i class='bx bx-file-blank'></i> Akta_Kelahiran.jpg
                            </a>
                        </td>
                        <td><span class="kk-badge-rstatus kk-badge-rstatus--disetujui">Disetujui</span></td>
                        <td style="color:#6c757d; font-size:12.5px;">-</td>
                        <td style="text-align:center;">
                            <button type="button" class="kk-btn-lihat kk-btn-lihat--green" id="btn-lihat-2" onclick="alert('Pengajuan disetujui. Nabila berhasil ditambahkan.')">
                                Selesai
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:#495057; font-weight:600;">3</td>
                        <td>
                            <div style="font-weight:700; color:#212529; font-size:12.5px;">10 Jan 2025</div>
                            <div style="font-size:11.5px; color:#6c757d; margin-top:2px;">11:05 WIB</div>
                        </td>
                        <td><span class="kk-badge-jenis kk-badge-jenis--ubah">Ubah Data</span></td>
                        <td style="font-weight:600; color:#212529;">Siti Aminah</td>
                        <td>
                            <a href="javascript:void(0)" class="kk-file-link" onclick="alert('Preview: Ijazah_Siti.pdf')">
                                <i class='bx bx-file-blank'></i> Ijazah_Siti.pdf
                            </a>
                        </td>
                        <td><span class="kk-badge-rstatus kk-badge-rstatus--ditolak">Ditolak</span></td>
                        <td style="color:#dc3545; font-size:12.5px; font-weight:500;">Scan dokumen buram dan tidak terbaca jelas</td>
                        <td style="text-align:center;">
                            <button type="button" class="kk-btn-lihat kk-btn-lihat--green" id="btn-lihat-3" onclick="alert('Pengajuan ditolak. Silakan ajukan ulang dengan scan yang lebih jelas.')">
                                Selesai
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="kk-table-footer">
            <span></span>
            <span>Menampilkan 3 dari 3 entri &nbsp;<span class="kk-pagination"><span class="kk-page-btn kk-page-btn--active">1</span></span></span>
        </div>
    </div>
</div>

{{-- ══ MODAL DETAIL BIODATA WARGA ══ --}}
<div class="kk-modal-overlay" id="modal-detail-overlay" onclick="tutupModal(event)">
    <div class="kk-modal" id="modal-detail-warga" role="dialog" aria-modal="true" aria-labelledby="modal-detail-title">
        <div class="kk-modal__header">
            <h2 class="kk-modal__title" id="modal-detail-title">Detail Biodata Kependudukan Warga</h2>
            <button type="button" class="kk-modal__close" onclick="tutupDetailWarga()" aria-label="Tutup" id="btn-modal-close">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="kk-modal__body">
            <div class="kk-modal__name-row">
                <span class="kk-modal__nama" id="modal-nama">-</span>
                <span class="kk-modal__status">
                    <i class='bx bxs-circle' style="font-size:9px; color:#28a745;"></i>
                    Hidup (Aktif)
                </span>
            </div>
            <div class="kk-modal__cols">
                <div class="kk-modal__col">
                    <div class="kk-modal__col-title">IDENTITAS UTAMA &amp; BIODATA</div>
                    <table class="kk-modal-table">
                        <tr>
                            <td class="kk-modal-table__label">NIK</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-nik">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Nama Lengkap</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-namalengkap">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Tempat, Tanggal Lahir</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-ttl">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Jenis Kelamin</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-jk">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Agama</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-agama">-</td>
                        </tr>
                    </table>
                </div>
                <div class="kk-modal__col">
                    <div class="kk-modal__col-title">HUBUNGAN &amp; SOSIAL</div>
                    <table class="kk-modal-table">
                        <tr>
                            <td class="kk-modal-table__label">No. Kartu Keluarga</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-nokk">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Hubungan Keluarga</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-hubungan">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Pendidikan Terakhir</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-pendidikan">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Pekerjaan</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-pekerjaan">-</td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Status Warga</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val">
                                <span class="kk-badge-status kk-badge-status--aktif" style="font-size:11px;">&#9679; Hidup (Aktif)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="kk-modal-table__label">Alamat Lengkap</td>
                            <td class="kk-modal-table__sep">:</td>
                            <td class="kk-modal-table__val" id="modal-alamat">-</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="kk-modal__footer">
            <button type="button" class="kk-modal__btn-tutup" onclick="tutupDetailWarga()" id="btn-tutup-modal">
                <i class='bx bx-x'></i> Tutup
            </button>
            <button type="button" class="kk-modal__btn-edit" onclick="bukaModeEdit()" id="btn-edit-modal">
                <i class='bx bx-edit-alt'></i> Edit Data Warga Ini
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var dataWarga = {
    bambang: { nama: 'Bpk. Bambang Pamungkas', namalengkap: 'Bambang Pamungkas', nik: '3271048809920001', ttl: 'Depok, 15 Agustus 1988', jk: 'Laki-Laki', agama: 'Islam', nokk: '3271048809920001', hubungan: 'Kepala Keluarga', pendidikan: 'S1 Teknik Informatika', pekerjaan: 'Karyawan Swasta', alamat: 'Blok B No. 14, RT 04 / RW 08' },
    siti: { nama: 'Ibu Siti Aminah', namalengkap: 'Siti Aminah', nik: '3271046204830002', ttl: 'Jakarta, 12 April 1983', jk: 'Perempuan', agama: 'Islam', nokk: '3271048809920001', hubungan: 'Istri', pendidikan: 'SMA / Sederajat', pekerjaan: 'Ibu Rumah Tangga', alamat: 'Blok B No. 14, RT 04 / RW 08' },
    dimas: { nama: 'Dimas Arya Pratama', namalengkap: 'Dimas Arya Pratama', nik: '3271042006060003', ttl: 'Depok, 20 Juni 2006', jk: 'Laki-Laki', agama: 'Islam', nokk: '3271048809920001', hubungan: 'Anak', pendidikan: 'SMA / Sederajat', pekerjaan: 'Mahasiswa / Pelajar', alamat: 'Blok B No. 14, RT 04 / RW 08' },
    nabila: { nama: 'Nabila Putri Kirani', namalengkap: 'Nabila Putri Kirani', nik: '3271045011120004', ttl: 'Depok, 10 November 2012', jk: 'Perempuan', agama: 'Islam', nokk: '3271048809920001', hubungan: 'Anak', pendidikan: 'SMP / Sederajat', pekerjaan: 'Pelajar', alamat: 'Blok B No. 14, RT 04 / RW 08' }
};

var currentDetailKey = 'bambang';

function bukaDetailWarga(key) {
    currentDetailKey = key;
    var d = dataWarga[key]; if (!d) return;
    document.getElementById('modal-nama').textContent = d.nama;
    document.getElementById('modal-namalengkap').textContent = d.namalengkap;
    document.getElementById('modal-nik').textContent = d.nik;
    document.getElementById('modal-ttl').textContent = d.ttl;
    document.getElementById('modal-jk').textContent = d.jk;
    document.getElementById('modal-agama').textContent = d.agama;
    document.getElementById('modal-nokk').textContent = d.nokk;
    document.getElementById('modal-hubungan').textContent = d.hubungan;
    document.getElementById('modal-pendidikan').textContent = d.pendidikan;
    document.getElementById('modal-pekerjaan').textContent = d.pekerjaan;
    document.getElementById('modal-alamat').textContent = d.alamat;
    document.getElementById('modal-detail-overlay').classList.add('kk-modal-overlay--open');
    document.body.style.overflow = 'hidden';
}
function tutupDetailWarga() {
    document.getElementById('modal-detail-overlay').classList.remove('kk-modal-overlay--open');
    document.body.style.overflow = '';
}
function tutupModal(e) { if (e.target.id === 'modal-detail-overlay') tutupDetailWarga(); }
function bukaModeEdit() {
    tutupDetailWarga();
    window.location.href = "{{ route('warga.keluarga.edit') }}?target=" + encodeURIComponent(currentDetailKey);
}
function switchTab(tab) {
    document.getElementById('panel-daftar').style.display = tab === 'daftar' ? 'block' : 'none';
    document.getElementById('panel-riwayat').style.display = tab === 'riwayat' ? 'block' : 'none';
    document.getElementById('tab-daftar').classList.toggle('kk-tab-btn--active', tab === 'daftar');
    document.getElementById('tab-riwayat').classList.toggle('kk-tab-btn--active', tab === 'riwayat');
}
function filterAnggota(q) {
    q = (q||'').toLowerCase().trim();
    var hub = document.getElementById('filter-hubungan').value;
    var rows = document.querySelectorAll('#tbody-anggota .anggota-row');
    var count = 0;
    rows.forEach(function(r){
        var ok = (!q || r.dataset.nama.includes(q) || r.dataset.nik.includes(q)) && (!hub || r.dataset.hubungan === hub);
        r.style.display = ok ? '' : 'none'; if (ok) count++;
    });
    document.getElementById('row-empty-anggota').style.display = count===0 ? '' : 'none';
}
function copyNoKK() {
    navigator.clipboard.writeText('3271048809920001').then(function() {
        var btn = document.getElementById('btn-copy-nkk');
        btn.innerHTML = "<i class='bx bx-check'></i>"; btn.style.color='#28a745';
        setTimeout(function(){ btn.innerHTML="<i class='bx bx-copy'></i>"; btn.style.color=''; }, 1800);
    });
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') tutupDetailWarga(); });
</script>
@endpush
