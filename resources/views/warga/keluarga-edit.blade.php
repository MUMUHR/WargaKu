@extends('layouts.warga')

@section('title', 'Form Pengajuan Perubahan Data Warga')
@section('meta_description', 'Formulir Pengajuan Perubahan Data Warga — WargaKu RT 04 / RW 08')

@section('topbar_section')
    <a href="{{ route('warga.keluarga') }}" class="admin-topbar__breadcrumb-link">Data Warga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Form Pengajuan Perubahan Data</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-form-keluarga.css') }}">
@endpush

@section('content')

{{-- ── Breadcrumb & Header ── --}}
<div class="warga-form-page">
    <div style="margin-bottom: 20px;">
        <nav style="font-size:12px;color:#6c757d;margin-bottom:6px;display:flex;align-items:center;gap:6px;" aria-label="Breadcrumb">
            <a href="{{ route('warga.keluarga') }}" style="color:#6c757d;text-decoration:none;">Data Warga</a>
            <span>/</span>
            <span style="color:#007bff;font-weight:600;">Form Pengajuan Perubahan Data</span>
        </nav>
        <h1 style="font-size:22px;font-weight:800;color:#111827;margin-bottom:4px;">
            Formulir Pengajuan Perubahan Data Warga
        </h1>
        <p style="font-size:13px;color:#6c757d;margin:0;">
            Sampaikan permohonan koreksi atau pembaruan data kependudukan ke pengurus RT 04
        </p>
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
                            <i class='bx bx-id-card' style="color:#007bff;font-size:19px;"></i>
                            <span>1. Identitas Pemohon &amp; Sasaran Perubahan</span>
                        </div>
                        <span class="wf-badge-step-blue">LANGKAH 1</span>
                    </div>
                    <div class="wf-card__body">
                        
                        <div style="display:flex;align-items:center;justify-content:space-between;background:#f8f9fa;border:1px solid #e9ecef;border-radius:4px;padding:10px 16px;margin-bottom:16px;">
                            <div style="font-size:12.5px;color:#495057;">
                                Kepala Keluarga: <strong style="color:#212529;">Bambang Pamungkas</strong>
                            </div>
                            <div style="font-size:12px;color:#6c757d;display:flex;align-items:center;gap:6px;">
                                <span>No. Kartu Keluarga (KK):</span>
                                <span style="background:#e7f1ff;color:#007bff;font-weight:700;padding:2px 8px;border-radius:4px;font-size:12px;">3271048809920001</span>
                            </div>
                        </div>

                        <div>
                            <label class="wf-label" for="select-target-anggota">
                                <span>Pilih Anggota Keluarga yang Ingin Diubah <span class="wf-req">*</span></span>
                            </label>
                            <select id="select-target-anggota" name="target_anggota" class="wf-select" onchange="gantiAnggota(this.value)" required>
                                <option value="dimas" selected>Dimas Arya Pratama — (Anak Kandung | NIK: 3271048809920003)</option>
                                <option value="bambang">Bpk. Bambang Pamungkas — (Kepala Keluarga | NIK: 3271041203780002)</option>
                                <option value="siti">Siti Aminah — (Istri | NIK: 3271046204830002)</option>
                                <option value="nabila">Nabila Putri Kirani — (Anak Kandung | NIK: 3271045011120004)</option>
                            </select>
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
                        
                        <table class="wf-comparison-table">
                            <thead>
                                <tr>
                                    <th class="col-param">NAMA FIELD / PARAMETER</th>
                                    <th class="col-old">
                                        <i class='bx bx-lock-alt' style="vertical-align:middle;font-size:13px;"></i>
                                        DATA LAMA / EKSISTING (READ-ONLY)
                                    </th>
                                    <th class="col-new">
                                        <i class='bx bx-edit' style="vertical-align:middle;font-size:13px;"></i>
                                        DATA BARU YANG DIAJUKAN (DAPAT DIEDIT)
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody-komparasi">
                                
                                {{-- 1. NIK --}}
                                <tr id="row-nik">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>1. NIK</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Nomor Induk Kependudukan (16 digit)</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-nik">3271048809920003</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-nik" name="new_nik" class="wf-input" value="3271048809920003">
                                            <i class='bx bx-pencil edit-icon-indicator'></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 2. Nama Lengkap --}}
                                <tr id="row-nama">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>2. Nama Lengkap</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Sesuai Akta / KTP-el</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-nama">Dimas Arya Pratama</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-nama" name="new_nama" class="wf-input" value="Dimas Arya Pratama">
                                            <i class='bx bx-pencil edit-icon-indicator'></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 3. Tempat Lahir --}}
                                <tr id="row-tempat-lahir">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>3. Tempat Lahir</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Kota / Kabupaten kelahiran</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-tempat-lahir">Bogor</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-tempat-lahir" name="new_tempat_lahir" class="wf-input" value="Bogor">
                                            <i class='bx bx-pencil edit-icon-indicator'></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 4. Tanggal Lahir --}}
                                <tr id="row-tgl-lahir">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>4. Tanggal Lahir</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Format: Tanggal / Bulan / Tahun</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-tgl-lahir">08-08-2005</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-tgl-lahir" name="new_tgl_lahir" class="wf-input" value="08-08-2005">
                                            <i class='bx bx-pencil edit-icon-indicator'></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 5. Jenis Kelamin --}}
                                <tr id="row-jk">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>5. Jenis Kelamin</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Sesuai identitas hukum</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-jk">Laki-Laki</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-jk" name="new_jk" class="wf-select">
                                                <option value="Laki-Laki" selected>Laki-Laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                            <i class='bx bx-pencil edit-icon-indicator' style="right:28px;"></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 6. Agama --}}
                                <tr id="row-agama">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>6. Agama</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Agama resmi terdaftar</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-agama">Islam</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-agama" name="new_agama" class="wf-select">
                                                <option value="Islam" selected>Islam</option>
                                                <option value="Kristen Protestan">Kristen Protestan</option>
                                                <option value="Katolik">Katolik</option>
                                                <option value="Hindu">Hindu</option>
                                                <option value="Buddha">Buddha</option>
                                                <option value="Konghucu">Konghucu</option>
                                            </select>
                                            <i class='bx bx-pencil edit-icon-indicator' style="right:28px;"></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 7. Pendidikan Terakhir --}}
                                <tr id="row-pendidikan">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>7. Pendidikan Terakhir</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Berdasarkan ijazah kelulusan</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-pendidikan">SLTA / Sederajat</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-pendidikan" name="new_pendidikan" class="wf-select">
                                                <option value="Tidak / Belum Sekolah">Tidak / Belum Sekolah (tidak_belum_sekolah)</option>
                                                <option value="Belum Tamat SD / Sederajat">Belum Tamat SD / Sederajat (belum_tamat_sd)</option>
                                                <option value="SD / Sederajat">SD / Sederajat (sd_sederajat)</option>
                                                <option value="SMP / Sederajat">SMP / Sederajat (smp_sederajat)</option>
                                                <option value="SLTA / Sederajat" selected>SLTA / SMA / SMK / Sederajat (sma_sederajat)</option>
                                                <option value="Diploma I / II">Diploma I / II (d1_d2)</option>
                                                <option value="Diploma III">Diploma III (d3)</option>
                                                <option value="Diploma IV / Strata 1 (S1)">Diploma IV / Strata 1 (S1) (d4_s1)</option>
                                                <option value="Strata 2 (S2)">Strata 2 (S2) (s2)</option>
                                                <option value="Strata 3 (S3)">Strata 3 (S3) (s3)</option>
                                            </select>
                                            <i class='bx bx-pencil edit-icon-indicator' style="right:28px;"></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 8. Pekerjaan --}}
                                <tr id="row-pekerjaan">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>8. Pekerjaan</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Profesi / Mata Pencaharian</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-pekerjaan">Pelajar / Mahasiswa</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-pekerjaan" name="new_pekerjaan" class="wf-select">
                                                <option value="Pelajar / Mahasiswa" selected>Pelajar / Mahasiswa</option>
                                                <option value="Belum / Tidak Bekerja">Belum / Tidak Bekerja</option>
                                                <option value="Karyawan Swasta">Karyawan Swasta</option>
                                                <option value="Pegawai Negeri Sipil (PNS)">Pegawai Negeri Sipil (PNS) / ASN</option>
                                                <option value="Wiraswasta / Pedagang">Wiraswasta / Pedagang / Pengusaha</option>
                                                <option value="Buruh Harian Lepas">Buruh Harian Lepas / Pabrik</option>
                                                <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                                <option value="Pensiunan">Pensiunan</option>
                                                <option value="Guru / Dosen">Guru / Dosen / Tenaga Pendidik</option>
                                                <option value="Tenaga Medis">Tenaga Medis / Dokter / Perawat</option>
                                                <option value="TNI / POLRI">TNI / POLRI</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                            <i class='bx bx-pencil edit-icon-indicator' style="right:28px;"></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 9. Alamat --}}
                                <tr id="row-alamat">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>9. Alamat</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Alamat tempat tinggal saat ini</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-alamat">Jl. Melati Blok B No. 14, RT 04 / RW 08</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <input type="text" id="new-alamat" name="new_alamat" class="wf-input" value="Jl. Melati Blok B No. 14, RT 04 / RW 08">
                                            <i class='bx bx-pencil edit-icon-indicator'></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 10. Status Hubungan Keluarga --}}
                                <tr id="row-shdk">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>10. Status Hubungan Keluarga</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Kedudukan dalam Kartu Keluarga</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-shdk">Anak Kandung</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-shdk" name="new_shdk" class="wf-select">
                                                <option value="Anak Kandung" selected>Anak Kandung (anak)</option>
                                                <option value="Kepala Keluarga">Kepala Keluarga (kepala_keluarga)</option>
                                                <option value="Suami">Suami (suami)</option>
                                                <option value="Istri">Istri (istri)</option>
                                                <option value="Menantu">Menantu (menantu)</option>
                                                <option value="Cucu">Cucu (cucu)</option>
                                                <option value="Orang Tua">Orang Tua (orang_tua)</option>
                                                <option value="Mertua">Mertua (mertua)</option>
                                                <option value="Famili Lain">Famili Lain (famili_lain)</option>
                                                <option value="Lainnya">Lainnya (lainnya)</option>
                                            </select>
                                            <i class='bx bx-pencil edit-icon-indicator' style="right:28px;"></i>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 11. Status Warga --}}
                                <tr id="row-status-warga">
                                    <td>
                                        <div class="wf-param-title">
                                            <span>11. Status Warga</span>
                                            <span class="wf-badge-updated">● Diperbarui</span>
                                        </div>
                                        <div class="wf-param-desc">Status kependudukan / keberadaan</div>
                                    </td>
                                    <td>
                                        <div class="wf-input-old-lock">
                                            <span id="old-status-warga">Hidup (Aktif)</span>
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wf-input-editable-wrap">
                                            <select id="new-status-warga" name="new_status_warga" class="wf-select">
                                                <option value="Hidup (Aktif)" selected>Hidup (Aktif)</option>
                                                <option value="Pindah KK">Pindah KK</option>
                                                <option value="Pindah Rumah">Pindah Rumah</option>
                                                <option value="Meninggal">Meninggal</option>
                                            </select>
                                            <i class='bx bx-pencil edit-icon-indicator' style="right:28px;"></i>
                                        </div>
                                        <div class="wf-help-text" style="font-size:11px;">
                                            ⓘ Pilih 'Pindah KK' jika warga membuat KK baru, atau 'Pindah Rumah' jika keluar dari wilayah RT.
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        {{-- Alasan / Keterangan --}}
                        <div class="wf-form-group" style="margin-top:20px; margin-bottom:0;">
                            <label class="wf-label" for="alasan_perubahan">
                                <span>Alasan / Keterangan Pengajuan Perubahan <span class="wf-req">*</span></span>
                            </label>
                            <textarea id="alasan_perubahan" name="alasan_perubahan" rows="4" class="wf-textarea" placeholder="Tuliskan alasan atau keterangan lengkap permohonan perubahan data yang diajukan (contoh: pembaruan jenjang pendidikan setelah kelulusan, perubahan jenis pekerjaan/profesi baru, koreksi data, dll)..." required></textarea>
                            <div class="wf-help-text" style="font-size:11.5px;color:#6c757d;">
                                ⓘ Penjelasan membantu Ketua RT mempercepat proses otentikasi berkas Anda.
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
                            <i class='bx bx-cloud-upload' style="color:#28a745;font-size:19px;"></i>
                            <span>3. Bukti Dokumen Pendukung</span>
                        </div>
                        <span class="wf-badge-step-green">LANGKAH 3</span>
                    </div>
                    <div class="wf-card__body">
                        
                        {{-- Dropzone --}}
                        <div class="wf-upload-dropzone" onclick="document.getElementById('file-input-edit').click()">
                            <input type="file" id="file-input-edit" name="bukti" style="display:none;" multiple accept=".pdf,.jpg,.jpeg,.png">
                            <div class="wf-upload-icon-circle">
                                <i class='bx bx-file'></i>
                            </div>
                            <div class="wf-upload-text-main">Tarik &amp; lepas berkas di sini, atau <span style="color:#007bff;text-decoration:underline;">Pilih File</span></div>
                            <div class="wf-upload-text-hint">Format yang didukung: PDF, JPG, PNG (Maks. 5 MB per berkas)</div>
                        </div>

                        {{-- Berkas Terpilih (Awalnya Kosong) --}}
                        <div class="wf-attached-section" id="edit-attached-section" style="display:none;">
                            <div class="wf-attached-title" id="edit-attached-title">BERKAS TERPILIH (0 BERKAS)</div>
                            <div class="wf-file-list" id="edit-file-list"></div>
                        </div>

                        <div id="edit-empty-file-notice" style="margin-top:16px; padding:14px; border-radius:6px; background:#f8fafc; border:1px dashed #cbd5e1; text-align:center; font-size:12px; color:#64748b;">
                            <i class='bx bx-info-circle' style="font-size:16px; vertical-align:middle; color:#0284c7; margin-right:4px;"></i>
                            Belum ada dokumen yang diunggah. Silakan klik atau tarik berkas ke area di atas.
                        </div>

                    </div>
                </div>

                {{-- CARD 4: Pernyataan & Eksekusi Pengajuan --}}
                <div class="wf-card">
                    <div class="wf-card__header-dark">
                        <div class="wf-header-title">
                            <i class='bx bx-shield-quarter' style="font-size:18px;"></i>
                            <span>4. Pernyataan &amp; Eksekusi Pengajuan</span>
                        </div>
                    </div>
                    <div class="wf-card__body">
                        
                        <div class="wf-checkbox-wrap">
                            <input type="checkbox" id="persetujuan-edit" class="wf-checkbox" required>
                            <label for="persetujuan-edit" style="cursor:pointer;">
                                Saya menyatakan bahwa data dan dokumen yang saya ajukan adalah <strong>benar, sah, dan dapat dipertanggungjawabkan secara hukum</strong>. Pemalsuan dokumen dapat dikenakan sanksi administrasi kependudukan.
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

