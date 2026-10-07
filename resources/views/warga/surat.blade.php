@extends('layouts.warga')

@section('title', 'Layanan Pengajuan Surat')
@section('meta_description', 'Layanan Pengajuan Surat Pengantar RT 04 Mandiri secara Online — WargaKu')

@section('topbar_section')
    <a href="{{ route('warga.beranda') }}" class="admin-topbar__breadcrumb-link">Beranda</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Pengajuan Surat</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-surat.css') }}">
@endpush

@section('content')

{{-- ── Breadcrumb & Header ── --}}
<div class="warga-surat-page">
    
    <div style="margin-bottom: 22px;">
        <nav style="font-size:12px;color:#6c757d;margin-bottom:6px;display:flex;align-items:center;gap:6px;" aria-label="Breadcrumb">
            <a href="{{ route('warga.beranda') }}" style="color:#6c757d;text-decoration:none;">Beranda</a>
            <span>/</span>
            <span style="color:#6c757d;">Layanan Warga</span>
            <span>/</span>
            <span style="color:#007bff;font-weight:600;">Pengajuan Surat Pengantar</span>
        </nav>
        <h1 style="font-size:22px;font-weight:800;color:#111827;margin-bottom:4px;">
            Layanan Pengajuan Surat Pengantar RT 04
        </h1>
        <p style="font-size:13px;color:#6c757d;margin:0;">
            Ajukan surat pengantar resmi ke Pengurus RT 04 / RW 08 secara online terintegrasi tabel <code>pengajuan_surat</code>, pantau status real-time, dan unduh e-surat resmi.
        </p>
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

    {{-- ── 3 Big Stat Cards Grid (Screenshot 1 & 2) ── --}}
    <div class="ws-stat-grid">
        
        {{-- Card 1: Menunggu Verifikasi --}}
        <div class="ws-stat-card ws-stat-card--yellow">
            <div class="ws-stat-card__body">
                <div class="ws-stat-card__number" id="stat-menunggu">1</div>
                <div class="ws-stat-card__label">SURAT MENUNGGU VERIFIKASI</div>
                <i class='bx bx-hourglass ws-stat-card__watermark'></i>
            </div>
            <a href="javascript:void(0)" onclick="filterByStatus('menunggu')" class="ws-stat-card__footer">
                <span>Lihat surat antrean RT</span>
                <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>

        {{-- Card 2: Disetujui & Terbit --}}
        <div class="ws-stat-card ws-stat-card--green">
            <div class="ws-stat-card__body">
                <div class="ws-stat-card__number" id="stat-disetujui">4</div>
                <div class="ws-stat-card__label">SURAT DISETUJUI &amp; TERBIT</div>
                <i class='bx bx-badge-check ws-stat-card__watermark'></i>
            </div>
            <a href="javascript:void(0)" onclick="filterByStatus('disetujui')" class="ws-stat-card__footer">
                <span>Siap unduh e-surat PDF</span>
                <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>

        {{-- Card 3: Perlu Revisi / Ditolak --}}
        <div class="ws-stat-card ws-stat-card--red">
            <div class="ws-stat-card__body">
                <div class="ws-stat-card__number" id="stat-ditolak">1</div>
                <div class="ws-stat-card__label">SURAT PERLU REVISI / DITOLAK</div>
                <i class='bx bx-x-circle ws-stat-card__watermark'></i>
            </div>
            <a href="javascript:void(0)" onclick="filterByStatus('ditolak')" class="ws-stat-card__footer">
                <span>Lihat catatan penolakan</span>
                <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>

    </div>

    {{-- ── Tab Navigation ── --}}
    <div class="ws-tab-nav">
        <button type="button" class="ws-tab-btn ws-tab-btn--active" id="tab-riwayat-btn" onclick="switchSuratTab('riwayat')">
            <i class='bx bx-history'></i>
            <span>Riwayat &amp; Status Pengajuan</span>
        </button>
        <button type="button" class="ws-tab-btn" id="tab-buat-btn" onclick="switchSuratTab('buat')">
            <i class='bx bx-edit'></i>
            <span>Buat Pengajuan Surat Baru</span>
        </button>
    </div>

    {{-- ── Tab Content Container ── --}}
    <div class="ws-tab-content-wrapper">
        
        {{-- ══════════════════════════════════════════════════════
             TAB 1: RIWAYAT & STATUS PENGAJUAN (SCREENSHOT 1)
             ══════════════════════════════════════════════════════ --}}
        <div id="tab-riwayat-content">
            
            {{-- Toolbar --}}
            <div class="ws-toolbar">
                <div>
                    <div class="ws-toolbar__title">Daftar Riwayat Pengajuan Surat Pengantar</div>
                    <div class="ws-toolbar__sub">Sinkronisasi langsung data tabel <code>pengajuan_surat</code> keluarga Bambang Pamungkas</div>
                </div>
                <div class="ws-toolbar__actions">
                    <select class="ws-filter-select" id="filter-status-select" onchange="applyFilters()">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu Verifikasi</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>

                    <div class="ws-search-wrap">
                        <i class='bx bx-search ws-search-icon'></i>
                        <input type="text" id="search-surat-input" class="ws-search-input" placeholder="Cari No. Surat / Pemohon..." oninput="applyFilters()">
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="ws-table-wrap">
                <table class="ws-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">TGL PENGAJUAN</th>
                            <th style="width: 220px;">PEMOHON / WARGA TUJUAN</th>
                            <th>KEPERLUAN / ISI SURAT</th>
                            <th style="width: 210px;">NOMOR SURAT</th>
                            <th style="width: 190px;">STATUS</th>
                            <th style="width: 230px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-riwayat-surat">
                        
                        {{-- Row 1: Menunggu Verifikasi (Siti Aminah) --}}
                        <tr class="ws-row-item" data-status="menunggu" data-pemohon="siti aminah" data-nosurat="" data-keperluan="pengantar permohonan surat keterangan catatan kepolisian skck">
                            <td style="font-weight: 600; color: #475569;">22 Okt 2025</td>
                            <td>
                                <strong style="color: #0f172a; font-size: 14px;">Siti Aminah</strong>
                            </td>
                            <td>
                                <div style="line-height: 1.5; color: #334155; font-size: 13.5px;">
                                    Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan rekrutmen BUMN
                                </div>
                            </td>
                            <td>
                                <span style="color: #94a3b8; font-weight: 700; font-size: 16px; padding-left: 4px;">—</span>
                            </td>
                            <td>
                                <span class="ws-badge-status ws-badge-status--menunggu">
                                    <i class='bx bx-time-five'></i> Menunggu Verifikasi
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-start; gap: 8px;">
                                    <button type="button" class="ws-btn-preview-outline" onclick="bukaPreviewDraft('Siti Aminah', 'Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan rekrutmen BUMN')">
                                        <i class='bx bx-show'></i>
                                        <span>Preview</span>
                                    </button>
                                    <button type="button" class="ws-btn-batal-outline" onclick="konfirmasiBatalSurat(2)">
                                        <i class='bx bx-x-circle'></i>
                                        <span>Batalkan</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2: Disetujui (Dimas) --}}
                        <tr class="ws-row-item" data-status="disetujui" data-pemohon="dimas arya pratama" data-nosurat="470/882/rt04/x/2025" data-keperluan="pengantar permohonan surat keterangan catatan kepolisian skck">
                            <td style="font-weight: 600; color: #475569;">24 Okt 2025</td>
                            <td>
                                <strong style="color: #0f172a; font-size: 14px;">Dimas Arya Pratama</strong>
                            </td>
                            <td>
                                <div style="line-height: 1.5; color: #334155; font-size: 13.5px;">
                                    Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan rekrutmen BUMN
                                </div>
                            </td>
                            <td>
                                <span class="ws-badge-no-surat">470/882/RT04/X/2025</span>
                            </td>
                            <td>
                                <span class="ws-badge-status ws-badge-status--disetujui">
                                    <i class='bx bx-check-circle'></i> Disetujui
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-start;">
                                    <a href="{{ route('warga.surat.cetak') }}" class="ws-btn-esurat" title="Lihat dan Unduh Surat">
                                        <i class='bx bx-download'></i>
                                        <span>Unduh e-Surat</span>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 3: Ditolak (Bambang Pamungkas) --}}
                        <tr class="ws-row-item" data-status="ditolak" data-pemohon="bambang pamungkas" data-nosurat="" data-keperluan="pengantar permohonan surat keterangan catatan kepolisian skck">
                            <td style="font-weight: 600; color: #475569;">15 Okt 2025</td>
                            <td>
                                <strong style="color: #0f172a; font-size: 14px;">Bambang Pamungkas</strong>
                            </td>
                            <td>
                                <div style="line-height: 1.5; color: #334155; font-size: 13.5px;">
                                    Pengantar permohonan Surat Keterangan Catatan Kepolisian (SKCK) untuk persyaratan rekrutmen BUMN
                                </div>
                            </td>
                            <td>
                                <span style="color: #94a3b8; font-weight: 700; font-size: 16px; padding-left: 4px;">—</span>
                            </td>
                            <td>
                                <span class="ws-badge-status ws-badge-status--ditolak">
                                    <i class='bx bx-x-circle'></i> Ditolak
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-start;">
                                    <button type="button" class="ws-btn-alasan-outline" onclick="bukaAlasanPenolakan('Dokumen identitas pendukung belum lengkap dan lampiran tidak terbaca jelas. Silakan ajukan ulang dengan melampirkan berkas KTP/KK yang jelas.')">
                                        <i class='bx bx-error-circle'></i>
                                        <span>Lihat Alasan</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 4: Disetujui (Nabila Putri Kirani) --}}
                        <tr class="ws-row-item" data-status="disetujui" data-pemohon="nabila putri kirani" data-nosurat="470/731/rt04/ix/2025" data-keperluan="pengantar permohonan kartu identitas anak kia ke dinas dukcapil">
                            <td style="font-weight: 600; color: #475569;">08 Sep 2025</td>
                            <td>
                                <strong style="color: #0f172a; font-size: 14px;">Nabila Putri Kirani</strong>
                            </td>
                            <td>
                                <div style="line-height: 1.5; color: #334155; font-size: 13.5px;">
                                    Pengantar permohonan penerbitan Kartu Identitas Anak (KIA) ke Dinas Kependudukan dan Catatan Sipil
                                </div>
                            </td>
                            <td>
                                <span class="ws-badge-no-surat">470/731/RT04/IX/2025</span>
                            </td>
                            <td>
                                <span class="ws-badge-status ws-badge-status--disetujui">
                                    <i class='bx bx-check-circle'></i> Disetujui
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-start;">
                                    <a href="{{ route('warga.surat.cetak') }}" class="ws-btn-esurat" title="Lihat dan Unduh Surat">
                                        <i class='bx bx-download'></i>
                                        <span>Unduh e-Surat</span>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 5: Disetujui (Dimas Arya Pratama) --}}
                        <tr class="ws-row-item" data-status="disetujui" data-pemohon="dimas arya pratama" data-nosurat="470/624/rt04/viii/2025" data-keperluan="pengantar surat keterangan belum menikah untuk persyaratan beasiswa">
                            <td style="font-weight: 600; color: #475569;">19 Agu 2025</td>
                            <td>
                                <strong style="color: #0f172a; font-size: 14px;">Dimas Arya Pratama</strong>
                            </td>
                            <td>
                                <div style="line-height: 1.5; color: #334155; font-size: 13.5px;">
                                    Pengantar surat keterangan belum menikah untuk kelengkapan administrasi beasiswa pendidikan
                                </div>
                            </td>
                            <td>
                                <span class="ws-badge-no-surat">470/624/RT04/VIII/2025</span>
                            </td>
                            <td>
                                <span class="ws-badge-status ws-badge-status--disetujui">
                                    <i class='bx bx-check-circle'></i> Disetujui
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-start;">
                                    <a href="{{ route('warga.surat.cetak') }}" class="ws-btn-esurat" title="Lihat dan Unduh Surat">
                                        <i class='bx bx-download'></i>
                                        <span>Unduh e-Surat</span>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 6: Disetujui (Bambang Pamungkas) --}}
                        <tr class="ws-row-item" data-status="disetujui" data-pemohon="bambang pamungkas" data-nosurat="470/512/rt04/vii/2025" data-keperluan="pengantar pengurusan klaim jaminan hari tua jht bpjs ketenagakerjaan">
                            <td style="font-weight: 600; color: #475569;">04 Jul 2025</td>
                            <td>
                                <strong style="color: #0f172a; font-size: 14px;">Bambang Pamungkas</strong>
                            </td>
                            <td>
                                <div style="line-height: 1.5; color: #334155; font-size: 13.5px;">
                                    Pengantar pengurusan klaim jaminan hari tua (JHT) BPJS Ketenagakerjaan
                                </div>
                            </td>
                            <td>
                                <span class="ws-badge-no-surat">470/512/RT04/VII/2025</span>
                            </td>
                            <td>
                                <span class="ws-badge-status ws-badge-status--disetujui">
                                    <i class='bx bx-check-circle'></i> Disetujui
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; justify-content: flex-start;">
                                    <a href="{{ route('warga.surat.cetak') }}" class="ws-btn-esurat" title="Lihat dan Unduh Surat">
                                        <i class='bx bx-download'></i>
                                        <span>Unduh e-Surat</span>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr id="row-empty-surat" style="display: none;">
                            <td colspan="6" style="text-align: center; padding: 36px 20px; color: #6b7280;">
                                <i class='bx bx-folder-open' style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 6px;"></i>
                                <span>Tidak ditemukan riwayat pengajuan surat yang sesuai kriteria pencarian.</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            {{-- Table Footer Pagination --}}
            <div class="ws-table-footer">
                <span id="footer-summary-text">Menampilkan 1 sampai 6 dari 6 data riwayat <code>pengajuan_surat</code></span>
                <div class="ws-pagination" id="ws-pagination-container">
                    <button type="button" class="ws-page-btn" id="btn-prev-page" onclick="gantiHalamanSurat('prev')" disabled>
                        <i class='bx bx-chevron-left' style="font-size: 18px; margin-right: 4px;"></i>
                        <span>Sebelumnya</span>
                    </button>
                    <div id="pagination-numbers" style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" class="ws-page-btn ws-page-btn--active" onclick="gantiHalamanSurat(1)">1</button>
                    </div>
                    <button type="button" class="ws-page-btn" id="btn-next-page" onclick="gantiHalamanSurat('next')" disabled>
                        <span>Selanjutnya</span>
                        <i class='bx bx-chevron-right' style="font-size: 18px; margin-left: 4px;"></i>
                    </button>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════
             TAB 2: BUAT PENGAJUAN SURAT BARU (SCREENSHOT 2)
             ══════════════════════════════════════════════════════ --}}
        <div id="tab-buat-content" style="display: none;">
            
            <div class="ws-form-card">
                
                {{-- Header Biru --}}
                <div class="ws-form-card__header">
                    <i class='bx bx-notepad ws-form-card__icon'></i>
                    <div>
                        <h2 class="ws-form-card__title">Formulir Pengajuan Surat Pengantar RT</h2>
                        <p class="ws-form-card__sub">Tabel database <code>pengajuan_surat</code> • Pengurus RT 04 / RW 08 Sukamaju</p>
                    </div>
                </div>

                <div class="ws-form-card__body">
                    
                    <form id="form-pengajuan-surat" action="{{ route('warga.surat.store') }}" method="POST">
                        @csrf

                        {{-- Mode Pemilihan Data Pemohon --}}
                        <div class="ws-form-group">
                            <label class="ws-label" style="margin-bottom: 8px;">
                                <span>Mode Pemilihan Data Pemohon <span class="ws-req">*</span></span>
                            </label>
                            
                            <div class="ws-mode-box">
                                <div class="ws-mode-box__icon">
                                    <i class='bx bx-id-card'></i>
                                </div>
                                <div>
                                    <div class="ws-mode-box__title">Pilih Anggota Keluarga Terdaftar</div>
                                    <div class="ws-mode-box__desc">Otomatis terisi dari master database KK terverifikasi RT 04.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Pilih Anggota Keluarga (warga_tujuan_id) --}}
                        <div class="ws-form-group">
                            <div class="ws-form-header-row">
                                <label class="ws-label" for="select-warga-tujuan">
                                    <span>Pilih Anggota Keluarga (warga_tujuan_id) <span class="ws-req">*</span></span>
                                </label>
                                <span style="font-size: 11.5px; color: #64748b; font-weight: 600;">
                                    No. KK : <strong style="color: #0284c7;">3271048809920001</strong>
                                </span>
                            </div>
                            
                            <select id="select-warga-tujuan" name="warga_tujuan_id" class="ws-select" onchange="gantiTargetSurat(this.value)" required>
                                <option value="" disabled selected>-- Pilih Anggota Keluarga Pemohon --</option>
                                <option value="dimas">Dimas Arya Pratama (Anak Kandung - NIK: 3271048809920003)</option>
                                <option value="bambang">Bpk. Bambang Pamungkas (Kepala Keluarga - NIK: 3271041203780002)</option>
                                <option value="siti">Siti Aminah (Istri - NIK: 3271046204830002)</option>
                                <option value="nabila">Nabila Putri Kirani (Anak Kandung - NIK: 3271045011120004)</option>
                            </select>
                        </div>

                        {{-- DATA MASTER TERISI OTOMATIS (READ-ONLY DATABASE WARGA) --}}
                        <div class="ws-readonly-section">
                            <div class="ws-readonly-header">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class='bx bx-lock-alt' style="font-size: 15px; color: #28a745;"></i>
                                    <span>DATA MASTER TERISI OTOMATIS (READ-ONLY DATABASE WARGA)</span>
                                </div>
                                <span class="ws-badge-verified-kk">
                                    <i class='bx bx-check'></i> Terverifikasi KK
                                </span>
                            </div>

                            <div class="ws-readonly-grid">
                                <div class="ws-readonly-field">
                                    <span class="ws-readonly-label">Nama Lengkap:</span>
                                    <input type="text" id="ro-nama" class="ws-readonly-input" value="" placeholder="Otomatis terisi setelah memilih anggota keluarga..." readonly>
                                </div>

                                <div class="ws-readonly-field">
                                    <span class="ws-readonly-label">TTL:</span>
                                    <input type="text" id="ro-ttl" class="ws-readonly-input" value="" placeholder="Otomatis terisi..." readonly>
                                </div>

                                <div class="ws-readonly-field">
                                    <span class="ws-readonly-label">Jenis Kelamin:</span>
                                    <input type="text" id="ro-jk" class="ws-readonly-input" value="" placeholder="Otomatis terisi..." readonly>
                                </div>

                                <div class="ws-readonly-field">
                                    <span class="ws-readonly-label">Agama:</span>
                                    <input type="text" id="ro-agama" class="ws-readonly-input" value="" placeholder="Otomatis terisi..." readonly>
                                </div>

                                <div class="ws-readonly-field" style="grid-column: 1 / -1;">
                                    <span class="ws-readonly-label">Alamat KTP / Domisili:</span>
                                    <input type="text" id="ro-alamat" class="ws-readonly-input" value="" placeholder="Otomatis terisi alamat domisili terverifikasi..." readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Keperluan Pengajuan Surat --}}
                        <div class="ws-form-group">
                            <div class="ws-form-header-row">
                                <label class="ws-label" for="keperluan">
                                    <span>Keperluan Pengajuan Surat <span class="ws-req">*</span></span>
                                </label>
                                <span style="font-size: 11.5px; color: #dc2626; font-weight: 700;">Wajib diisi</span>
                            </div>

                            <textarea id="keperluan" name="keperluan" rows="4" class="ws-textarea" placeholder="Tuliskan secara jelas keperluan permohonan surat pengantar Anda (contoh: Pengantar permohonan SKCK di Polsek untuk persyaratan kelengkapan berkas melamar pekerjaan)..." required></textarea>
                            <div class="ws-help-text">
                                Jelaskan tujuan pengajuan surat secara rinci dan jelas untuk mempermudah verifikasi Ketua RT.
                            </div>
                        </div>

                        {{-- Checkbox Persetujuan --}}
                        <div class="ws-checkbox-wrap">
                            <input type="checkbox" id="persetujuan-surat" class="ws-checkbox" required>
                            <label for="persetujuan-surat" style="cursor: pointer;">
                                Saya menyatakan bahwa data permohonan yang diajukan di atas adalah <strong>benar dan dapat dipertanggungjawabkan</strong>.
                            </label>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="ws-form-actions">
                            <button type="button" class="ws-btn-action-preview" onclick="bukaPreviewSuratBaru()">
                                <i class='bx bx-show'></i>
                                <span>Pratinjau / Preview Surat</span>
                            </button>

                            <button type="reset" class="ws-btn-action-reset" onclick="resetFormSurat()">
                                <i class='bx bx-reset'></i>
                                <span>Reset Form</span>
                            </button>

                            <button type="submit" class="ws-btn-action-submit" id="btn-submit-surat">
                                <i class='bx bx-send'></i>
                                <span>Kirim Pengajuan Surat</span>
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ══ MODAL 1: LIHAT ALASAN PENOLAKAN (SCREENSHOT 1 ROW 3) ══ --}}
<div class="ws-modal-overlay" id="modal-alasan-penolakan">
    <div class="ws-modal-card">
        <div class="ws-modal-header">
            <div class="ws-modal-title" style="color: #dc2626;">
                <i class='bx bx-error-circle' style="font-size: 20px;"></i>
                <span>Catatan Penolakan Pengajuan Surat</span>
            </div>
            <button type="button" class="ws-modal-close" onclick="tutupModal('modal-alasan-penolakan')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="ws-modal-body">
            <p style="font-size: 12.5px; color: #64748b; margin: 0 0 10px 0;">
                Catatan dari Pengurus RT 04 terkait pengajuan surat yang ditolak:
            </p>
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 14px 16px; font-size: 13.5px; color: #991b1b; line-height: 1.5;" id="alasan-text-content">
                Dokumen identitas pendukung belum lengkap dan lampiran tidak terbaca jelas. Silakan ajukan ulang dengan melampirkan berkas KTP/KK yang jelas.
            </div>
        </div>
        <div class="ws-modal-footer">
            <button type="button" class="ws-btn-action-reset" onclick="tutupModal('modal-alasan-penolakan')">
                Tutup
            </button>
            <button type="button" class="ws-btn-action-submit" onclick="tutupModal('modal-alasan-penolakan'); switchSuratTab('buat');">
                <i class='bx bx-edit'></i> Ajukan Ulang Sekarang
            </button>
        </div>
    </div>
