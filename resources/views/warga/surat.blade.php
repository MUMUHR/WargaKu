@extends('layouts.warga')

@section('title', 'Layanan Pengajuan Surat')
@section('meta_description', 'Layanan Pengajuan Surat Pengantar RT 04 Mandiri secara Online — WargaKu')

@section('topbar_section')
    <span class="admin-topbar__breadcrumb-link">Layanan Warga</span>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Pengajuan Surat Pengantar</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-surat.css') }}">
@endpush

@section('content')

{{-- ── Header Halaman ── --}}
<div class="warga-surat-page">
    
    <div style="margin-bottom: 22px;">
        <h1 style="font-size:22px;font-weight:800;color:#111827;margin-bottom:4px;">
            Layanan Pengajuan Surat Pengantar RT 04
        </h1>
        <p style="font-size:13px;color:#6c757d;margin:0;">
            Ajukan surat pengantar resmi ke Pengurus RT 04 secara online terintegrasi tabel <code>pengajuan_surat</code>, pantau status real-time, dan unduh e-surat resmi.
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

    @if(session('error'))
    <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 18px;border-radius:6px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:600;">
            <i class='bx bx-error-circle' style="font-size:20px;color:#dc3545;"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:transparent;border:none;color:#721c24;font-size:18px;cursor:pointer;line-height:1;">
            &times;
        </button>
    </div>
    @endif

    @if($errors->any())
    <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 18px;border-radius:6px;margin-bottom:20px;">
        <ul style="margin: 0; padding-left: 20px; font-size: 13.5px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── 3 Big Stat Cards Grid ── --}}
    <div class="ws-stat-grid">
        
        {{-- Card 1: Menunggu Verifikasi --}}
        <div class="ws-stat-card ws-stat-card--yellow">
            <div class="ws-stat-card__body">
                <div class="ws-stat-card__number" id="stat-menunggu">{{ $statMenunggu }}</div>
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
                <div class="ws-stat-card__number" id="stat-disetujui">{{ $statDisetujui }}</div>
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
                <div class="ws-stat-card__number" id="stat-ditolak">{{ $statDitolak }}</div>
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
            <span>
                <span class="ws-tab-label-main">Riwayat &amp; Status</span>
                <span class="ws-tab-label-extra"> Pengajuan</span>
            </span>
        </button>
        <button type="button" class="ws-tab-btn" id="tab-buat-btn" onclick="switchSuratTab('buat')">
            <i class='bx bx-edit'></i>
            <span>
                <span class="ws-tab-label-main">Buat Pengajuan</span>
                <span class="ws-tab-label-extra"> Surat Baru</span>
            </span>
        </button>
    </div>

    {{-- ── Tab Content Container ── --}}
    <div class="ws-tab-content-wrapper">
        
        {{-- ══════════════════════════════════════════════════════
             TAB 1: RIWAYAT & STATUS PENGAJUAN
             ══════════════════════════════════════════════════════ --}}
        <div id="tab-riwayat-content">
            
            {{-- Toolbar --}}
            <div class="ws-toolbar">
                <div>
                    <div class="ws-toolbar__title">Daftar Riwayat Pengajuan Surat Pengantar</div>
                    <div class="ws-toolbar__sub">Sinkronisasi langsung data tabel <code>pengajuan_surat</code> keluarga {{ $kepala?->nama ?? 'Warga' }}</div>
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
                        
                        @forelse($allSurats as $surat)
                            @php
                                $statusStr = is_object($surat->status) ? $surat->status->value : $surat->status;
                                $wargaTarget = $surat->wargaTujuan ?? $surat->pemohon;
                                $namaTarget = $wargaTarget?->nama ?? '-';
                                $ttlTarget = ($wargaTarget?->tempat_lahir ? $wargaTarget->tempat_lahir . ', ' : '') . ($wargaTarget?->tanggal_lahir ? $wargaTarget->tanggal_lahir->translatedFormat('d F Y') : '-');
                                $jkTarget = $wargaTarget?->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : ($wargaTarget?->jenis_kelamin === 'perempuan' ? 'Perempuan' : '-');
                                $agamaTarget = ucfirst($wargaTarget?->agama ?? 'Islam');
                                $alamatTarget = $wargaTarget?->alamat ?? ($surat->pemohon?->alamat ?? $alamatKeluarga);
                                $keperluanTarget = $surat->keperluan;
                            @endphp
                            <tr class="ws-row-item" 
                                data-status="{{ $statusStr }}" 
                                data-pemohon="{{ strtolower($namaTarget) }}" 
                                data-nosurat="{{ strtolower($surat->nomor_surat ?? '') }}" 
                                data-keperluan="{{ strtolower($surat->keperluan) }}">
                                <td style="font-weight: 600; color: #475569;">
                                    {{ $surat->created_at ? $surat->created_at->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td>
                                    <strong style="color: #0f172a; font-size: 14px;">{{ $namaTarget }}</strong>
                                    @if($wargaTarget?->status_warga === 'meninggal')
                                        <span style="display:inline-block; font-size:10px; font-weight:700; color:#dc2626; background:#fee2e2; padding:1px 6px; border-radius:4px; margin-left:4px;">(Alm.)</span>
                                    @endif
                                    @if($wargaTarget && $surat->pemohon && $wargaTarget->id !== $surat->pemohon->id)
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                            Diajukan: {{ $surat->pemohon->nama }}
                                            @if($surat->pemohon->status_warga === 'meninggal')
                                                <span style="color:#dc2626; font-weight:600;">(Alm.)</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="line-height: 1.5; color: #334155; font-size: 13.5px;">
                                        {{ $surat->keperluan }}
                                    </div>
                                </td>
                                <td>
                                    @if($surat->nomor_surat)
                                        <span class="ws-badge-no-surat">{{ $surat->nomor_surat }}</span>
                                    @else
                                        <span style="color: #94a3b8; font-weight: 700; font-size: 16px; padding-left: 4px;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($statusStr === 'menunggu')
                                        <span class="ws-badge-status ws-badge-status--menunggu">
                                            <i class='bx bx-time-five'></i> Menunggu Verifikasi
                                        </span>
                                    @elseif($statusStr === 'disetujui')
                                        <span class="ws-badge-status ws-badge-status--disetujui">
                                            <i class='bx bx-check-circle'></i> Disetujui
                                        </span>
                                    @elseif($statusStr === 'ditolak')
                                        <span class="ws-badge-status ws-badge-status--ditolak">
                                            <i class='bx bx-x-circle'></i> Ditolak
                                        </span>
                                    @else
                                        <span class="ws-badge-status" style="background:#f1f5f9;color:#64748b;border-color:#cbd5e1;">
                                            <i class='bx bx-minus-circle'></i> {{ ucfirst($statusStr) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($statusStr === 'menunggu')
                                        <div style="display: flex; align-items: center; justify-content: flex-start; gap: 8px;">
                                            <button type="button" class="ws-btn-preview-outline" 
                                                onclick="bukaPreviewDraftCustom('{{ addslashes($namaTarget) }}', '{{ addslashes($ttlTarget) }}', '{{ addslashes($jkTarget) }}', '{{ addslashes($agamaTarget) }}', '{{ addslashes($alamatTarget) }}', '{{ addslashes($keperluanTarget) }}')">
                                                <i class='bx bx-show'></i>
                                                <span>Preview</span>
                                            </button>
                                            <button type="button" class="ws-btn-batal-outline" onclick="konfirmasiBatalSurat({{ $surat->id }})">
                                                <i class='bx bx-x-circle'></i>
                                                <span>Batalkan</span>
                                            </button>
                                        </div>
                                    @elseif($statusStr === 'disetujui')
                                        <div style="display: flex; align-items: center; justify-content: flex-start;">
                                            <a href="{{ route('warga.surat.cetak', $surat->id) }}" class="ws-btn-esurat" title="Unduh dan Cetak Dokumen Surat">
                                                <i class='bx bx-download'></i>
                                                <span>Unduh e-Surat</span>
                                            </a>
                                        </div>
                                    @elseif($statusStr === 'ditolak')
                                        <div style="display: flex; align-items: center; justify-content: flex-start;">
                                            <button type="button" class="ws-btn-alasan-outline" onclick="bukaAlasanPenolakan('{{ addslashes($surat->alasan ?? 'Berkas permohonan belum memenuhi syarat administratif RT.') }}')">
                                                <i class='bx bx-error-circle'></i>
                                                <span>Lihat Alasan</span>
                                            </button>
                                        </div>
                                    @else
                                        <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Tidak ada aksi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="row-empty-surat-default">
                                <td colspan="6" style="text-align: center; padding: 36px 20px; color: #6b7280;">
                                    <i class='bx bx-folder-open' style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 6px;"></i>
                                    <span>Belum ada riwayat pengajuan surat pengantar dari keluarga Anda.</span>
                                </td>
                            </tr>
                        @endforelse

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
                <span id="footer-summary-text">Menampilkan {{ count($allSurats) }} data riwayat <code>pengajuan_surat</code></span>
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
             TAB 2: BUAT PENGAJUAN SURAT BARU
             ══════════════════════════════════════════════════════ --}}
        <div id="tab-buat-content" style="display: none;">
            
            <div class="ws-form-card">
                
                {{-- Header Biru --}}
                <div class="ws-form-card__header">
                    <i class='bx bx-notepad ws-form-card__icon'></i>
                    <div>
                        <h2 class="ws-form-card__title">Formulir Pengajuan Surat Pengantar RT</h2>
                        <p class="ws-form-card__sub">Tabel database <code>pengajuan_surat</code> • Pengurus RT 04 Kelurahan Belian</p>
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
                                    No. KK : <strong style="color: #0284c7;">{{ $keluarga->no_kk ?? '-' }}</strong>
                                </span>
                            </div>
                            
                            <select id="select-warga-tujuan" name="warga_tujuan_id" class="ws-select" onchange="gantiTargetSurat(this.value)" required>
                                <option value="" disabled selected>-- Pilih Anggota Keluarga Pemohon --</option>
                                @foreach($anggotaKeluarga as $w)
                                    <option value="{{ $w->id }}" {{ old('warga_tujuan_id') == $w->id ? 'selected' : '' }}>
                                        {{ $w->nama }} ({{ ucwords(str_replace('_', ' ', $w->status_hubungan ?? 'Anggota')) }} - NIK: {{ $w->nik }})
                                    </option>
                                @endforeach
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
                                    <span class="ws-readonly-label">Alamat Domisili:</span>
                                    <input type="text" id="ro-alamat" class="ws-readonly-input" value="" placeholder="Contoh: Blok B4 No. 12 (otomatis terisi dari data warga terpilih)" readonly>
                                    <small style="font-size: 12px; color: #64748b; font-weight: 500; display: block; margin-top: 4px;">
                                        Contoh: Blok A No. 05 atau Blok B4 No. 12 (hanya blok &amp; nomor rumah tempat tinggal di RT 04).
                                    </small>
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

                            <textarea id="keperluan" name="keperluan" rows="4" class="ws-textarea" placeholder="Tuliskan secara jelas keperluan permohonan surat pengantar Anda (contoh: Pengantar permohonan SKCK di Polsek untuk persyaratan kelengkapan berkas melamar pekerjaan)..." required>{{ old('keperluan') }}</textarea>
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

{{-- ══ MODAL 1: LIHAT ALASAN PENOLAKAN ══ --}}
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
                -
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

{{-- ══ MODAL 2: KONFIRMASI BATALKAN SURAT ══ --}}
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
            <form id="form-batal-surat" action="" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="ws-btn-batal-outline" style="background: #dc2626; color: #fff;">
                    Ya, Batalkan Pengajuan
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══ MODAL 3: PRATINJAU DOKUMEN CEPAT ══ --}}
<div class="ws-modal-overlay" id="modal-preview-surat">
    <div class="ws-modal-card ws-modal-card--large ws-modal-card--preview">
        <div class="ws-modal-header">
            <div class="ws-modal-title">
                <i class='bx bx-show' style="color: #007bff; font-size: 20px;"></i>
                <span>Pratinjau Format Surat Pengantar RT</span>
            </div>
            <button type="button" class="ws-modal-close" onclick="tutupModal('modal-preview-surat')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="ws-modal-body ws-preview-modal-body">
            
            <div style="text-align: center; margin-bottom: 12px; font-size: 12px; color: #64748b; font-family: sans-serif; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class='bx bx-info-circle' style="font-size: 15px; color: #0284c7;"></i>
                <span>Format pratinjau dokumen skala tetap (A4). Geser ke samping jika tampilan terpotong di layar kecil.</span>
            </div>

            {{-- Kertas Dokumen Tetap (A4 format) --}}
            <div class="ws-paper-preview">
                <div style="text-align: center; line-height: 1.35; margin-bottom: 8px;">
                    <p style="font-size: 15px; font-weight: bold; letter-spacing: 0.5px; margin: 0; text-transform: uppercase;">RUKUN TETANGGA 04 KELURAHAN BELIAN</p>
                    <p style="font-size: 14px; font-weight: bold; margin: 2px 0 0 0; text-transform: uppercase;">KECAMATAN BATAM KOTA – KOTA BATAM</p>
                    <p style="font-size: 12.5px; margin: 2px 0 0 0;">Sekretariat: Jl. Melati Blok A No. 1, RT 04 Kode Pos: 29464</p>
                </div>
                <div style="border-top: 3px solid #000; border-bottom: 1px solid #000; height: 2px; margin: 10px 0 22px 0;"></div>

                <div style="text-align: center; margin-bottom: 22px;">
                    <p style="font-size: 15.5px; font-weight: bold; text-decoration: underline; margin: 0; letter-spacing: 0.5px;">SURAT PENGANTAR</p>
                    <p style="font-size: 13px; font-weight: bold; margin: 3px 0 0 0; color: #64748b;" id="prev-nomor-surat">
                        (Nomor surat akan diterbitkan otomatis setelah verifikasi RT)
                    </p>
                </div>

                <p style="font-size: 13.5px; line-height: 1.5; text-indent: 30px; text-align: justify; margin-bottom: 12px;">Yang bertanda tangan di bawah ini Ketua RT 04, menerangkan bahwa:</p>
                <table style="width: 100%; font-size: 13.5px; line-height: 1.5; margin: 12px 0 16px 0; border-collapse: collapse;">
                    <tr>
                        <td style="width: 175px; padding: 3px 0;">Nama Lengkap</td>
                        <td style="width: 20px; padding: 3px 0; text-align: center;">:</td>
                        <td style="font-weight: bold; padding: 3px 0;" id="prev-nama">-</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Tempat/Tgl Lahir</td>
                        <td style="padding: 3px 0; text-align: center;">:</td>
                        <td style="padding: 3px 0;" id="prev-ttl">-</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Jenis Kelamin</td>
                        <td style="padding: 3px 0; text-align: center;">:</td>
                        <td style="padding: 3px 0;" id="prev-jk">-</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Agama</td>
                        <td style="padding: 3px 0; text-align: center;">:</td>
                        <td style="padding: 3px 0;" id="prev-agama">-</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Alamat Domisili</td>
                        <td style="padding: 3px 0; text-align: center;">:</td>
                        <td style="padding: 3px 0;" id="prev-alamat">-</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Keperluan</td>
                        <td style="padding: 3px 0; text-align: center;">:</td>
                        <td style="font-weight: 500; padding: 3px 0;" id="prev-keperluan">-</td>
                    </tr>
                </table>

                <p style="font-size: 13.5px; line-height: 1.5; text-indent: 30px; text-align: justify; margin-bottom: 24px;">Demikian surat keterangan pengantar ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

                <div style="display: flex; justify-content: space-between; text-align: center; margin-top: 60px; margin-bottom: 25px;">
                    <div style="width: 200px;">
                        <p style="margin: 0;">&nbsp;</p>
                        <p style="margin: 0; font-weight: bold; font-size: 13.5px;">Pemohon,</p>
                        <div style="height: 65px;"></div>
                        <span style="font-weight: bold; text-decoration: underline; display: inline-block; font-size: 13.5px;" id="prev-ttd-pemohon">-</span>
                    </div>

                    <div style="width: 200px;">
                        <p style="margin: 0; font-size: 13.5px;" id="prev-tanggal-surat">Batam, -</p>
                        <p style="margin: 0; font-weight: bold; font-size: 13.5px;">Ketua RT. 04,</p>
                        <div style="height: 65px; display: flex; align-items: center; justify-content: center;" id="prev-ttd-rt-box">
                            <div id="prev-ttd-rt-empty" style="font-size: 11px; color: #64748b; font-style: italic; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 6px 10px; background: #f8fafc; line-height: 1.3;">
                                (Menunggu Persetujuan RT)
                            </div>
                            <img id="prev-ttd-rt-img" src="{{ asset('tanda_tangan_rt.png') }}" alt="Tanda Tangan RT" style="display: none; max-height: 58px; max-width: 130px; object-fit: contain;">
                        </div>
                        <span style="font-weight: bold; text-decoration: underline; display: inline-block; font-size: 13.5px;">Supriyadi</span>
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
// Master Data Anggota Keluarga dari Database untuk Auto-Fill Form
const masterWarga = @json($dataWargaMap);

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
function gantiTargetSurat(id) {
    const data = masterWarga[id];
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

// Pratinjau Surat Baru sebelum Kirim (Draft Belum Disetujui)
function bukaPreviewSuratBaru() {
    const selectEl = document.getElementById('select-warga-tujuan');
    const id = selectEl.value;
    if (!id || !masterWarga[id]) {
        alert('Silakan pilih Anggota Keluarga Pemohon terlebih dahulu sebelum melihat pratinjau surat.');
        selectEl.focus();
        return;
    }
    const data = masterWarga[id];
    const keperluan = document.getElementById('keperluan').value.trim() || '(Keperluan belum diisi)';

    document.getElementById('prev-nomor-surat').textContent = '(Nomor surat akan diterbitkan otomatis setelah verifikasi RT)';
    document.getElementById('prev-nama').textContent = data.namaLengkap || data.nama;
    document.getElementById('prev-ttl').textContent = data.ttlSurat || data.ttl;
    document.getElementById('prev-jk').textContent = data.jk;
    document.getElementById('prev-agama').textContent = data.agama;
    document.getElementById('prev-alamat').textContent = data.alamat;
    document.getElementById('prev-keperluan').textContent = keperluan;
    
    const ttdPemohon = document.getElementById('prev-ttd-pemohon');
    if (ttdPemohon) ttdPemohon.textContent = data.namaLengkap || data.nama;

    // TTD RT masih belum disetujui
    const imgTtd = document.getElementById('prev-ttd-rt-img');
    const emptyTtd = document.getElementById('prev-ttd-rt-empty');
    const tglEl = document.getElementById('prev-tanggal-surat');
    if (imgTtd) imgTtd.style.display = 'none';
    if (emptyTtd) emptyTtd.style.display = 'block';
    if (tglEl) tglEl.textContent = 'Batam, (Menunggu Persetujuan RT)';

    bukaModal('modal-preview-surat');
}

// Preview Draft Surat dari Tabel Riwayat
function bukaPreviewDraftCustom(nama, ttl, jk, agama, alamat, keperluan) {
    document.getElementById('prev-nomor-surat').textContent = '(Menunggu Nomor Registrasi dari RT 04)';
    document.getElementById('prev-nama').textContent = nama;
    document.getElementById('prev-ttl').textContent = ttl;
    document.getElementById('prev-jk').textContent = jk;
    document.getElementById('prev-agama').textContent = agama;
    document.getElementById('prev-alamat').textContent = alamat;
    document.getElementById('prev-keperluan').textContent = keperluan;

    const ttdPemohon = document.getElementById('prev-ttd-pemohon');
    if (ttdPemohon) ttdPemohon.textContent = nama;

    const imgTtd = document.getElementById('prev-ttd-rt-img');
    const emptyTtd = document.getElementById('prev-ttd-rt-empty');
    const tglEl = document.getElementById('prev-tanggal-surat');
    if (imgTtd) imgTtd.style.display = 'none';
    if (emptyTtd) emptyTtd.style.display = 'block';
    if (tglEl) tglEl.textContent = 'Batam, (Menunggu Persetujuan RT)';

    bukaModal('modal-preview-surat');
}

// Alasan Penolakan Modal
function bukaAlasanPenolakan(teks) {
    document.getElementById('alasan-text-content').textContent = teks;
    bukaModal('modal-alasan-penolakan');
}

// Batalkan Surat Modal
function konfirmasiBatalSurat(suratId) {
    const form = document.getElementById('form-batal-surat');
    if (form) {
        form.action = '{{ url('/warga/surat') }}/' + suratId + '/batal';
    }
    bukaModal('modal-batal-surat');
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

    if (allRows.length === 0) return;

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
    
    // Auto-populate if old input exists
    const initialSelect = document.getElementById('select-warga-tujuan');
    if (initialSelect && initialSelect.value) {
        gantiTargetSurat(initialSelect.value);
    }
});
</script>
@endpush
