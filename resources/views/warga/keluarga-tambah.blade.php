@extends('layouts.warga')

@section('title', 'Tambah Anggota Keluarga')
@section('meta_description', 'Form Pendaftaran Anggota Keluarga Baru — WargaKu RT 04 / RW 08')

@section('topbar_section')
    <a href="{{ route('warga.keluarga') }}" class="admin-topbar__breadcrumb-link">Data Keluarga</a>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Tambah Anggota</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-form-keluarga.css') }}">
@endpush

@section('content')

{{-- ── Breadcrumb & Header ── --}}
<div class="warga-form-page">
    <div style="margin-bottom: 20px;">
        <nav style="font-size:12px;color:#6c757d;margin-bottom:6px;display:flex;align-items:center;gap:6px;" aria-label="Breadcrumb">
            <a href="{{ route('warga.beranda') }}" style="color:#6c757d;text-decoration:none;">Home</a>
            <span>/</span>
            <a href="{{ route('warga.keluarga') }}" style="color:#6c757d;text-decoration:none;">Data Warga</a>
            <span>/</span>
            <span style="color:#007bff;font-weight:600;">Tambah Anggota Keluarga Baru</span>
        </nav>
        <h1 style="font-size:22px;font-weight:800;color:#111827;margin-bottom:4px;">
            Form Pendaftaran Anggota Keluarga Baru
        </h1>
        <p style="font-size:13px;color:#6c757d;margin:0;">
            Tambahkan anggota baru ke dalam Kartu Keluarga (kelahiran baru, pernikahan, kepindahan, atau mutasi keluarga) untuk diverifikasi oleh Pengurus RT 04.
        </p>
    </div>

    <form id="form-tambah-anggota" action="{{ route('warga.keluarga.tambah.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="warga-form-grid">
            {{-- ── KOLOM UTAMA (KIRI) ── --}}
            <div class="warga-form-main">
                
                {{-- CARD 1: Informasi Target Kartu Keluarga (KK) --}}
                <div class="wf-card">
                    <div class="wf-card__header-dark">
                        <div class="wf-header-title">
                            <i class='bx bx-id-card' style="font-size:18px;"></i>
                            <span>1. Informasi Target Kartu Keluarga (KK)</span>
                        </div>
                        <span class="wf-badge-verified-green">
                            <i class='bx bx-check'></i> Terverifikasi Dukcapil
                        </span>
                    </div>
                    <div class="wf-card__body">
                        <div class="wf-kk-summary">
                            <div class="wf-kk-item">
                                <span class="wf-kk-label">NOMOR KARTU KELUARGA (KELUARGA.NO_KK)</span>
                                <span class="wf-kk-value-nkk">3271048809920001</span>
                            </div>
                            <div class="wf-kk-item">
                                <span class="wf-kk-label">KEPALA KELUARGA AKTIF</span>
                                <span class="wf-kk-value-head">Bpk. Bambang Pamungkas</span>
                                <span class="wf-kk-subhead">NIK: 3271041203780002</span>
                            </div>
                            <div class="wf-kk-item">
                                <span class="wf-kk-label">ALAMAT TERDAFTAR</span>
                                <span class="wf-kk-value-address">Jl. Melati Blok B No. 14</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Formulir Biodata Warga Baru --}}
                <div class="wf-card">
                    <div class="wf-card__header-dark">
                        <div class="wf-header-title">
                            <i class='bx bx-user-plus' style="font-size:18px;"></i>
                            <span>2. Formulir Biodata Warga Baru</span>
                        </div>
                    </div>
                    <div class="wf-card__body">
                        
                        {{-- NIK --}}
                        <div class="wf-form-group">
                            <label class="wf-label" for="nik">
                                <span>Nomor Induk Kependudukan (NIK 16 Digit) <span class="wf-req">*</span> <span style="font-weight:400;color:#6c757d;font-size:11.5px;">(opsional bila belum terbit)</span></span>
                            </label>
                            <div class="wf-input-wrap">
                                <i class='bx bx-id-card wf-input-icon'></i>
                                <input type="text" id="nik" name="nik" class="wf-input wf-input-with-icon" 
                                       placeholder="NIK AKAN DITERBITKAN DUKCAPIL BERSAMA AKTA LAHIR" 
                                       value="">
                            </div>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div class="wf-form-group">
                            <label class="wf-label" for="nama_lengkap">
                                <span>Nama Lengkap (Sesuai Akta Kelahiran / KTP) <span class="wf-req">*</span></span>
                                <span class="wf-label-hint">Maks. 100 Karakter</span>
                            </label>
                            <div class="wf-input-wrap">
                                <i class='bx bx-user wf-input-icon'></i>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" class="wf-input wf-input-with-icon" 
                                       placeholder="CONTOH: MUHAMMAD RAYHAN PAMUNGKAS" required>
                            </div>
                        </div>

                        {{-- Tempat & Tanggal Lahir --}}
                        <div class="wf-form-row">
                            <div>
                                <label class="wf-label" for="tempat_lahir">
                                    <span>Tempat Lahir <span class="wf-req">*</span></span>
                                </label>
                                <div class="wf-input-wrap">
                                    <i class='bx bx-buildings wf-input-icon'></i>
                                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="wf-input wf-input-with-icon" 
                                           placeholder="Contoh: Bogor" required>
                                </div>
                            </div>
                            <div>
                                <label class="wf-label" for="tanggal_lahir">
                                    <span>Tanggal Lahir <span class="wf-req">*</span></span>
                                </label>
                                <div class="wf-input-wrap">
                                    <i class='bx bx-calendar wf-input-icon'></i>
                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="wf-input wf-input-with-icon" required>
                                </div>
                            </div>
                        </div>

                        {{-- Jenis Kelamin & Hubungan SHDK --}}
                        <div class="wf-form-row">
                            <div>
                                <label class="wf-label" for="jenis_kelamin">
                                    <span>Jenis Kelamin <span class="wf-req">*</span></span>
                                </label>
                                <select id="jenis_kelamin" name="jenis_kelamin" class="wf-select" required>
                                    <option value="Laki-Laki">Laki-Laki (laki_laki)</option>
                                    <option value="Perempuan">Perempuan (perempuan)</option>
                                </select>
                            </div>
                            <div>
                                <label class="wf-label" for="hubungan_keluarga">
                                    <span>Hubungan dalam Keluarga (SHDK) <span class="wf-req">*</span></span>
                                </label>
                                <select id="hubungan_keluarga" name="hubungan_keluarga" class="wf-select" required>
                                    <option value="Anak" selected>Anak (anak)</option>
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
                            </div>
                        </div>

                        {{-- Agama & Pendidikan Terakhir --}}
                        <div class="wf-form-row">
                            <div>
                                <label class="wf-label" for="agama">
                                    <span>Agama <span class="wf-req">*</span></span>
                                </label>
                                <select id="agama" name="agama" class="wf-select" required>
                                    <option value="Islam" selected>Islam</option>
                                    <option value="Kristen Protestan">Kristen Protestan</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                            <div>
                                <label class="wf-label" for="pendidikan_terakhir">
                                    <span>Pendidikan Terakhir <span class="wf-req">*</span></span>
                                </label>
                                <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="wf-select" required>
                                    <option value="Tidak / Belum Sekolah" selected>Tidak / Belum Sekolah (tidak_belum_sekolah)</option>
                                    <option value="Belum Tamat SD / Sederajat">Belum Tamat SD / Sederajat (belum_tamat_sd)</option>
                                    <option value="SD / Sederajat">SD / Sederajat (sd_sederajat)</option>
                                    <option value="SMP / Sederajat">SMP / Sederajat (smp_sederajat)</option>
                                    <option value="SLTA / Sederajat">SLTA / SMA / SMK / Sederajat (sma_sederajat)</option>
                                    <option value="Diploma I / II">Diploma I / II (d1_d2)</option>
                                    <option value="Diploma III">Diploma III (d3)</option>
                                    <option value="Diploma IV / Strata 1 (S1)">Diploma IV / Strata 1 (S1) (d4_s1)</option>
                                    <option value="Strata 2 (S2)">Strata 2 (S2) (s2)</option>
                                    <option value="Strata 3 (S3)">Strata 3 (S3) (s3)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Pekerjaan & Status Warga --}}
                        <div class="wf-form-row">
                            <div>
                                <label class="wf-label" for="pekerjaan">
                                    <span>Pekerjaan <span class="wf-req">*</span></span>
                                </label>
                                <select id="pekerjaan" name="pekerjaan" class="wf-select" required>
                                    <option value="Belum / Tidak Bekerja" selected>Belum / Tidak Bekerja</option>
                                    <option value="Pelajar / Mahasiswa">Pelajar / Mahasiswa</option>
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
                            </div>
                            <div>
                                <label class="wf-label">
                                    <span>Status Warga</span>
                                </label>
                                <div class="wf-status-active-pill">
                                    <span class="dot">●</span>
                                    <span>Hidup (Aktif)</span>
                                    <span class="wf-badge-default">✓ Default Sistem</span>
                                </div>
                                <div class="wf-help-text">
                                    Anggota baru otomatis diinisialisasi dengan status hidup aktif.
                                </div>
                            </div>
                        </div>

                        {{-- Alamat Domisili Lengkap --}}
                        <div class="wf-form-group" style="margin-bottom:0;">
                            <label class="wf-label" for="alamat_domisili">
                                <span>Alamat Domisili Lengkap <span class="wf-req">*</span></span>
                            </label>
                            <textarea id="alamat_domisili" name="alamat_domisili" rows="3" class="wf-textarea" required>Jl. Melati Blok B No. 14, RT 04 / RW 08, Kel. Baranangsiang, Kec. Bogor Timur</textarea>
                            <div class="wf-help-text">
                                Diisi sesuai alamat domisili Kartu Keluarga pemohon.
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ── KOLOM SAMPING (KANAN) ── --}}
            <div class="warga-form-side">
                
                {{-- CARD 3: Berkas Persyaratan Wajib --}}
                <div class="wf-card">
                    <div class="wf-card__header-dark">
                        <div class="wf-header-title">
                            <i class='bx bx-folder-open' style="font-size:18px;"></i>
                            <span>3. Berkas Persyaratan Wajib</span>
                        </div>
                        <span class="wf-badge-wajib-red">WAJIB</span>
                    </div>
                    <div class="wf-card__body">
                        
                        {{-- Dropzone --}}
                        <div class="wf-upload-dropzone" id="dropzone-tambah" onclick="document.getElementById('file-input-tambah').click()" style="cursor: pointer;">
                            <input type="file" id="file-input-tambah" name="bukti" style="display:none;" multiple accept=".pdf,.jpg,.jpeg,.png">
                            <div class="wf-upload-icon-circle">
                                <i class='bx bx-cloud-upload'></i>
                            </div>
                            <div class="wf-upload-text-main">Tarik &amp; lepas berkas di sini</div>
                            <div class="wf-upload-text-sub">atau Pilih Berkas dari Komputer</div>
                            <div class="wf-upload-text-hint">Mendukung format PDF, JPG, PNG (Maksimal 2 MB per berkas)</div>
                        </div>

                        {{-- Berkas Terlampir (Default Kosong & Tersembunyi) --}}
                        <div class="wf-attached-section" id="section-berkas-terlampir" style="display: none;">
                            <div class="wf-attached-title" id="judul-berkas-terlampir">BERKAS TERLAMPIR (0 FILE)</div>
                            <div class="wf-file-list" id="tambah-file-list"></div>
                        </div>

                    </div>
                </div>

                {{-- CARD 4: Verifikasi & Eksekusi Pengajuan --}}
                <div class="wf-card">
                    <div class="wf-card__header-dark">
                        <div class="wf-header-title">
                            <i class='bx bx-check-shield' style="font-size:18px;"></i>
                            <span>4. Verifikasi &amp; Eksekusi Pengajuan</span>
                        </div>
                    </div>
                    <div class="wf-card__body">
                        
                        <div class="wf-checkbox-wrap">
                            <input type="checkbox" id="persetujuan" class="wf-checkbox" required>
                            <label for="persetujuan" style="cursor:pointer;">
                                Saya menyatakan dengan sesungguhnya bahwa seluruh data anggota keluarga yang didaftarkan adalah benar, sah, dan sesuai dengan dokumen hukum yang sah. Apabila terdapat ketidaksesuaian data, saya bersedia bertanggung jawab secara hukum.
                            </label>
                        </div>

                        <button type="button" class="wf-btn-submit-green" id="btn-trigger-modal">
                            <i class='bx bx-user-plus' style="font-size:18px;"></i>
                            <span>Daftarkan Anggota Baru</span>
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