</div>

{{-- ══ MODAL 2: KONFIRMASI BATALKAN SURAT (SCREENSHOT 1 ROW 2) ══ --}}
<div class="ws-modal-overlay" id="modal-batal-surat">
    <div class="ws-modal-card">
        <div class="ws-modal-header">
            <div class="ws-modal-title" style="color: #dc2626;">
                <i class='bx bx-x-circle' style="font-size: 20px;"></i>
                <span>Batalkan Pengajuan Surat?</span>
            </div>
            <button type="button" class="ws-modal-close" onclick="tutupModal('modal-batal-surat')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="ws-modal-body">
            <p style="font-size: 13.5px; color: #374151; margin: 0; line-height: 1.5;">
                Apakah Anda yakin ingin membatalkan permohonan surat pengantar ini? Pengajuan yang dibatalkan tidak akan diproses oleh Ketua RT.
            </p>
        </div>
        <div class="ws-modal-footer">
            <button type="button" class="ws-btn-action-reset" onclick="tutupModal('modal-batal-surat')">
                Kembali
            </button>
            <button type="button" class="ws-btn-batal-outline" style="background: #dc2626; color: #fff;" onclick="eksekusiBatalSurat()">
                Ya, Batalkan Pengajuan
            </button>
        </div>
    </div>
