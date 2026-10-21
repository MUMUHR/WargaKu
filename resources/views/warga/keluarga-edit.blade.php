@extends('layouts.warga')

@section('title', 'Form Pengajuan Perubahan Data Warga')
@section('meta_description', 'Formulir Pengajuan Perubahan Data Warga — WargaKu RT 04')

@section('topbar_section')
    <a href="{{ route('warga.keluarga') }}" class="admin-topbar__breadcrumb-link">Data Warga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Form Pengajuan Perubahan Data</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-form-keluarga.css') }}">
@endpush

@section('content')

@php
    $selectedNik = $targetNik ?? ($anggotaKeluarga->first()?->nik ?? '');
    $selectedData = $anggotaDataEdit[$selectedNik] ?? (count($anggotaDataEdit) > 0 ? reset($anggotaDataEdit) : null);
@endphp
<div class="warga-form-page">
    <div class="wf-header-action-row">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin-bottom:4px;letter-spacing:-0.3px;">
                Formulir Pengajuan Perubahan Data Warga
            </h1>
            <p style="font-size:13px;color:#64748b;margin:0;">
                Sampaikan permohonan koreksi atau pembaruan data kependudukan ke Pengurus RT 04
            </p>
        </div>
        <div>
            <a href="{{ route('warga.keluarga') }}" class="wf-btn-back">
                <i class='bx bx-arrow-back'></i>
                <span>Kembali ke Data Keluarga</span>
            </a>
        </div>
    </div>

    {{-- Banner Petunjuk --}}
    <div class="wf-info-banner">
        <div class="wf-info-banner__icon">
            <i class='bx bx-info-circle'></i>
        </div>
        <div class="wf-info-banner__text">
            <strong>Petunjuk Pengajuan:</strong> Pilih anggota keluarga yang hendak diperbarui pada <strong>Langkah 1</strong>. Selanjutnya sesuaikan data baru pada kolom <strong>Data Usulan Baru</strong> (baris yang berubah akan otomatis disorot hijau). Lampirkan dokumen bukti resmi pendukung di kolom kanan sebelum mengirim pengajuan.
        </div>
    </div>

    <form id="form-edit-keluarga" action="{{ route('warga.keluarga.edit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="warga-form-grid">
            {{-- ── KOLOM UTAMA (KIRI) ── --}}
            <div class="warga-form-main">
                
                {{-- CARD 1: Identitas Pemohon & Sasaran Perubahan --}}
                <div class="wf-card">
                    <div class="wf-card__header-light">
                        <div class="wf-header-title">
                            <i class='bx bx-user-pin' style="color:#007bff;font-size:19px;"></i>
                            <span>1. Pilih Anggota Keluarga Sasaran Perubahan</span>
                        </div>
                        <span class="wf-badge-step-blue">LANGKAH 1</span>
                    </div>
                    <div class="wf-card__body">
                        
                        {{-- Ringkasan KK --}}
                        <div class="wf-kk-bar">
                            <div class="wf-kk-bar__item">
                                <span class="wf-kk-bar__label">No. Kartu Keluarga (KK)</span>
                                <span class="wf-kk-bar__chip">{{ $keluarga?->no_kk ?? '-' }}</span>
                            </div>
                            <div class="wf-kk-bar__item">
                                <span class="wf-kk-bar__label">Kepala Keluarga</span>
                                <strong class="wf-kk-bar__val">{{ $kepala?->nama ?? '-' }}</strong>
                            </div>
                            <div class="wf-kk-bar__item">
                                <span class="wf-kk-bar__label">Wilayah Domisili</span>
                                <span class="wf-kk-bar__val">RT 04</span>
                            </div>
                        </div>

                        <div>
                            <label class="wf-label" for="select-target-anggota">
                                <span>Pilih Anggota Keluarga yang Ingin Diperbarui <span class="wf-req">*</span></span>
                                <span class="wf-label-hint">Data tabel otomatis dimuat sesuai pilihan</span>
                            </label>
                            <select id="select-target-anggota" name="target_anggota" class="wf-select" onchange="gantiAnggota(this.value)" required>
                                @forelse($anggotaKeluarga as $ang)
                                    <option value="{{ $ang->nik }}" {{ (isset($targetNik) && $targetNik == $ang->nik) ? 'selected' : '' }}>
                                        {{ $ang->nama }} — ({{ ucwords(str_replace('_', ' ', $ang->status_hubungan)) }} | NIK: {{ $ang->nik }})
                                    </option>
                                @empty
                                    <option value="" disabled>Tidak ada anggota keluarga terdaftar</option>
                                @endforelse
                            </select>
                        </div>

                        {{-- Kartu Ringkasan Anggota Terpilih --}}
                        <div class="wf-target-card" id="member-preview-card">
                            <div class="wf-target-card__user">
                                <div class="wf-target-card__avatar" id="preview-avatar">{{ $selectedData['avatar'] ?? 'WG' }}</div>
                                <div>
                                    <div class="wf-target-card__name" id="preview-name">{{ $selectedData['nama'] ?? 'Pilih Anggota' }}</div>
                                    <div class="wf-target-card__nik">NIK: <span id="preview-nik">{{ $selectedData['nik'] ?? '-' }}</span> • Hubungan: <span id="preview-shdk">{{ $selectedData['shdk'] ?? '-' }}</span></div>
                                </div>
                            </div>
                            <div>
                                <span class="wf-target-card__status">
                                    <i class='bx bx-check-circle'></i> Siap Diedit
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- CARD 2: Komparasi Data (Eksisting vs Usulan Baru) --}}
                <div class="wf-card">
                    <div class="wf-card__header-light">
                        <div class="wf-header-title">
                            <i class='bx bx-sync' style="color:#007bff;font-size:19px;"></i>
                            <span>2. Komparasi Data (Eksisting vs Usulan Baru)</span>
                        </div>
                        <span class="wf-badge-step-blue">LANGKAH 2</span>
                    </div>
                    <div class="wf-card__body" style="padding:16px 20px;">
                        
                        <div class="wf-comparison-table-wrap">
                        <table class="wf-comparison-table">
                            <thead>
                                <tr>
                                    <th class="col-param">NAMA PARAMETER</th>
                                    <th class="col-old">
                                        <i class='bx bx-lock-alt' style="vertical-align:middle;font-size:13px;margin-right:2px;"></i>
                                        DATA EKSISTING (SAAT INI)
                                    </th>
                                    <th class="col-new">
                                        <i class='bx bx-edit' style="vertical-align:middle;font-size:13px;margin-right:2px;"></i>
                                        DATA USULAN BARU (DAPAT DIEDIT)
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody-komparasi">
                                
                                {{-- 1. NIK --}}
                                <tr id="row-nik">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>1. NIK</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Nomor Induk Kependudukan (16 digit)</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-nik">{{ $selectedData['nik'] ?? '' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-nik" name="new_nik" class="wf-input" value="{{ $selectedData['nik'] ?? '' }}" maxlength="16">
                                        </div>
                                    </td>
                                </tr>

                                {{-- 2. Nama Lengkap --}}
                                <tr id="row-nama">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>2. Nama Lengkap</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Sesuai KTP-el / Akta Kelahiran</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-nama">{{ $selectedData['nama'] ?? '' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-nama" name="new_nama" class="wf-input" value="{{ $selectedData['nama'] ?? '' }}">
                                        </div>
                                    </td>
                                </tr>

                                {{-- 3. Tempat Lahir --}}
                                <tr id="row-tempat-lahir">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>3. Tempat Lahir</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Kota / Kabupaten kelahiran</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-tempat-lahir">{{ $selectedData['tempatLahir'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-tempat-lahir" name="new_tempat_lahir" class="wf-input" value="{{ $selectedData['tempatLahir'] ?? '' }}">
                                        </div>
                                    </td>
                                </tr>

                                {{-- 4. Tanggal Lahir --}}
                                <tr id="row-tgl-lahir">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>4. Tanggal Lahir</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Sesuai akta kelahiran</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-tgl-lahir">{{ $selectedData['tglLahir'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="date" id="new-tgl-lahir" name="new_tgl_lahir" class="wf-input" value="{{ $selectedData['tglLahirNew'] ?? '' }}">
                                        </div>
                                    </td>
                                </tr>

                                {{-- 5. Jenis Kelamin --}}
                                <tr id="row-jk">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>5. Jenis Kelamin</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Identitas hukum kependudukan</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-jk">{{ $selectedData['jk'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-jk" name="new_jk" class="wf-select">
                                                <option value="Laki-Laki" {{ ($selectedData['jk'] ?? '') === 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                                <option value="Perempuan" {{ ($selectedData['jk'] ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 6. Agama --}}
                                <tr id="row-agama">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>6. Agama</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Agama resmi terdaftar</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-agama">{{ $selectedData['agama'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-agama" name="new_agama" class="wf-select">
                                                @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm)
                                                    <option value="{{ $agm }}" {{ ($selectedData['agama'] ?? '') === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 7. Pendidikan Terakhir --}}
                                <tr id="row-pendidikan">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>7. Pendidikan Terakhir</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Berdasarkan ijazah resmi</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-pendidikan">{{ $selectedData['pendidikanOld'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-pendidikan" name="new_pendidikan" class="wf-select">
                                                @php
                                                    $opsiPendidikan = [
                                                        'Tidak / Belum Sekolah', 'Belum Tamat SD / Sederajat', 'SD / Sederajat',
                                                        'SMP / Sederajat', 'SLTA / Sederajat', 'Diploma I / II', 'Diploma III',
                                                        'Diploma IV / Strata 1 (S1)', 'Strata 2 (S2)', 'Strata 3 (S3)'
                                                    ];
                                                @endphp
                                                @foreach($opsiPendidikan as $pdd)
                                                    <option value="{{ $pdd }}" {{ ($selectedData['pendidikanNew'] ?? '') === $pdd ? 'selected' : '' }}>{{ $pdd }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 8. Pekerjaan --}}
                                <tr id="row-pekerjaan">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>8. Pekerjaan</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Profesi / Mata Pencaharian</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-pekerjaan">{{ $selectedData['pekerjaanOld'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-pekerjaan" name="new_pekerjaan" class="wf-input" value="{{ $selectedData['pekerjaanNew'] ?? '' }}" placeholder="Contoh: Karyawan Swasta, Wiraswasta, PNS, Pelajar, dll.">
                                        </div>
                                    </td>
                                </tr>

                                {{-- 9. Alamat --}}
                                <tr id="row-alamat">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>9. Alamat Domisili</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Tempat tinggal saat ini di lingkungan RT 04</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-alamat">{{ $selectedData['alamat'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-alamat" name="new_alamat" class="wf-input" value="{{ $selectedData['alamat'] ?? '' }}" placeholder="Contoh: Blok B4 No. 12">
                                            <span class="wf-help-text" style="font-size:11px; color:#64748b; margin-top:4px; display:block;">
                                                Contoh: <strong>Blok A No. 05</strong> atau <strong>Blok B4 No. 12</strong> (hanya blok &amp; nomor rumah di RT 04).
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 10. Status Hubungan Keluarga --}}
                                <tr id="row-shdk">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>10. Hubungan Keluarga (SHDK)</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Kedudukan dalam Kartu Keluarga</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-shdk">{{ $selectedData['shdk'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-shdk" name="new_shdk" class="wf-select">
                                                @foreach(['Kepala Keluarga', 'Suami', 'Istri', 'Anak Kandung', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Lainnya'] as $sh)
                                                    <option value="{{ $sh }}" {{ ($selectedData['shdk'] ?? '') === $sh ? 'selected' : '' }}>{{ $sh }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 11. Status Warga --}}
                                <tr id="row-status-warga">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>11. Status Warga</span>
                                            <span class="wf-badge-updated">● Diubah</span>
                                        </div>
                                        <div class="wf-param-desc">Status kependudukan / keberadaan</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-status-warga">{{ $selectedData['statusWarga'] ?? '-' }}</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-status-warga" name="new_status_warga" class="wf-select">
                                                @foreach(['Hidup (Aktif)', 'Pindah KK', 'Pindah Rumah', 'Meninggal'] as $sw)
                                                    <option value="{{ $sw }}" {{ ($selectedData['statusWarga'] ?? '') === $sw ? 'selected' : '' }}>{{ $sw }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        </div>

                        {{-- Legend --}}
                        <div class="wf-table-legend">
                            <div class="wf-table-legend-item">
                                <span class="wf-legend-dot" style="background:#94a3b8;"></span>
                                <span>Kolom Abu-abu: Data eksisting terkunci (read-only)</span>
                            </div>
                            <div class="wf-table-legend-item">
                                <span class="wf-legend-dot" style="background:#16a34a;"></span>
                                <span>Sorotan Hijau: Menandakan field telah diubah dari data lama</span>
                            </div>
                        </div>

                        {{-- Alasan / Keterangan --}}
                        <div class="wf-form-group" style="margin-top:22px; margin-bottom:0;">
                            <label class="wf-label" for="alasan_perubahan">
                                <span>Alasan / Keterangan Pengajuan Perubahan <span class="wf-req">*</span></span>
                            </label>
                            <textarea id="alasan_perubahan" name="alasan_perubahan" rows="3" class="wf-textarea" placeholder="Tuliskan alasan lengkap permohonan perubahan data yang diajukan (contoh: pembaruan jenjang pendidikan setelah kelulusan, penggantian pekerjaan, perbaikan ejaan nama, dll)..." required></textarea>
                            <div class="wf-help-text" style="font-size:11.5px;color:#64748b;margin-top:5px;">
                                ⓘ Keterangan yang jelas membantu Ketua RT memverifikasi berkas Anda lebih cepat.
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ── KOLOM SAMPING (KANAN) ── --}}
            <div class="warga-form-side">
                
                {{-- CARD 3: Bukti Dokumen Pendukung --}}
                <div class="wf-card">
                    <div class="wf-card__header-light">
                        <div class="wf-header-title">
                            <i class='bx bx-cloud-upload' style="color:#0284c7;font-size:19px;"></i>
                            <span>3. Dokumen Bukti Pendukung</span>
                        </div>
                        <span class="wf-badge-step-blue">LANGKAH 3</span>
                    </div>
                    <div class="wf-card__body">

                        {{-- Kotak Edukasi / Petunjuk Ramah untuk Orang Awam --}}
                        <div class="wf-upload-info-box">
                            <div class="wf-upload-info-box__icon">
                                <i class='bx bx-info-circle'></i>
                            </div>
                            <div class="wf-upload-info-box__content">
                                <div class="wf-upload-info-box__title">
                                    <i class='bx bx-check-double' style="color:#16a34a;"></i>
                                    Boleh &amp; Bisa Unggah Lebih Dari 1 Bukti
                                </div>
                                <div class="wf-upload-info-box__desc">
                                    Anda dapat melampirkan beberapa foto/dokumen sekaligus (misal: <strong>KTP + Ijazah Baru + Akta</strong>). Klik area di bawah untuk memilih file dari HP atau Komputer Anda.
                                </div>
                            </div>
                        </div>

                        {{-- Dropzone --}}
                        <div class="wf-upload-dropzone" onclick="document.getElementById('file-input-edit').click()" style="cursor: pointer;">
                            <input type="file" id="file-input-edit" name="bukti[]" style="display:none;" multiple accept=".pdf,.jpg,.jpeg,.png">
                            <div class="wf-upload-icon-circle">
                                <i class='bx bx-cloud-upload' style="font-size:26px;color:#0284c7;"></i>
                            </div>
                            <div class="wf-upload-text-main">
                                <i class='bx bx-pointer'></i> Klik di Sini untuk Memilih / Unggah Berkas
                            </div>
                            <div class="wf-upload-text-sub">
                                atau seret &amp; lepas (drag &amp; drop) berkas ke dalam kotak ini
                            </div>

                            <div class="wf-upload-multi-badge">
                                <i class='bx bx-layer-plus'></i>
                                <span>Bisa Upload Banyak Bukti Sekaligus</span>
                            </div>

                            <div class="wf-upload-text-hint">
                                Format didukung: <strong>PDF, JPG, JPEG, PNG</strong> (Maksimal 5 MB per berkas)
                            </div>
                        </div>

                        {{-- Berkas Terpilih --}}
                        <div class="wf-attached-section" id="edit-attached-section" style="display:none;margin-top:14px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                                <div class="wf-attached-title" id="edit-attached-title" style="margin-bottom:0;">BERKAS TERPILIH (0 BERKAS)</div>
                                <button type="button" class="wf-btn-add-more" onclick="document.getElementById('file-input-edit').click()">
                                    <i class='bx bx-plus-circle'></i> Tambah Bukti Lain
                                </button>
                            </div>
                            <div class="wf-file-list" id="edit-file-list"></div>
                        </div>

                        <div id="edit-empty-file-notice" class="wf-empty-file-notice">
                            <i class='bx bx-file-blank' style="font-size:16px; color:#94a3b8; flex-shrink:0;"></i>
                            <span>Belum ada dokumen yang dipilih. Contoh: KTP, Akta Kelahiran, Ijazah, atau Surat Nikah.</span>
                        </div>

                        {{-- Contoh Dokumen yang Dianjurkan --}}
                        <div class="wf-upload-examples-box">
                            <div class="wf-upload-examples-title">
                                <i class='bx bx-book-bookmark' style="color:#0284c7;"></i>
                                Contoh Dokumen Sesuai Data yang Diubah:
                            </div>
                            <ul class="wf-upload-examples-list">
                                <li><strong>Ubah Nama / Tgl Lahir:</strong> Akta Kelahiran atau Ijazah.</li>
                                <li><strong>Ubah Pendidikan:</strong> Ijazah kelulusan terakhir atau Transkrip nilai.</li>
                                <li><strong>Ubah Pekerjaan:</strong> SK Kerja / Kartu Pegawai / Surat Keterangan Usaha.</li>
                                <li><strong>Pindah KK / Meninggal:</strong> Surat Keterangan Pindah / Surat Kematian.</li>
                            </ul>
                        </div>

                    </div>
                </div>

                {{-- CARD 4: Pernyataan & Eksekusi Pengajuan --}}
                <div class="wf-card">
                    <div class="wf-card__header-light">
                        <div class="wf-header-title">
                            <i class='bx bx-shield-quarter' style="color:#16a34a;font-size:19px;"></i>
                            <span>4. Pernyataan &amp; Eksekusi</span>
                        </div>
                    </div>
                    <div class="wf-card__body">
                        
                        <div class="wf-checkbox-wrap">
                            <input type="checkbox" id="persetujuan-edit" class="wf-checkbox" required>
                            <label for="persetujuan-edit" style="cursor:pointer;font-size:12.5px;">
                                Saya menyatakan bahwa data dan dokumen yang saya ajukan adalah <strong>benar, sah, dan dapat dipertanggungjawabkan</strong> secara hukum kependudukan.
                            </label>
                        </div>

                        <button type="button" class="wf-btn-submit-green" id="btn-trigger-modal-edit">
                            <i class='bx bx-paper-plane' style="font-size:18px;"></i>
                            <span>Kirim Pengajuan Sekarang</span>
                        </button>

                        <a href="{{ route('warga.keluarga') }}" class="wf-btn-cancel">
                            <i class='bx bx-x-circle'></i>
                            <span>Batal</span>
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- ── MODAL KONFIRMASI EDIT ── --}}
<div class="wf-modal-overlay" id="modal-konfirmasi-edit">
    <div class="wf-modal-box">
        <div class="wf-modal-icon-circle">
            <i class='bx bx-help-circle'></i>
        </div>
        <h2 class="wf-modal-heading">
            Konfirmasi Pengajuan Perubahan
        </h2>
        <p class="wf-modal-desc">
            Apakah Anda yakin ingin mengirim permohonan perubahan data keluarga ini? Data yang diajukan akan diverifikasi dan disetujui terlebih dahulu oleh Ketua RT 04.
        </p>
        <div class="wf-modal-actions">
            <button type="button" class="wf-modal-btn-batal" id="btn-modal-edit-batal">
                Periksa Kembali
            </button>
            <button type="button" class="wf-modal-btn-konfirmasi" id="btn-modal-edit-konfirmasi">
                <i class='bx bx-check'></i>
                <span>Ya, Kirim Pengajuan</span>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnTrigger = document.getElementById('btn-trigger-modal-edit');
    const modal = document.getElementById('modal-konfirmasi-edit');
    const btnBatal = document.getElementById('btn-modal-edit-batal');
    const btnKonfirmasi = document.getElementById('btn-modal-edit-konfirmasi');
    const checkboxPersetujuan = document.getElementById('persetujuan-edit');
    const form = document.getElementById('form-edit-keluarga');

    btnTrigger.addEventListener('click', function () {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        if (!checkboxPersetujuan.checked) {
            alert('Silakan centang pernyataan persetujuan terlebih dahulu sebelum mengirim pengajuan.');
            checkboxPersetujuan.focus();
            return;
        }
        modal.classList.add('active');
    });

    btnBatal.addEventListener('click', function () {
        modal.classList.remove('active');
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            modal.classList.remove('active');
        }
    });

    btnKonfirmasi.addEventListener('click', function () {
        modal.classList.remove('active');
        form.submit();
    });

    // Dynamic File Upload Management
    const fileInputEdit = document.getElementById('file-input-edit');
    const editFileList = document.getElementById('edit-file-list');
    const editAttachedSection = document.getElementById('edit-attached-section');
    const editEmptyNotice = document.getElementById('edit-empty-file-notice');
    const editAttachedTitle = document.getElementById('edit-attached-title');
    let selectedFiles = [];

    if (fileInputEdit) {
        fileInputEdit.addEventListener('change', function (e) {
            Array.from(e.target.files).forEach(f => selectedFiles.push(f));
            e.target.value = '';
            renderFileList();
        });
    }

    const dropzone = document.querySelector('.wf-upload-dropzone');
    if (dropzone) {
        dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.style.borderColor = '#007bff'; });
        dropzone.addEventListener('dragleave', () => { dropzone.style.borderColor = '#93c5fd'; });
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#93c5fd';
            if (e.dataTransfer.files.length) {
                Array.from(e.dataTransfer.files).forEach(f => selectedFiles.push(f));
                renderFileList();
            }
        });
    }

    function renderFileList() {
        if (!editAttachedSection || !editEmptyNotice) return;
        if (selectedFiles.length === 0) {
            editAttachedSection.style.display = 'none';
            editEmptyNotice.style.display = 'block';
        } else {
            editAttachedSection.style.display = 'block';
            editEmptyNotice.style.display = 'none';
            editAttachedTitle.innerText = `BERKAS TERPILIH (${selectedFiles.length} BERKAS)`;
            editFileList.innerHTML = selectedFiles.map((file, idx) => {
                const size = file.size ? (file.size / 1024 < 1024 ? (file.size / 1024).toFixed(1) + ' KB' : (file.size / (1024 * 1024)).toFixed(1) + ' MB') : 'Dokumen';
                const isPdf = file.name.toLowerCase().endsWith('.pdf');
                return `
                    <div class="wf-file-item">
                        <div class="wf-file-info">
                            <i class='bx ${isPdf ? 'bxs-file-pdf wf-file-icon-pdf' : 'bxs-file-image wf-file-icon-img'}'></i>
                            <div style="min-width:0;">
                                <div class="wf-file-name" title="${file.name}">${file.name}</div>
                                <div style="font-size:11px;color:#6c757d;">
                                    <span>${size}</span> • <span style="color:#28a745;">Siap diunggah</span>
                                </div>
                            </div>
                        </div>
                        <div class="wf-file-actions">
                            <button type="button" class="wf-file-btn" title="Lihat Berkas" onclick="previewSelectedFile(${idx})">
                                <i class='bx bx-show'></i>
                            </button>
                            <button type="button" class="wf-file-btn wf-file-btn--delete" title="Hapus Berkas" onclick="removeSelectedFile(${idx})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }
    }

    window.previewSelectedFile = function (idx) {
        const file = selectedFiles[idx];
        if (file) {
            const fileUrl = URL.createObjectURL(file);
            const win = window.open(fileUrl, '_blank');
            if (!win) {
                const a = document.createElement('a');
                a.href = fileUrl;
                a.target = '_blank';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        }
    };

    window.removeSelectedFile = function (idx) {
        selectedFiles.splice(idx, 1);
        renderFileList();
    };

    // Setup Realtime Dynamic Difference Evaluation for All 11 Rows
    initComparisonListeners();

    function syncFileInput() {
        if (!fileInputEdit) return;
        try {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            fileInputEdit.files = dt.files;
        } catch(e) {}
    }

    const origRenderFileList = renderFileList;
    renderFileList = function() {
        origRenderFileList();
        syncFileInput();
    };

    // Auto-select target from query param if available
    const urlParams = new URLSearchParams(window.location.search);
    const targetParam = urlParams.get('nik') || urlParams.get('target') || "{{ $targetNik ?? '' }}";
    if (targetParam && anggotaData[targetParam]) {
        const selectEl = document.getElementById('select-target-anggota');
        if (selectEl) {
            selectEl.value = targetParam;
            gantiAnggota(targetParam);
        }
    } else {
        const firstKey = Object.keys(anggotaData)[0];
        if (firstKey) {
            gantiAnggota(firstKey);
        } else {
            evaluateAllRows();
        }
    }
});

// Dynamic Data Loading when switching members
const anggotaData = @json($anggotaDataEdit);

const comparisonFields = [
    { rowId: 'row-nik', oldId: 'old-nik', newId: 'new-nik' },
    { rowId: 'row-nama', oldId: 'old-nama', newId: 'new-nama' },
    { rowId: 'row-tempat-lahir', oldId: 'old-tempat-lahir', newId: 'new-tempat-lahir' },
    { rowId: 'row-tgl-lahir', oldId: 'old-tgl-lahir', newId: 'new-tgl-lahir', isDate: true },
    { rowId: 'row-jk', oldId: 'old-jk', newId: 'new-jk' },
    { rowId: 'row-agama', oldId: 'old-agama', newId: 'new-agama' },
    { rowId: 'row-pendidikan', oldId: 'old-pendidikan', newId: 'new-pendidikan' },
    { rowId: 'row-pekerjaan', oldId: 'old-pekerjaan', newId: 'new-pekerjaan' },
    { rowId: 'row-alamat', oldId: 'old-alamat', newId: 'new-alamat' },
    { rowId: 'row-shdk', oldId: 'old-shdk', newId: 'new-shdk' },
    { rowId: 'row-status-warga', oldId: 'old-status-warga', newId: 'new-status-warga' }
];

function checkRowDifference(item) {
    const rowEl = document.getElementById(item.rowId);
    const oldEl = document.getElementById(item.oldId);
    const newEl = document.getElementById(item.newId);
    if (!rowEl || !oldEl || !newEl) return;

    let oldVal = oldEl.innerText.trim();
    let newVal = newEl.value.trim();

    if (item.isDate && newVal.includes('-')) {
        const parts = newVal.split('-');
        if (parts.length === 3 && parts[0].length === 4) {
            newVal = `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
    }

    if (newVal !== '' && newVal.toLowerCase() !== oldVal.toLowerCase()) {
        rowEl.classList.add('wf-row-updated');
    } else {
        rowEl.classList.remove('wf-row-updated');
    }
}

function evaluateAllRows() {
    comparisonFields.forEach(checkRowDifference);
}

function initComparisonListeners() {
    comparisonFields.forEach(item => {
        const newEl = document.getElementById(item.newId);
        if (newEl) {
            newEl.addEventListener('input', () => checkRowDifference(item));
            newEl.addEventListener('change', () => checkRowDifference(item));
        }
    });
}

function gantiAnggota(key) {
    const data = anggotaData[key];
    if (!data) return;

    // Perbarui widget kartu preview anggota
    const previewAvatar = document.getElementById('preview-avatar');
    const previewName = document.getElementById('preview-name');
    const previewNik = document.getElementById('preview-nik');
    const previewShdk = document.getElementById('preview-shdk');

    if (previewAvatar) previewAvatar.innerText = data.avatar || 'WG';
    if (previewName) previewName.innerText = data.nama;
    if (previewNik) previewNik.innerText = data.nik;
    if (previewShdk) previewShdk.innerText = data.shdk;

    // Perbarui data tabel
    document.getElementById('old-nik').innerText = data.nik;
    document.getElementById('new-nik').value = data.nik;

    document.getElementById('old-nama').innerText = data.nama;
    document.getElementById('new-nama').value = data.nama;

    document.getElementById('old-tempat-lahir').innerText = data.tempatLahir;
    document.getElementById('new-tempat-lahir').value = data.tempatLahir;

    document.getElementById('old-tgl-lahir').innerText = data.tglLahir;
    document.getElementById('new-tgl-lahir').value = data.tglLahirNew;

    document.getElementById('old-jk').innerText = data.jk;
    document.getElementById('new-jk').value = data.jk;

    document.getElementById('old-agama').innerText = data.agama;
    document.getElementById('new-agama').value = data.agama;

    document.getElementById('old-pendidikan').innerText = data.pendidikanOld;
    document.getElementById('new-pendidikan').value = data.pendidikanNew;

    document.getElementById('old-pekerjaan').innerText = data.pekerjaanOld;
    document.getElementById('new-pekerjaan').value = data.pekerjaanNew;

    document.getElementById('old-alamat').innerText = data.alamat;
    document.getElementById('new-alamat').value = data.alamat;

    document.getElementById('old-shdk').innerText = data.shdk;
    document.getElementById('new-shdk').value = data.shdk;

    document.getElementById('old-status-warga').innerText = data.statusWarga;
    document.getElementById('new-status-warga').value = data.statusWarga;

    document.getElementById('alasan_perubahan').value = data.alasan;

    // Evaluasi ulang semua baris komparasi untuk anggota terpilih
    evaluateAllRows();
}
</script>
@endpush
