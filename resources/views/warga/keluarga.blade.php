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
            <div class="kk-stat-card__number">{{ $totalJiwa }}</div>
            <div class="kk-stat-card__label">Total Jiwa Terdaftar</div>
            <i class='bx bxs-group kk-stat-card__watermark'></i>
        </div>
    </div>
    <div class="kk-stat-card {{ $totalMenunggu > 0 ? 'kk-stat-card--amber' : 'kk-stat-card--gray' }}">
        <div class="kk-stat-card__body">
            <div class="kk-stat-card__number">{{ $totalMenunggu }}</div>
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
                {{ $keluarga?->no_kk ?? '-' }}
                @if($keluarga?->no_kk)
                <button type="button" class="kk-copy-btn" onclick="copyNoKK('{{ $keluarga->no_kk }}')" title="Salin Nomor KK" id="btn-copy-nkk">
                    <i class='bx bx-copy'></i>
                </button>
                @endif
            </div>
        </div>
        <div class="kk-info-divider"></div>
        <div class="kk-info-item">
            <div class="kk-info-item__label">KEPALA KELUARGA</div>
            <div class="kk-info-item__value">
                <i class='bx bxs-user-check' style="color:#007bff; font-size:15px;"></i>
                {{ $kepala?->nama ?? '-' }}
            </div>
        </div>
        <div class="kk-info-divider"></div>
        <div class="kk-info-item">
            <div class="kk-info-item__label">ALAMAT &amp; DOMISILI</div>
            <div class="kk-info-item__value" style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                <span>{{ $alamatKeluarga }}</span>
                <div style="display:inline-flex;gap:5px;align-items:center;flex-shrink:0;">
                    <span class="kk-badge kk-badge--tetap">{{ strtoupper(str_replace('_', ' ', $keluarga?->status_tinggal ?? 'TETAP')) }}</span>
                    <span class="kk-badge kk-badge--mampu">{{ strtoupper(str_replace('_', ' ', $keluarga?->status_ekonomi ?? 'MAMPU')) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Tab Panel ── --}}