</div>

{{-- ══ MODAL 3: PRATINJAU DOKUMEN CEPAT ══ --}}
<div class="ws-modal-overlay" id="modal-preview-surat">
    <div class="ws-modal-card ws-modal-card--large">
        <div class="ws-modal-header">
            <div class="ws-modal-title">
                <i class='bx bx-show' style="color: #007bff; font-size: 20px;"></i>
                <span>Pratinjau Format Surat Pengantar RT</span>
            </div>
            <button type="button" class="ws-modal-close" onclick="tutupModal('modal-preview-surat')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="ws-modal-body" style="background: #f8fafc; max-height: 70vh; overflow-y: auto;">
            
            <div style="background: #ffffff; border: 1px solid #cbd5e1; padding: 24px; border-radius: 4px; font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.5;">
                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 16px;">
                    <p style="font-size: 13pt; font-weight: bold; margin: 0;">RUKUN TETANGGA 04 KELURAHAN BARANANGSIANG</p>
                    <p style="font-size: 11pt; font-weight: bold; margin: 2px 0 0 0;">KECAMATAN BOGOR TIMUR – KOTA BOGOR</p>
                </div>

                <div style="text-align: center; margin-bottom: 16px;">
                    <p style="font-size: 13pt; font-weight: bold; text-decoration: underline; margin: 0;">SURAT PENGANTAR</p>
                    <p style="font-size: 10pt; margin: 2px 0 0 0; color: #64748b;">(Nomor surat akan diterbitkan otomatis setelah verifikasi RT)</p>
                </div>

                <p style="font-size: 11pt;">Yang bertanda tangan di bawah ini Ketua RT 04, menerangkan bahwa:</p>
                <table style="width: 100%; font-size: 11pt; margin-bottom: 16px;">
                    <tr>
                        <td style="width: 170px;">Nama Lengkap</td>
                        <td style="width: 15px;">:</td>
                        <td style="font-weight: bold;" id="prev-nama">Dimas Arya Pratama</td>
                    </tr>
                    <tr>
                        <td>Tempat/Tgl Lahir</td>
                        <td>:</td>
                        <td id="prev-ttl">Bogor, 08 Agustus 2005</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td id="prev-jk">Laki-Laki</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td id="prev-agama">Islam</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td id="prev-alamat">Jl. Melati Blok B No. 14, RT 04</td>
                    </tr>
                    <tr>
                        <td>Keperluan</td>
                        <td>:</td>
                        <td id="prev-keperluan" style="font-weight: 500;">-</td>
                    </tr>
                </table>

                <p style="font-size: 11pt;">Demikian surat keterangan pengantar ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

                <div style="display: flex; justify-content: space-between; margin-top: 36px; text-align: center;">
                    <div style="width: 200px;">
                        <p style="margin: 0;">&nbsp;</p>
                        <p style="margin: 0; font-weight: bold;">Pemohon,</p>
                        <div style="height: 60px;"></div>
                        <span style="font-weight: bold; border-bottom: 1px solid #000; display: inline-block; padding-bottom: 2px; min-width: 140px;" id="prev-ttd-pemohon">Dimas Arya Pratama</span>
                    </div>

                    <div style="width: 200px;">
                        <p style="margin: 0;" id="prev-tanggal-surat">Bogor, -</p>
                        <p style="margin: 0; font-weight: bold;">Ketua RT. 04,</p>
                        <div style="height: 60px; display: flex; align-items: center; justify-content: center;" id="prev-ttd-rt-box">
                            {{-- Area tanda tangan: Jangan munculkan gambar ttd jika belum disetujui --}}
                            <div id="prev-ttd-rt-empty" style="font-size: 11px; color: #64748b; font-style: italic; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 6px 10px; background: #f8fafc; line-height: 1.3;">
                                (Menunggu Persetujuan RT)
                            </div>
                            <img id="prev-ttd-rt-img" src="{{ asset('tanda_tangan_rt.png') }}" alt="Tanda Tangan RT" style="display: none; max-height: 58px; max-width: 130px; object-fit: contain;">
                        </div>
                        <span style="font-weight: bold; border-bottom: 1px solid #000; display: inline-block; padding-bottom: 2px; min-width: 140px;">Supriyadi</span>
                    </div>
                </div>
            </div>

        </div>
        <div class="ws-modal-footer">
            <button type="button" class="ws-btn-action-reset" onclick="tutupModal('modal-preview-surat')">
                Tutup Pratinjau
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Master Data KK untuk Auto-Fill Read-Only Fields
const masterWarga = {
    dimas: {
        nama: 'Dimas Arya Pratama',
        ttl: 'Bogor, 08 Agustus 2005 (20 Th)',
        ttlSurat: 'Bogor, 08 Agustus 2005',
        jk: 'Laki-Laki',
        agama: 'Islam',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 Kel. Baranangsiang'
    },
    bambang: {
        nama: 'Bpk. Bambang Pamungkas',
        ttl: 'Depok, 15 Agustus 1988 (37 Th)',
        ttlSurat: 'Depok, 15 Agustus 1988',
        jk: 'Laki-Laki',
        agama: 'Islam',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 Kel. Baranangsiang'
    },
    siti: {
        nama: 'Siti Aminah',
        ttl: 'Bandung, 12 April 1983 (42 Th)',
        ttlSurat: 'Bandung, 12 April 1983',
        jk: 'Perempuan',
        agama: 'Islam',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 Kel. Baranangsiang'
    },
    nabila: {
        nama: 'Nabila Putri Kirani',
        ttl: 'Bogor, 10 November 2012 (13 Th)',
        ttlSurat: 'Bogor, 10 November 2012',
        jk: 'Perempuan',
        agama: 'Islam',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 Kel. Baranangsiang'
    }
};