{{-- ── MODAL KONFIRMASI (SCREENSHOT 3) ── --}}
<div class="wf-modal-overlay" id="modal-konfirmasi-tambah">
    <div class="wf-modal-box">
        <h2 class="wf-modal-heading">
            Apakah Kamu yakin menambah anggota baru
        </h2>
        <div class="wf-modal-actions">
            <button type="button" class="wf-modal-btn-batal" id="btn-modal-batal">
                Batal
            </button>
            <button type="button" class="wf-modal-btn-konfirmasi" id="btn-modal-konfirmasi">
                Konfirmasi
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnTrigger = document.getElementById('btn-trigger-modal');
    const modal = document.getElementById('modal-konfirmasi-tambah');
    const btnBatal = document.getElementById('btn-modal-batal');
    const btnKonfirmasi = document.getElementById('btn-modal-konfirmasi');
    const checkboxPersetujuan = document.getElementById('persetujuan');
    const form = document.getElementById('form-tambah-anggota');

    // ── Logika Upload & Berkas Terlampir Dinamis ──
    const fileInput = document.getElementById('file-input-tambah');
    const dropzone = document.getElementById('dropzone-tambah');
    const attachedSection = document.getElementById('section-berkas-terlampir');
    const fileListContainer = document.getElementById('tambah-file-list');
    const attachedTitle = document.getElementById('judul-berkas-terlampir');
    let uploadedFiles = [];

    function renderUploadedFiles() {
        if (!attachedSection || !fileListContainer) return;
        if (uploadedFiles.length === 0) {
            attachedSection.style.display = 'none';
            fileListContainer.innerHTML = '';
            return;
        }

        attachedSection.style.display = 'block';
        if (attachedTitle) {
            attachedTitle.textContent = `BERKAS TERLAMPIR (${uploadedFiles.length} FILE TERDETEKSI)`;
        }
        fileListContainer.innerHTML = '';

        uploadedFiles.forEach((file, index) => {
            const isPdf = file.name.toLowerCase().endsWith('.pdf');
            const iconClass = isPdf ? 'bxs-file-pdf wf-file-icon-pdf' : 'bxs-file-image wf-file-icon-img';
            const sizeInMB = file.size / (1024 * 1024);
            const sizeStr = sizeInMB >= 0.1 
                ? sizeInMB.toFixed(1) + ' MB' 
                : Math.max(1, Math.round(file.size / 1024)) + ' KB';

            const item = document.createElement('div');
            item.className = 'wf-file-item';
            item.innerHTML = `
                <div class="wf-file-info">
                    <i class='bx ${iconClass}'></i>
                    <div style="min-width:0;">
                        <div class="wf-file-name" title="${file.name}">${file.name}</div>
                        <div class="wf-file-meta">
                            <span>${sizeStr}</span> • <span>✓ Siap Diunggah</span>
                        </div>
                    </div>
                </div>
                <div class="wf-file-actions">
                    <button type="button" class="wf-file-btn" title="Lihat Berkas" onclick="previewFileTambah(${index})">
                        <i class='bx bx-show'></i>
                    </button>
                    <button type="button" class="wf-file-btn wf-file-btn--delete" title="Hapus Berkas" onclick="hapusFileTambah(${index})">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            `;
            fileListContainer.appendChild(item);
        });
    }

    window.previewFileTambah = function(index) {
        if (uploadedFiles[index]) {
            alert('Pratinjau berkas: ' + uploadedFiles[index].name);
        }
    };

    window.hapusFileTambah = function(index) {
        uploadedFiles.splice(index, 1);
        renderUploadedFiles();
    };

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            files.forEach(f => {
                if (!uploadedFiles.some(existing => existing.name === f.name && existing.size === f.size)) {
                    uploadedFiles.push(f);
                }
            });
            renderUploadedFiles();
        });
    }

    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = '#28a745';
                dropzone.style.backgroundColor = '#f0fff4';
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = '';
                dropzone.style.backgroundColor = '';
            }, false);
        });

        dropzone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = Array.from(dt.files);
            files.forEach(f => {
                if (!uploadedFiles.some(existing => existing.name === f.name && existing.size === f.size)) {
                    uploadedFiles.push(f);
                }
            });
            renderUploadedFiles();
        });
    }

    // ── Logika Modal Konfirmasi ──
    btnTrigger.addEventListener('click', function () {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        if (!checkboxPersetujuan.checked) {
            alert('Silakan centang pernyataan persetujuan terlebih dahulu sebelum melanjutkan.');
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
});
</script>
@endpush