{{-- ── MODAL KONFIRMASI EDIT (SCREENSHOT 4) ── --}}
<div class="wf-modal-overlay" id="modal-konfirmasi-edit">
    <div class="wf-modal-box">
        <h2 class="wf-modal-heading">
            Apakah Kamu yakin mengedit data keluarga
        </h2>
        <div class="wf-modal-actions">
            <button type="button" class="wf-modal-btn-batal" id="btn-modal-edit-batal">
                Batal
            </button>
            <button type="button" class="wf-modal-btn-konfirmasi" id="btn-modal-edit-konfirmasi">
                Konfirmasi
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
                            <span class="wf-badge-file-ready">✓ Siap</span>
                            <button type="button" class="wf-file-btn wf-file-btn--delete" title="Hapus Berkas" onclick="removeSelectedFile(${idx})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }
    }

    window.removeSelectedFile = function (idx) {
        selectedFiles.splice(idx, 1);
        renderFileList();
    };

    // Setup Realtime Dynamic Difference Evaluation for All 11 Rows
    initComparisonListeners();

    // Auto-select target from query param if available
    const urlParams = new URLSearchParams(window.location.search);
    const targetParam = urlParams.get('target');
    if (targetParam && anggotaData[targetParam]) {
        const selectEl = document.getElementById('select-target-anggota');
        if (selectEl) {
            selectEl.value = targetParam;
            gantiAnggota(targetParam);
        }
    } else {
        evaluateAllRows();
    }
});