// Tab Switching
function switchSuratTab(tab) {
    const tabRiwayatBtn = document.getElementById('tab-riwayat-btn');
    const tabBuatBtn = document.getElementById('tab-buat-btn');
    const riwayatContent = document.getElementById('tab-riwayat-content');
    const buatContent = document.getElementById('tab-buat-content');

    if (tab === 'buat') {
        tabRiwayatBtn.classList.remove('ws-tab-btn--active');
        tabBuatBtn.classList.add('ws-tab-btn--active');
        riwayatContent.style.display = 'none';
        buatContent.style.display = 'block';
    } else {
        tabBuatBtn.classList.remove('ws-tab-btn--active');
        tabRiwayatBtn.classList.add('ws-tab-btn--active');
        buatContent.style.display = 'none';
        riwayatContent.style.display = 'block';
    }
}

// Ganti Anggota Warga Tujuan (Auto-Fill Realtime)
function gantiTargetSurat(key) {
    const data = masterWarga[key];
    if (!data) {
        document.getElementById('ro-nama').value = '';
        document.getElementById('ro-ttl').value = '';
        document.getElementById('ro-jk').value = '';
        document.getElementById('ro-agama').value = '';
        document.getElementById('ro-alamat').value = '';
        return;
    }

    document.getElementById('ro-nama').value = data.nama;
    document.getElementById('ro-ttl').value = data.ttl;
    document.getElementById('ro-jk').value = data.jk;
    document.getElementById('ro-agama').value = data.agama;
    document.getElementById('ro-alamat').value = data.alamat;
}