<div class="kk-tabpanel">
    <div class="kk-tab-header">
        <button class="kk-tab-btn kk-tab-btn--active" id="tab-daftar" onclick="switchTab('daftar')" type="button">
            <i class='bx bxs-group'></i>
            <span class="kk-tab-btn-text-desktop">Daftar Anggota Keluarga</span>
            <span class="kk-tab-btn-text-mobile">Anggota Keluarga</span>
        </button>
        <button class="kk-tab-btn" id="tab-riwayat" onclick="switchTab('riwayat')" type="button">
            <i class='bx bx-history'></i>
            <span class="kk-tab-btn-text-desktop">Riwayat Pengajuan Perubahan Data</span>
            <span class="kk-tab-btn-text-mobile">Riwayat Pengajuan</span>
            @if($totalMenunggu > 0)
            <span class="kk-tab-dot" title="Ada {{ $totalMenunggu }} pengajuan status menunggu"></span>
            @endif
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
                    <option value="orang_tua">Orang Tua</option>
                    <option value="famili_lain">Famili Lain</option>
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
                    @forelse($anggotaKeluarga as $idx => $warga)
                    <tr class="anggota-row" data-hubungan="{{ $warga->status_hubungan }}" data-nama="{{ strtolower($warga->nama) }}" data-nik="{{ $warga->nik }}">
                        <td style="text-align:center; color:#495057; font-weight:600;">{{ $loop->iteration }}</td>
                        <td>
                            <div class="kk-name">{{ $warga->nama }}</div>
                            <div class="kk-nik">NIK: <a href="javascript:void(0)" class="kk-nik-link" onclick="bukaDetailWarga('{{ $warga->nik }}')">{{ $warga->nik }}</a></div>
                        </td>
                        <td>
                            @if($warga->jenis_kelamin === 'laki_laki')
                                <span class="kk-gender-m">Laki-Laki</span>
                            @else
                                <span class="kk-gender-f">Perempuan</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $hubClass = match($warga->status_hubungan) {
                                    'kepala_keluarga' => 'kk-badge-hub--kk',
                                    'istri' => 'kk-badge-hub--istri',
                                    'anak' => 'kk-badge-hub--anak',
                                    default => 'kk-badge-hub--anak'
                                };
                            @endphp
                            <span class="kk-badge-hub {{ $hubClass }}">
                                {{ ucwords(str_replace('_', ' ', $warga->status_hubungan)) }}
                            </span>
                        </td>
                        <td style="color:#495057;">
                            {{ $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td style="color:#495057;">
                            {{ $warga->pekerjaan ?? '-' }}
                        </td>
                        <td>
                            <span class="kk-badge-status kk-badge-status--aktif">&#9679; {{ $warga->status_warga == 'hidup' ? 'Tetap / Aktif' : ucfirst($warga->status_warga) }}</span>
                        </td>
                        <td>
                            <div class="kk-aksi-wrap">
                                <button type="button" class="kk-btn-detail" onclick="bukaDetailWarga('{{ $warga->nik }}')" id="btn-detail-{{ $warga->nik }}">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <a href="{{ route('warga.keluarga.edit', ['nik' => $warga->nik]) }}" class="kk-btn-ubah" id="btn-ubah-{{ $warga->nik }}" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
                                    <i class='bx bx-edit'></i> Ubah
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:36px 18px; color:#6c757d; font-size:13px;">
                            <i class='bx bxs-group' style="font-size:30px; display:block; margin-bottom:8px; opacity:0.35;"></i>
                            Belum ada data anggota keluarga terdaftar.
                        </td>
                    </tr>
                    @endforelse
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
            <span>Menampilkan {{ $anggotaKeluarga->count() }} dari {{ $anggotaKeluarga->count() }} anggota keluarga terdaftar &nbsp;<span class="kk-pagination"><span class="kk-page-btn kk-page-btn--active">1</span></span></span>
        </div>
    </div>

    {{-- TAB 2: Riwayat Pengajuan --}}
    <div class="kk-tab-content" id="panel-riwayat" style="display:none;">
        <div class="kk-toolbar">
            <div class="kk-toolbar__left">
                <div class="kk-search-wrap">
                    <i class='bx bx-search kk-search-wrap__icon'></i>
                    <input type="text" id="search-riwayat" class="kk-search-input" placeholder="Cari target warga..." oninput="filterRiwayat()">
                </div>
                <select class="kk-filter-select" id="filter-jenis-riwayat" onchange="filterRiwayat()">
                    <option value="">Semua Jenis Pengajuan</option>
                    <option value="ubah_data">Ubah Data</option>
                    <option value="tambah_anggota">Tambah Anggota</option>
                </select>
                <select class="kk-filter-select" id="filter-status-riwayat" onchange="filterRiwayat()">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
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
                <tbody id="tbody-riwayat">
                    @forelse($riwayatPengajuan as $idx => $r)
                    @php
                        $targetNama = $r->warga?->nama ?? ($r->data_baru['nama'] ?? 'Anggota Baru');
                        $jenisLabel = $r->jenis_pengajuan === 'tambah_anggota' ? 'Tambah Anggota' : 'Ubah Data';
                        $jenisClass = $r->jenis_pengajuan === 'tambah_anggota' ? 'kk-badge-jenis--tambah' : 'kk-badge-jenis--ubah';
                        $statusDotClass = match($r->status) {
                            'disetujui' => 'kk-badge-rstatus--disetujui',
                            'ditolak' => 'kk-badge-rstatus--ditolak',
                            default => 'kk-badge-rstatus--menunggu'
                        };
                        $statusLabel = match($r->status) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => 'Menunggu'
                        };
                    @endphp
                    <tr class="riwayat-row" data-jenis="{{ $r->jenis_pengajuan }}" data-status="{{ $r->status }}" data-nama="{{ strtolower($targetNama) }}">
                        <td style="text-align:center; color:#495057; font-weight:600;">{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight:700; color:#212529; font-size:12.5px;">{{ $r->created_at->translatedFormat('d M Y') }}</div>
                            <div style="font-size:11.5px; color:#6c757d; margin-top:2px;">{{ $r->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td><span class="kk-badge-jenis {{ $jenisClass }}">{{ $jenisLabel }}</span></td>
                        <td style="font-weight:600; color:#212529;">{{ $targetNama }}</td>
                        <td>
                            @if(!empty($r->bukti_path))
                                <a href="javascript:void(0)" class="kk-file-link" onclick="bukaModalLihatPengajuan({{ $r->id }})" title="Klik untuk pratinjau berkas">
                                    <i class='bx bx-file-blank'></i> {{ basename($r->bukti_path) }}
                                </a>
                            @else
                                <span style="color:#94a3b8; font-size:12px; font-style:italic;">Tidak ada berkas</span>
                            @endif
                        </td>
                        <td><span class="kk-badge-rstatus {{ $statusDotClass }}"><span class="kk-status-dot">●</span> {{ $statusLabel }}</span></td>
                        <td style="color:{{ $r->status === 'ditolak' ? '#dc3545' : '#6c757d' }}; font-size:12.5px; font-weight:{{ $r->status === 'ditolak' ? '500' : 'normal' }};">
                            {{ $r->catatan_admin ?? ($r->status === 'ditolak' ? $r->alasan : ($r->alasan ?? '-')) }}
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="kk-btn-lihat {{ $r->status === 'disetujui' ? 'kk-btn-lihat--green' : '' }}" id="btn-lihat-{{ $r->id }}" onclick="bukaModalLihatPengajuan({{ $r->id }})">
                                @if($r->status === 'disetujui')
                                    <i class='bx bx-check-circle'></i> Selesai
                                @elseif($r->status === 'ditolak')
                                    <i class='bx bx-info-circle'></i> Lihat Catatan
                                @else
                                    <i class='bx bx-show'></i> Lihat Perubahan
                                @endif
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:36px 18px; color:#6c757d; font-size:13px;">
                            <i class='bx bx-history' style="font-size:30px; display:block; margin-bottom:8px; opacity:0.35;"></i>
                            Belum ada riwayat pengajuan perubahan data keluarga.
                        </td>
                    </tr>
                    @endforelse
                    <tr id="row-empty-riwayat" style="display:none;">
                        <td colspan="8" style="text-align:center; padding:36px 18px; color:#6c757d; font-size:13px;">
                            <i class='bx bx-search' style="font-size:30px; display:block; margin-bottom:8px; opacity:0.35;"></i>
                            Tidak ada riwayat yang sesuai pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="kk-table-footer">
            <span></span>
            <span>Menampilkan {{ $riwayatPengajuan->count() }} dari {{ $riwayatPengajuan->count() }} entri &nbsp;<span class="kk-pagination"><span class="kk-page-btn kk-page-btn--active">1</span></span></span>
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

{{-- ══ MODAL LIHAT PENGAJUAN PERUBAHAN DATA (POV WARGA) ══ --}}
<div class="kk-modal-overlay" id="modal-pengajuan-overlay" onclick="tutupPengajuanViaOverlay(event)">
    <div class="kk-pengajuan-modal" id="modal-pengajuan-container" role="dialog" aria-modal="true" aria-labelledby="modal-pengajuan-title">
        <!-- Header -->
        <div class="kk-pengajuan-modal__header">
            <div>
                <div class="kk-pengajuan-modal__title-row">
                    <h2 class="kk-pengajuan-modal__title" id="modal-pengajuan-title">Review Verifikasi Pengajuan</h2>
                    <span class="kk-pengajuan-modal__status-badge kk-pengajuan-modal__status-badge--menunggu" id="modal-p-status-badge">Menunggu Persetujuan RT</span>
                </div>
                <div class="kk-pengajuan-modal__subtitle">
                    Pemohon: <strong>{{ $kepala?->nama ?? '-' }}</strong> &bull; No. KK: <strong>{{ $keluarga?->no_kk ?? '-' }}</strong> &bull; {{ $alamatKeluarga }}
                </div>
            </div>
            <button type="button" class="kk-pengajuan-modal__close" onclick="tutupModalLihatPengajuan()" aria-label="Tutup" id="btn-pengajuan-close">
                <i class='bx bx-x'></i>
            </button>
        </div>

        <!-- Body -->
        <div class="kk-pengajuan-modal__body">
            <!-- Strip Target Data -->
            <div class="kk-target-strip">
                <div class="kk-target-strip__left">
                    <div class="kk-target-strip__title" id="modal-p-target-title">Target Data: -</div>
                    <div class="kk-target-strip__sub" id="modal-p-target-sub">-</div>
                </div>
                <div class="kk-target-strip__date" id="modal-p-tgl">
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
                        <span class="kk-pengajuan-card__badge-green" id="modal-p-attr-count">0 Atribut Diperbarui</span>
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
                            <tbody id="modal-p-table-body">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Alasan Pengajuan -->
                    <div class="kk-alasan-box">
                        <div class="kk-alasan-box__label">Alasan Pengajuan Perubahan:</div>
                        <div class="kk-alasan-box__val" id="modal-p-alasan">-</div>
                    </div>

                    <!-- Catatan RT (Jika Ditolak atau Ada Catatan) -->
                    <div class="kk-catatan-rt-box" id="modal-p-catatan-rt-box" style="display:none;">
                        <div class="kk-catatan-rt-box__label"><i class='bx bx-error-circle'></i> Catatan Verifikasi Pengurus RT:</div>
                        <div class="kk-catatan-rt-box__val" id="modal-p-catatan-rt">-</div>
                    </div>
                </div>

                <!-- Kolom Kanan: Pratinjau Dokumen Pendukung -->
                <div class="kk-pengajuan-card">
                    <div class="kk-pengajuan-card__header">
                        <div class="kk-pengajuan-card__title">
                            <i class='bx bx-check-shield' style="color:#16a34a; font-size:16px;"></i>
                            <span>Pratinjau Dokumen Pendukung</span>
                        </div>
                        <span class="kk-pengajuan-card__badge-green" id="modal-p-doc-count">0 Dokumen</span>
                    </div>

                    <!-- Tabs Dokumen -->
                    <div class="kk-doc-tabs" id="modal-p-doc-tabs">
                        <!-- Dynamic tabs -->
                    </div>

                    <!-- Visual Document Box Serupa Admin -->
                    <div class="kk-doc-preview-box">
                        <div class="kk-doc-display-container">
                            <img id="modal-p-doc-img" src="" alt="Pratinjau Dokumen" class="kk-doc-preview-img" onclick="bukaZoomDokumen()" title="Klik untuk memperbesar gambar" style="display:none;">
                            <div id="modal-p-doc-fallback" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:36px 16px;color:#94a3b8;width:100%;">
                                <i class='bx bx-file' id="modal-p-doc-none-icon" style="font-size:52px;color:#cbd5e1;margin-bottom:8px;"></i>
                                <span id="modal-p-doc-fallback-text" style="font-size:13px;font-weight:600;">Tidak ada berkas bukti yang diunggah</span>
                            </div>
                        </div>

                        <div class="kk-doc-info-bar">
                            <span id="modal-p-doc-filename" style="font-size: 12px; color: #475569; font-weight: 600;">Dokumen</span>
                            <span id="modal-p-doc-verified" style="font-size: 11.5px; color: #16a34a; font-weight: 600;">
                                <i class='bx bx-check-circle'></i> Dokumen Asli Terverifikasi
                            </span>
                        </div>

                        <!-- Action buttons for document -->
                        <div class="kk-doc-actions">
                            <button type="button" class="kk-doc-btn" onclick="bukaZoomDokumen()" id="btn-perbesar-berkas">
                                <i class='bx bx-zoom-in'></i> Perbesar Berkas
                            </button>
                            <a id="btn-unduh-berkas" href="#" download class="kk-doc-btn">
                                <i class='bx bx-download'></i> Unduh File Asli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer POV Warga -->
        <div class="kk-pengajuan-modal__footer">
            <button type="button" class="kk-pbtn-close" onclick="tutupModalLihatPengajuan()">
                Tutup / Batal
            </button>
            <div class="kk-pengajuan-modal__footer-btns">
                <div class="kk-pengajuan-modal__footer-status" id="modal-p-footer-status">
                    <i class='bx bx-info-circle' style="color:#0284c7;"></i>
                    <span>Pengajuan ini sedang dalam antrean verifikasi oleh Pengurus RT 04.</span>
                </div>
                <a href="{{ route('warga.keluarga.edit') }}" class="kk-pbtn-reapply" id="modal-p-btn-reapply" style="display:none;">
                    <i class='bx bx-refresh'></i> Ajukan Ulang Perubahan
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var dataWarga = @json($dataWargaMap);
var dataPengajuan = @json($dataPengajuanMap);
var currentDetailKey = Object.keys(dataWarga)[0] || '';
var currentPengajuanId = Object.keys(dataPengajuan)[0] || null;
var currentDocIdx = 0;

function bukaDetailWarga(key) {
    currentDetailKey = key;
    var d = dataWarga[key]; 
    if (!d) return;
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

function tutupModal(e) { 
    if (e.target.id === 'modal-detail-overlay') tutupDetailWarga(); 
}

function bukaModeEdit() {
    tutupDetailWarga();
    if (currentDetailKey) {
        window.location.href = "{{ route('warga.keluarga.edit') }}?nik=" + encodeURIComponent(currentDetailKey);
    } else {
        window.location.href = "{{ route('warga.keluarga.edit') }}";
    }
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
        r.style.display = ok ? '' : 'none'; 
        if (ok) count++;
    });
    var emptyRow = document.getElementById('row-empty-anggota');
    if (emptyRow) {
        emptyRow.style.display = (rows.length > 0 && count === 0) ? '' : 'none';
    }
}

function filterRiwayat() {
    var q = (document.getElementById('search-riwayat')?.value || '').toLowerCase().trim();
    var jenis = document.getElementById('filter-jenis-riwayat')?.value || '';
    var status = document.getElementById('filter-status-riwayat')?.value || '';
    var rows = document.querySelectorAll('#tbody-riwayat .riwayat-row');
    var count = 0;
    rows.forEach(function(r){
        var ok = (!q || r.dataset.nama.includes(q)) && 
                 (!jenis || r.dataset.jenis === jenis) && 
                 (!status || r.dataset.status === status);
        r.style.display = ok ? '' : 'none'; 
        if (ok) count++;
    });
    var emptyRow = document.getElementById('row-empty-riwayat');
    if (emptyRow) {
        emptyRow.style.display = (rows.length > 0 && count === 0) ? '' : 'none';
    }
}

function copyNoKK(noKk) {
    if (!noKk) noKk = '{{ $keluarga?->no_kk ?? "" }}';
    if (!noKk) return;
    navigator.clipboard.writeText(noKk).then(function() {
        var btn = document.getElementById('btn-copy-nkk');
        if (btn) {
            btn.innerHTML = "<i class='bx bx-check'></i>"; 
            btn.style.color = '#28a745';
            setTimeout(function(){ 
                btn.innerHTML = "<i class='bx bx-copy'></i>"; 
                btn.style.color = ''; 
            }, 1800);
        }
    });
}

function bukaModalLihatPengajuan(id) {
    var p = dataPengajuan[id];
    if (!p) return;
    currentPengajuanId = id;
    currentDocIdx = 0;

    // Status Badge
    var badgeEl = document.getElementById('modal-p-status-badge');
    badgeEl.textContent = p.statusLabel;
    badgeEl.className = 'kk-pengajuan-modal__status-badge kk-pengajuan-modal__status-badge--' + p.status;

    // Target info
    document.getElementById('modal-p-target-title').textContent = p.targetTitle;
    document.getElementById('modal-p-target-sub').textContent = p.targetSub;
    document.getElementById('modal-p-tgl').textContent = p.tgl;

    // Atribut count
    document.getElementById('modal-p-attr-count').textContent = p.attrCount;

    // Komparasi Table
    var tbody = document.getElementById('modal-p-table-body');
    var rowsHtml = '';
    p.komparasi.forEach(function(item) {
        rowsHtml += '<tr>' +
            '<td class="kk-ptable-field">' + item.field + '</td>' +
            '<td class="kk-ptable-old">' +
                '<span class="kk-mobile-row-label">Data Lama:</span>' +
                '<span>' + item.old + '</span>' +
            '</td>' +
            '<td class="kk-ptable-new">' +
                '<span class="kk-mobile-row-label">Usulan Baru:</span>' +
                (item.changed ? 
                    ('<div class="kk-val-new"><span>' + item.new + '</span><span class="kk-badge-diubah">Diubah</span></div>') : 
                    ('<span class="kk-ptable-old">' + item.new + '</span>')
                ) +
            '</td>' +
        '</tr>';
    });
    tbody.innerHTML = rowsHtml;

    // Alasan Pengajuan
    document.getElementById('modal-p-alasan').textContent = p.alasan;

    // Catatan RT
    var catBox = document.getElementById('modal-p-catatan-rt-box');
    if (p.catatanRT && p.catatanRT.trim() !== '') {
        document.getElementById('modal-p-catatan-rt').textContent = p.catatanRT;
        catBox.style.display = 'block';
    } else {
        catBox.style.display = 'none';
    }

    // Dokumen Tabs & Content
    document.getElementById('modal-p-doc-count').textContent = (p.docs ? p.docs.length : 0) + ' Dokumen';
    renderDocTabs(p.docs || [], 0);
    var imgEl = document.getElementById('modal-p-doc-img');
    var fallbackEl = document.getElementById('modal-p-doc-fallback');
    var verEl = document.getElementById('modal-p-doc-verified');
    var btnPerbesar = document.getElementById('btn-perbesar-berkas');
    var btnUnduh = document.getElementById('btn-unduh-berkas');

    if (p.docs && p.docs.length > 0) {
        if (imgEl) imgEl.style.display = 'block';
        if (fallbackEl) fallbackEl.style.display = 'none';
        if (btnPerbesar) btnPerbesar.style.display = 'inline-flex';
        if (btnUnduh) btnUnduh.style.display = 'inline-flex';
        renderDocPreview(p.docs[0]);
    } else {
        if (imgEl) imgEl.style.display = 'none';
        if (fallbackEl) fallbackEl.style.display = 'flex';
        document.getElementById('modal-p-doc-filename').textContent = 'Tidak ada dokumen';
        if (verEl) {
            verEl.style.color = '#dc2626';
            verEl.innerHTML = "<i class='bx bx-error-circle'></i> Belum Terlampir";
        }
        if (btnPerbesar) btnPerbesar.style.display = 'none';
        if (btnUnduh) btnUnduh.style.display = 'none';
    }

    // Footer Status & Tombol Reapply
    var footerStatusEl = document.getElementById('modal-p-footer-status');
    var iconClass = p.status === 'disetujui' ? 'bx-check-circle' : (p.status === 'ditolak' ? 'bx-error-circle' : 'bx-time-five');
    var iconColor = p.status === 'disetujui' ? '#16a34a' : (p.status === 'ditolak' ? '#dc2626' : '#d97706');
    footerStatusEl.innerHTML = "<i class='bx " + iconClass + "' style='color:" + iconColor + "; font-size:16px;'></i> <span>" + p.footerText + "</span>";

    var reapplyBtn = document.getElementById('modal-p-btn-reapply');
    if (p.status === 'ditolak') {
        reapplyBtn.style.display = 'inline-flex';
    } else {
        reapplyBtn.style.display = 'none';
    }

    // Tampilkan Modal
    document.getElementById('modal-pengajuan-overlay').classList.add('kk-modal-overlay--open');
    document.body.style.overflow = 'hidden';
}

function renderDocTabs(docs, activeIdx) {
    var tabsContainer = document.getElementById('modal-p-doc-tabs');
    var html = '';
    docs.forEach(function(doc, idx) {
        var activeClass = idx === activeIdx ? 'kk-doc-tab-btn--active' : '';
        var iconColorStyle = doc.iconColor ? 'style="color:' + doc.iconColor + ';"' : '';
        html += '<button type="button" class="kk-doc-tab-btn ' + activeClass + '" onclick="switchDocPreview(' + idx + ')">' +
            "<i class='bx " + doc.icon + "' " + iconColorStyle + "></i> " + doc.label +
        '</button>';
    });
    tabsContainer.innerHTML = html;
}

function switchDocPreview(idx) {
    currentDocIdx = idx;
    var p = dataPengajuan[currentPengajuanId];
    if (!p || !p.docs[idx]) return;
    renderDocTabs(p.docs, idx);
    renderDocPreview(p.docs[idx]);
}

function renderDocPreview(doc) {
    var imgEl = document.getElementById('modal-p-doc-img');
    imgEl.src = doc.src;
    imgEl.alt = doc.label;
    document.getElementById('modal-p-doc-filename').textContent = doc.label;
    
    var btnUnduh = document.getElementById('btn-unduh-berkas');
    if (btnUnduh) {
        btnUnduh.href = doc.src;
        btnUnduh.setAttribute('download', doc.label);
    }
    
    var verEl = document.getElementById('modal-p-doc-verified');
    if (doc.verified.includes('Perlu Unggah')) {
        verEl.style.color = '#dc2626';
        verEl.innerHTML = "<i class='bx bx-error-circle'></i> " + doc.verified;
    } else {
        verEl.style.color = '#16a34a';
        verEl.innerHTML = "<i class='bx bx-check-circle'></i> " + doc.verified;
    }
}

function bukaZoomDokumen() {
    var imgEl = document.getElementById('modal-p-doc-img');
    if (imgEl && imgEl.src) {
        window.open(imgEl.src, '_blank');
    }
}

function tutupModalLihatPengajuan() {
    document.getElementById('modal-pengajuan-overlay').classList.remove('kk-modal-overlay--open');
    document.body.style.overflow = '';
}

function tutupPengajuanViaOverlay(e) {
    if (e.target.id === 'modal-pengajuan-overlay') {
        tutupModalLihatPengajuan();
    }
}

document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') {
        tutupDetailWarga();
        tutupModalLihatPengajuan();
    }
});
</script>
@endpush