// Dynamic Data Loading when switching members
const anggotaData = {
    dimas: {
        nik: '3271048809920003',
        nama: 'Dimas Arya Pratama',
        tempatLahir: 'Bogor',
        tglLahir: '08-08-2005',
        tglLahirNew: '08-08-2005',
        jk: 'Laki-Laki',
        agama: 'Islam',
        pendidikanOld: 'SLTA / Sederajat',
        pendidikanNew: 'SLTA / Sederajat',
        pekerjaanOld: 'Pelajar / Mahasiswa',
        pekerjaanNew: 'Pelajar / Mahasiswa',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 / RW 08',
        shdk: 'Anak Kandung',
        statusWarga: 'Hidup (Aktif)',
        alasan: ''
    },
    bambang: {
        nik: '3271041203780002',
        nama: 'Bambang Pamungkas',
        tempatLahir: 'Bogor',
        tglLahir: '15-08-1988',
        tglLahirNew: '15-08-1988',
        jk: 'Laki-Laki',
        agama: 'Islam',
        pendidikanOld: 'Diploma IV / Strata 1 (S1)',
        pendidikanNew: 'Diploma IV / Strata 1 (S1)',
        pekerjaanOld: 'Karyawan Swasta',
        pekerjaanNew: 'Karyawan Swasta',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 / RW 08',
        shdk: 'Kepala Keluarga',
        statusWarga: 'Hidup (Aktif)',
        alasan: ''
    },
    siti: {
        nik: '3271046204830002',
        nama: 'Siti Aminah',
        tempatLahir: 'Bandung',
        tglLahir: '12-04-1983',
        tglLahirNew: '12-04-1983',
        jk: 'Perempuan',
        agama: 'Islam',
        pendidikanOld: 'SLTA / Sederajat',
        pendidikanNew: 'SLTA / Sederajat',
        pekerjaanOld: 'Ibu Rumah Tangga',
        pekerjaanNew: 'Ibu Rumah Tangga',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 / RW 08',
        shdk: 'Istri',
        statusWarga: 'Hidup (Aktif)',
        alasan: ''
    },
    nabila: {
        nik: '3271045011120004',
        nama: 'Nabila Putri Kirani',
        tempatLahir: 'Bogor',
        tglLahir: '10-11-2012',
        tglLahirNew: '10-11-2012',
        jk: 'Perempuan',
        agama: 'Islam',
        pendidikanOld: 'SD / Sederajat',
        pendidikanNew: 'SD / Sederajat',
        pekerjaanOld: 'Pelajar / Mahasiswa',
        pekerjaanNew: 'Pelajar / Mahasiswa',
        alamat: 'Jl. Melati Blok B No. 14, RT 04 / RW 08',
        shdk: 'Anak Kandung',
        statusWarga: 'Hidup (Aktif)',
        alasan: ''
    }
};

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

    if (item.isDate) {
        oldVal = oldVal.replace(/\//g, '-');
        newVal = newVal.replace(/\//g, '-');
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