// Reset Form Handler
function resetFormSurat() {
    setTimeout(() => {
        document.getElementById('select-warga-tujuan').value = '';
        gantiTargetSurat('');
        document.getElementById('keperluan').value = '';
        document.getElementById('persetujuan-surat').checked = false;
    }, 50);
}

// Helper Tanda Tangan RT Preview (Hanya muncul jika surat disetujui)
function aturTtdPreview(isDisetujui, tanggalTerbit) {
    const imgTtd = document.getElementById('prev-ttd-rt-img');
    const emptyTtd = document.getElementById('prev-ttd-rt-empty');
    const tglEl = document.getElementById('prev-tanggal-surat');

    if (isDisetujui) {
        if (imgTtd) imgTtd.style.display = 'block';
        if (emptyTtd) emptyTtd.style.display = 'none';
        if (tglEl) tglEl.textContent = 'Bogor, ' + (tanggalTerbit || '24 Oktober 2025');
    } else {
        if (imgTtd) imgTtd.style.display = 'none';
        if (emptyTtd) emptyTtd.style.display = 'block';
        if (tglEl) tglEl.textContent = 'Bogor, (Menunggu Tanggal Terbit)';
    }
}

// Pratinjau Surat Baru sebelum Kirim (Draft Belum Disetujui)
function bukaPreviewSuratBaru() {
    const selectEl = document.getElementById('select-warga-tujuan');
    const key = selectEl.value;
    if (!key || !masterWarga[key]) {
        alert('Silakan pilih Anggota Keluarga Pemohon terlebih dahulu sebelum melihat pratinjau surat.');
        selectEl.focus();
        return;
    }
    const data = masterWarga[key];
    const keperluan = document.getElementById('keperluan').value.trim() || '(Keperluan belum diisi)';

    document.getElementById('prev-nama').textContent = data.nama;
    document.getElementById('prev-ttl').textContent = data.ttlSurat;
    document.getElementById('prev-jk').textContent = data.jk;
    document.getElementById('prev-agama').textContent = data.agama;
    document.getElementById('prev-alamat').textContent = data.alamat;
    document.getElementById('prev-keperluan').textContent = keperluan;
    const ttdPemohon = document.getElementById('prev-ttd-pemohon');
    if (ttdPemohon) ttdPemohon.textContent = data.nama;

    // Surat belum disetujui: Jangan munculkan tanda tangan RT
    aturTtdPreview(false);

    bukaModal('modal-preview-surat');
}

// Preview Draft Surat (Status Menunggu Verifikasi)
function bukaPreviewDraft(nama, keperluan) {
    document.getElementById('prev-nama').textContent = nama;
    document.getElementById('prev-ttl').textContent = 'Bogor, 12 April 1983';
    document.getElementById('prev-jk').textContent = 'Perempuan';
    document.getElementById('prev-agama').textContent = 'Islam';
    document.getElementById('prev-alamat').textContent = 'Jl. Melati Blok B No. 14, RT 04';
    document.getElementById('prev-keperluan').textContent = keperluan;
    const ttdPemohon = document.getElementById('prev-ttd-pemohon');
    if (ttdPemohon) ttdPemohon.textContent = nama;

    // Surat berstatus menunggu verifikasi: Jangan munculkan tanda tangan RT
    aturTtdPreview(false);

    bukaModal('modal-preview-surat');
}

// Alasan Penolakan Modal
function bukaAlasanPenolakan(teks) {
    document.getElementById('alasan-text-content').textContent = teks;
    bukaModal('modal-alasan-penolakan');
}

// Batalkan Surat Modal
let rowBatalTarget = null;
function konfirmasiBatalSurat(rowId) {
    rowBatalTarget = rowId;
    bukaModal('modal-batal-surat');
}

function eksekusiBatalSurat() {
    tutupModal('modal-batal-surat');
    // Update stat counter
    const statMenunggu = document.getElementById('stat-menunggu');
    if (statMenunggu && parseInt(statMenunggu.textContent) > 0) {
        statMenunggu.textContent = parseInt(statMenunggu.textContent) - 1;
    }
    alert('Pengajuan surat berhasil dibatalkan.');
    location.reload();
}

// Modal Helpers
function bukaModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
}

function tutupModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
}

// Pagination & Filter Functionality
let currentSuratPage = 1;
const suratPerPage = 10;

function updatePagination() {
    const statusVal = document.getElementById('filter-status-select').value.toLowerCase();
    const searchVal = document.getElementById('search-surat-input').value.toLowerCase().trim();
    const tbody = document.getElementById('tbody-riwayat-surat');
    if (!tbody) return;
    const allRows = Array.from(tbody.querySelectorAll('.ws-row-item'));

    // Prioritaskan status 'menunggu' selalu di paling depan, disusul 'disetujui' dan 'ditolak'
    allRows.sort((a, b) => {
        const statusOrder = { 'menunggu': 1, 'disetujui': 2, 'ditolak': 3 };
        const orderA = statusOrder[(a.dataset.status || '').toLowerCase()] || 99;
        const orderB = statusOrder[(b.dataset.status || '').toLowerCase()] || 99;
        return orderA - orderB;
    });
    allRows.forEach(r => tbody.appendChild(r));

    // Filter matching rows
    const matchingRows = allRows.filter(row => {
        const rowStatus = (row.dataset.status || '').toLowerCase();
        const rowPemohon = (row.dataset.pemohon || '').toLowerCase();
        const rowNoSurat = (row.dataset.nosurat || '').toLowerCase();
        const rowKeperluan = (row.dataset.keperluan || '').toLowerCase();

        const matchStatus = !statusVal || rowStatus === statusVal;
        const matchSearch = !searchVal || 
            rowPemohon.includes(searchVal) || 
            rowNoSurat.includes(searchVal) || 
            rowKeperluan.includes(searchVal);

        return matchStatus && matchSearch;
    });

    const totalMatching = matchingRows.length;
    const totalPages = Math.ceil(totalMatching / suratPerPage) || 1;

    if (currentSuratPage > totalPages) {
        currentSuratPage = totalPages;
    }
    if (currentSuratPage < 1) {
        currentSuratPage = 1;
    }

    // Hide all rows first
    allRows.forEach(r => r.style.display = 'none');

    // Show only rows for current page
    const startIndex = (currentSuratPage - 1) * suratPerPage;
    const endIndex = Math.min(startIndex + suratPerPage, totalMatching);

    for (let i = startIndex; i < endIndex; i++) {
        matchingRows[i].style.display = '';
    }

    // Empty state
    const emptyRow = document.getElementById('row-empty-surat');
    if (emptyRow) {
        emptyRow.style.display = totalMatching === 0 ? '' : 'none';
    }

    // Summary text
    const summaryText = document.getElementById('footer-summary-text');
    if (summaryText) {
        if (totalMatching === 0) {
            summaryText.innerHTML = `Tidak ada data yang sesuai filter pencarian.`;
        } else {
            summaryText.innerHTML = `Menampilkan <strong>${startIndex + 1}</strong> sampai <strong>${endIndex}</strong> dari <strong>${totalMatching}</strong> data riwayat <code>pengajuan_surat</code>`;
        }
    }

    // Render Page Numbers
    const numbersContainer = document.getElementById('pagination-numbers');
    if (numbersContainer) {
        numbersContainer.innerHTML = '';
        for (let p = 1; p <= totalPages; p++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'ws-page-btn' + (p === currentSuratPage ? ' ws-page-btn--active' : '');
            btn.textContent = p;
            btn.setAttribute('aria-label', 'Halaman ' + p);
            btn.onclick = () => gantiHalamanSurat(p);
            numbersContainer.appendChild(btn);
        }
    }

    // Prev / Next button state
    const prevBtn = document.getElementById('btn-prev-page');
    const nextBtn = document.getElementById('btn-next-page');

    if (prevBtn) {
        prevBtn.disabled = currentSuratPage <= 1;
    }
    if (nextBtn) {
        nextBtn.disabled = currentSuratPage >= totalPages;
    }
}

function gantiHalamanSurat(page) {
    if (page === 'prev') {
        currentSuratPage--;
    } else if (page === 'next') {
        currentSuratPage++;
    } else {
        currentSuratPage = Number(page);
    }
    updatePagination();
}

function applyFilters() {
    currentSuratPage = 1;
    updatePagination();
}

function filterByStatus(status) {
    document.getElementById('filter-status-select').value = status;
    switchSuratTab('riwayat');
    applyFilters();
}

// Check URL param and initialize pagination on load
document.addEventListener('DOMContentLoaded', function () {
    updatePagination();
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'buat') {
        switchSuratTab('buat');
    }
});
</script>
@endpush
