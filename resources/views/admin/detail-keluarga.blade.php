@extends('layouts.admin')

@section('title', 'Detail Kartu Keluarga: ' . $keluarga->no_kk)
@section('meta_description', 'Detail informasi susunan anggota keluarga, profil hunian, dan data kependudukan warga RT 04.')

@section('breadcrumb')
    <a href="{{ route('admin.data-warga') }}" style="color:inherit;text-decoration:none;">Kelola Data Warga</a>
    <span style="margin: 0 4px; color: #adb5bd;">/</span>
    <span class="admin-topbar__breadcrumb-current">Detail Kartu Keluarga</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-warga.css') }}">
    <style>
        .btn-act-ubah-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            color: #2b8a3e;
            background: #ebfbee;
            border: 1px solid #b2f2bb;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-act-ubah-status:hover {
            background: #d3f9d8;
            color: #237032;
        }
    </style>
@endpush

@section('content')

{{-- Alert Notifikasi --}}
@if(session('success'))
    <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px 18px;border-radius:8px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:center;gap:10px;">
            <i class='bx bx-check-circle' style="font-size:20px;color:#28a745;"></i>
            <span style="font-size:13.5px;font-weight:500;">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#155724;line-height:1;">&times;</button>
    </div>
@endif

@if(session('error'))
    <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px 18px;border-radius:8px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:center;gap:10px;">
            <i class='bx bx-error-circle' style="font-size:20px;color:#dc3545;"></i>
            <span style="font-size:13.5px;font-weight:500;">{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#721c24;line-height:1;">&times;</button>
    </div>
@endif

{{-- ========================================================
     HEADER HALAMAN DETAIL KARTU KELUARGA
     ======================================================== --}}
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-header__title" style="margin:0;">
            Detail Kartu Keluarga (KK): <span style="color:#007bff;font-weight:700">{{ $keluarga->no_kk }}</span>
        </h1>
    </div>
    <div id="header-actions">
        <a href="{{ route('admin.data-warga') }}" class="btn-kembali-kk" style="text-decoration:none;">
            <i class='bx bx-arrow-back'></i> Kembali ke Daftar KK
        </a>
    </div>
</div>

{{-- ========================================================
     TOP STAT CARDS: VIEW DETAIL KK (3 BOXES)
     ======================================================== --}}
<div class="warga-stats-grid" id="stats-detail" style="margin-bottom: 22px;">
    <div class="warga-stat-box warga-stat-box--blue">
        <div class="warga-stat-box__label">NOMOR KARTU KELUARGA</div>
        <div class="warga-stat-box__val" style="font-size:22px;margin-top:6px;">{{ $keluarga->no_kk }}</div>
        <i class='bx bx-id-card warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--green">
        <div class="warga-stat-box__label">TOTAL JIWA TERDAFTAR</div>
        <div class="warga-stat-box__val" style="font-size:24px;margin-top:4px;">
            {{ $totalJiwaAktif }} <small>Jiwa Aktif</small>
        </div>
        <div class="warga-stat-box__sub">
            {{ $totalLakiKk }} Laki-laki • {{ $totalPerempuanKk }} Perempuan
        </div>
        <i class='bx bx-group warga-stat-box__icon'></i>
    </div>
    <div class="warga-stat-box warga-stat-box--cyan">
        <div class="warga-stat-box__label">STATUS HUNIAN &amp; EKONOMI</div>
        <div class="warga-stat-box__val" style="font-size:22px;margin-top:6px;">
            @php
                $mapTinggal = [
                    'tetap' => 'Tetap (Milik Sendiri)',
                    'kontrak' => 'Kontrak / Sewa',
                    'kos' => 'Kos',
                    'menumpang' => 'Menumpang',
                ];
            @endphp
            {{ $mapTinggal[$keluarga->status_tinggal] ?? ucfirst($keluarga->status_tinggal) }}
        </div>
        <div class="warga-stat-box__sub" style="font-size:12px;font-weight:600;color:#007bff;margin-top:4px;">
            Status Ekonomi: {{ $keluarga->status_ekonomi === 'kurang_mampu' ? 'Kurang Mampu' : 'Mampu' }}
        </div>
        <i class='bx bx-building-house warga-stat-box__icon'></i>
    </div>
</div>

{{-- ========================================================
     CONTAINER VIEW DETAIL KK
     ======================================================== --}}
<div class="detail-kk-wrapper" style="display:flex;flex-direction:column;gap:20px;">

    {{-- Card 1: Profil Keluarga & Domisili Rumah --}}
    <div class="detail-section-card">
        <div class="detail-section-head" onclick="toggleCollapse('detail-profil-content', 'chevron-profil')">
            <h3 class="detail-section-title">
                <i class='bx bx-home'></i>
                Profil Keluarga &amp; Domisili Rumah
            </h3>
            <div style="display:flex;align-items:center;gap:12px;">
                <button type="button" class="btn-act-ubah-status" onclick="event.stopPropagation(); bukaModalEditStatus();">
                    <i class='bx bx-edit'></i> Ubah Status Hunian
                </button>
                <i class='bx bx-chevron-up' id="chevron-profil" style="font-size:20px;color:#6c757d;"></i>
            </div>
        </div>
        <div id="detail-profil-content">
            <div class="detail-profil-grid">
                <div>
                    <div class="detail-profil-item__label">KEPALA KELUARGA</div>
                    <div class="detail-profil-item__value" id="detail-kepala-nama">
                        {{ $kepalaKeluarga->nama ?? 'Belum ditentukan' }}
                    </div>
                    <div class="detail-profil-item__sub">
                        NIK : <span id="detail-kepala-nik">{{ $kepalaKeluarga->nik ?? '-' }}</span>
                    </div>
                </div>
                <div>
                    <div class="detail-profil-item__label">ALAMAT RUMAH LENGKAP</div>
                    <div class="detail-profil-item__value" id="detail-alamat">
                        {{ $alamatKeluarga }}
                    </div>
                </div>
                <div>
                    <div class="detail-profil-item__label">KONTAK TELEPON / WHATSAPP</div>
                    <div class="detail-profil-item__value" style="display:flex;align-items:center;gap:6px;">
                        <i class='bx bxl-whatsapp' style="color:#25d366;font-size:16px;"></i>
                        <span id="detail-kontak" style="color:#007bff;font-weight:700;">{{ $noTelpKeluarga }}</span>
                    </div>
                    @if($akunKk)
                        <div class="detail-profil-item__sub" style="margin-top:2px;">
                            Akun Login: <strong>{{ $akunKk->username }}</strong>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="detail-profil-item__label">STATUS EKONOMI &amp; TINGGAL</div>
                    <div style="margin-top:4px;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                        <span class="badge-ekonomi-detail" id="detail-ekonomi">
                            Ekonomi: {{ $keluarga->status_ekonomi === 'kurang_mampu' ? 'Kurang Mampu' : 'Mampu' }}
                        </span>
                        <span class="{{ $keluarga->status_tinggal === 'tetap' ? 'badge-status-tetap' : 'badge-status-sewa' }}" style="padding: 4px 10px; font-size: 11.5px; border-radius: 4px; font-weight: 600;">
                            {{ $mapTinggal[$keluarga->status_tinggal] ?? ucfirst($keluarga->status_tinggal) }}
                        </span>
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
                <span class="badge-tab-kk" id="detail-badge-count">{{ $totalJiwaAktif }} Orang</span>
            </h3>
            <div class="detail-anggota-actions">
                <button type="button" class="btn-reset-password" onclick="konfirmasiResetPassword('{{ addslashes($kepalaKeluarga->nama ?? 'Kepala Keluarga') }}')">
                    <i class='bx bx-shield-quarter'></i> Reset Password Akun
                </button>
                <a href="{{ route('admin.data-warga.tambah-anggota') }}?no_kk={{ $keluarga->no_kk }}" class="btn-tambah-anggota" style="text-decoration:none;">
                    <i class='bx bx-user-plus'></i> Tambah Anggota
                </a>
            </div>
        </div>

        <div class="warga-table-wrap">
            <table class="warga-table" id="table-anggota">
                <colgroup>
                    <col style="width: 45px;">
                    <col style="width: 165px;">
                    <col style="width: 180px;">
                    <col style="width: 130px;">
                    <col style="width: 125px;">
                    <col style="width: 185px;">
                    <col style="width: 115px;">
                    <col style="width: 255px;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">NO</th>
                        <th>NIK</th>
                        <th>NAMA LENGKAP</th>
                        <th>HUBUNGAN KK</th>
                        <th>JK &amp; AGAMA</th>
                        <th>PENDIDIKAN TERAKHIR &amp; PEKERJAAN</th>
                        <th>STATUS WARGA</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody id="detail-anggota-tbody">
                    @forelse($anggotaHidup as $index => $m)
                    @php
                        $badgeClass = match($m->status_hubungan) {
                            'kepala_keluarga' => 'badge-hub-kepala',
                            'istri' => 'badge-hub-istri',
                            default => 'badge-hub-anak'
                        };

                        $hubunganLabel = match($m->status_hubungan) {
                            'kepala_keluarga' => 'Kepala Keluarga',
                            'suami' => 'Suami',
                            'istri' => 'Istri',
                            'anak' => 'Anak Kandung',
                            'menantu' => 'Menantu',
                            'cucu' => 'Cucu',
                            'orang_tua' => 'Orang Tua',
                            'mertua' => 'Mertua',
                            'famili_lain' => 'Famili Lain',
                            default => ucfirst(str_replace('_', ' ', $m->status_hubungan))
                        };

                        $pendidikanLabel = match($m->pendidikan_terakhir) {
                            'tidak_belum_sekolah' => 'Tidak/Belum Sekolah',
                            'belum_tamat_sd' => 'Belum Tamat SD',
                            'sd_sederajat' => 'SD / Sederajat',
                            'smp_sederajat' => 'SMP / Sederajat',
                            'sma_sederajat' => 'SMA / SMK Sederajat',
                            'd1_d2' => 'Diploma 1/2',
                            'd3' => 'Diploma 3',
                            'd4_s1' => 'Sarjana (S1/D4)',
                            's2' => 'Magister (S2)',
                            's3' => 'Doktor (S3)',
                            default => $m->pendidikan_terakhir ? strtoupper(str_replace('_', ' ', $m->pendidikan_terakhir)) : '-'
                        };

                        $tglLahirStr = $m->tanggal_lahir ? $m->tanggal_lahir->isoFormat('D MMMM Y') : '-';
                    @endphp
                    <tr>
                        <td style="text-align: center; color: #495057; font-weight: 500;">{{ $index + 1 }}</td>
                        <td>
                            <span style="font-weight: 700; color: #007bff; font-size: 13px; letter-spacing: 0.02em;">
                                {{ $m->nik }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #212529; font-size: 13px;">{{ $m->nama }}</div>
                        </td>
                        <td>
                            <span class="{{ $badgeClass }}">{{ $hubunganLabel }}</span>
                        </td>
                        <td>
                            <div style="color: #212529; font-weight: 600; font-size: 13px;">
                                {{ $m->jenis_kelamin === 'laki_laki' ? 'Laki-laki' : 'Perempuan' }}
                            </div>
                            <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">
                                {{ ucfirst($m->agama ?? '-') }}
                            </div>
                        </td>
                        <td>
                            <div style="color: #212529; font-weight: 600; font-size: 13px;">{{ $pendidikanLabel }}</div>
                            <div style="font-size: 11.5px; color: #6c757d; margin-top: 2px;">{{ $m->pekerjaan ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge-warga-hidup">{{ ucfirst($m->status_warga) }} (Aktif)</span>
                        </td>
                        <td>
                            <div class="warga-row-actions">
                                <button type="button" class="btn-act-detail-blue"
                                        onclick="bukaModalDetailWarga({
                                            nik: '{{ $m->nik }}',
                                            nama: '{{ addslashes($m->nama) }}',
                                            tempat_lahir: '{{ addslashes($m->tempat_lahir ?? '-') }}',
                                            tanggal_lahir: '{{ $tglLahirStr }}',
                                            jk: '{{ $m->jenis_kelamin === 'laki_laki' ? 'Laki-laki' : 'Perempuan' }}',
                                            agama: '{{ ucfirst($m->agama ?? '-') }}',
                                            no_kk: '{{ $keluarga->no_kk }}',
                                            hubungan: '{{ $hubunganLabel }}',
                                            pendidikan: '{{ addslashes($pendidikanLabel) }}',
                                            pekerjaan: '{{ addslashes($m->pekerjaan ?? '-') }}',
                                            status_warga: '{{ ucfirst($m->status_warga) }} (Aktif)',
                                            alamat: '{{ addslashes($m->alamat ?? $alamatKeluarga) }}'
                                        })" title="Lihat Detail Profil Warga">
                                    <i class='bx bx-show'></i> Detail
                                </button>
                                <a href="{{ route('admin.data-warga.edit-anggota', $m->nik) }}" class="btn-act-edit-green" title="Edit Data Warga" style="text-decoration:none;">
                                    <i class='bx bx-edit'></i> Edit
                                </a>
                                <button type="button" class="btn-act-delete" onclick="konfirmasiHapusAnggota('{{ $m->nik }}', '{{ addslashes($m->nama) }}')" title="Hapus Warga dari KK">
                                    <i class='bx bx-trash'></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 32px; color: #6c757d;">
                            Belum ada anggota keluarga aktif yang terdaftar pada Kartu Keluarga ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="detail-footer-sync">
            <div class="detail-footer-sync__left">
                <i class='bx bx-info-circle'></i>
                <span>Semua data anggota keluarga di atas telah disinkronkan dengan basis data RT 04.</span>
            </div>
            <div>
                Terakhir diupdate: {{ $terakhirDiupdate ? \Carbon\Carbon::parse($terakhirDiupdate)->isoFormat('D MMMM Y • HH:mm') . ' WIB' : '-' }}
            </div>
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
                <h3 class="detail-warga-banner-name" id="modal-dw-nama">-</h3>
                <span class="badge-warga-hidup" id="modal-dw-status">● Hidup (Aktif)</span>
            </div>

            <div class="detail-warga-grid">
                <div class="detail-warga-subcard">
                    <h5 class="detail-warga-subcard-title">IDENTITAS UTAMA &amp; BIODATA</h5>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">NIK:</span>
                        <span class="detail-warga-v" id="modal-dw-nik" style="font-weight:700;">-</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Nama Lengkap:</span>
                        <span class="detail-warga-v" id="modal-dw-namalengkap">-</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Tempat, Tanggal Lahir:</span>
                        <span class="detail-warga-v" id="modal-dw-ttl">-</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Jenis Kelamin:</span>
                        <span class="detail-warga-v" id="modal-dw-jk">-</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Agama:</span>
                        <span class="detail-warga-v" id="modal-dw-agama">-</span>
                    </div>
                </div>

                <div class="detail-warga-subcard">
                    <h5 class="detail-warga-subcard-title">HUBUNGAN &amp; SOSIAL</h5>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">No. Kartu Keluarga:</span>
                        <span class="detail-warga-v" id="modal-dw-nokk" style="font-weight:700;">{{ $keluarga->no_kk }}</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Hubungan Keluarga:</span>
                        <span class="detail-warga-v" id="modal-dw-hubungan">-</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Pendidikan Terakhir:</span>
                        <span class="detail-warga-v" id="modal-dw-pendidikan">-</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Pekerjaan:</span>
                        <span class="detail-warga-v" id="modal-dw-pekerjaan">-</span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Status Warga:</span>
                        <span class="detail-warga-v">
                            <span class="badge-warga-hidup" id="modal-dw-status-badge">Hidup (Aktif)</span>
                        </span>
                    </div>
                    <div class="detail-warga-kv">
                        <span class="detail-warga-k">Alamat Lengkap:</span>
                        <span class="detail-warga-v" id="modal-dw-alamat">{{ $alamatKeluarga }}</span>
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
     MODAL POPUP UBAH STATUS KELUARGA
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-edit-status-kk" onclick="if(event.target === this) tutupModalEditStatus()">
    <div class="warga-modal-dialog">
        <form method="POST" action="{{ route('admin.data-warga.status', $keluarga->no_kk) }}">
            @csrf
            <div class="warga-modal-header warga-modal-header--green">
                <h4 class="warga-modal-title">Ubah Status Keluarga</h4>
                <button type="button" class="warga-modal-close" onclick="tutupModalEditStatus()">&times;</button>
            </div>
            <div class="warga-modal-body">
                <div class="edit-status-target-card">
                    <span class="edit-status-target-label">KK Tujuan:</span>
                    <span class="edit-status-target-val">{{ $keluarga->no_kk }}</span>
                </div>

                <div class="edit-status-grid">
                    <div class="edit-status-field">
                        <label class="edit-status-label" for="edit-input-tinggal">Status Tinggal</label>
                        <select class="edit-status-select" id="edit-input-tinggal" name="status_tinggal">
                            <option value="tetap" {{ $keluarga->status_tinggal === 'tetap' ? 'selected' : '' }}>Tetap</option>
                            <option value="kontrak" {{ $keluarga->status_tinggal === 'kontrak' ? 'selected' : '' }}>Kontrak / Sewa</option>
                            <option value="kos" {{ $keluarga->status_tinggal === 'kos' ? 'selected' : '' }}>Kos</option>
                            <option value="menumpang" {{ $keluarga->status_tinggal === 'menumpang' ? 'selected' : '' }}>Menumpang</option>
                        </select>
                    </div>
                    <div class="edit-status-field">
                        <label class="edit-status-label" for="edit-input-ekonomi">Status Ekonomi</label>
                        <select class="edit-status-select" id="edit-input-ekonomi" name="status_ekonomi">
                            <option value="mampu" {{ $keluarga->status_ekonomi === 'mampu' ? 'selected' : '' }}>Mampu</option>
                            <option value="kurang_mampu" {{ $keluarga->status_ekonomi === 'kurang_mampu' ? 'selected' : '' }}>Kurang Mampu</option>
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
     MODAL POPUP KONFIRMASI RESET PASSWORD AKUN
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-reset-password-warga" onclick="if(event.target === this) tutupModalReset()">
    <div class="warga-modal-dialog">
        <form method="POST" action="{{ route('admin.data-warga.reset-password', $keluarga->no_kk) }}">
            @csrf
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
                        <strong>Perhatian:</strong> Kata sandi akun akan diatur ulang ke username default (NIK). Warga dapat login kembali di aplikasi WargaKu menggunakan NIK.
                    </div>
                </div>
            </div>
            <div class="warga-modal-footer">
                <button type="button" class="btn-warga-modal-batal" onclick="tutupModalReset()">Batal</button>
                <button type="submit" class="btn-warga-modal-action-green">
                    <i class='bx bx-check-circle'></i> Ya, Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL POPUP KONFIRMASI HAPUS ANGGOTA WARGA
     ======================================================== --}}
<div class="warga-modal-overlay" id="modal-hapus-warga" onclick="if(event.target === this) tutupModalHapus()">
    <div class="warga-modal-dialog">
        <form id="form-hapus-anggota" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="warga-modal-header warga-modal-header--red">
                <div class="warga-modal-header-left">
                    <div class="warga-modal-icon-circle">
                        <i class='bx bx-trash'></i>
                    </div>
                    <h4 class="warga-modal-title">Konfirmasi Hapus Data Warga</h4>
                </div>
                <button type="button" class="warga-modal-close" onclick="tutupModalHapus()">&times;</button>
            </div>
            <div class="warga-modal-body">
                <p class="warga-modal-question" id="modal-hapus-question">
                    Apakah Anda yakin ingin menghapus data warga ini dari Kartu Keluarga?
                </p>
                <div class="warga-modal-alert-box warga-modal-alert-box--danger">
                    <i class='bx bx-info-circle warga-modal-alert-icon'></i>
                    <div class="warga-modal-alert-text">
                        <strong>Perhatian:</strong> Data kependudukan anggota keluarga ini akan dihapus dari Kartu Keluarga ini. Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
            </div>
            <div class="warga-modal-footer">
                <button type="button" class="btn-warga-modal-batal" onclick="tutupModalHapus()">Batal</button>
                <button type="submit" class="btn-warga-modal-action-red">
                    <i class='bx bx-trash'></i> Ya, Hapus Data
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Collapse toggle profil rumah
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

    // Modal Detail Biodata Warga
    var activeDetailNik = '';

    function bukaModalDetailWarga(w) {
        if (!w) return;
        activeDetailNik = w.nik;

        document.getElementById('modal-dw-nama').textContent = w.nama;
        document.getElementById('modal-dw-nik').textContent = w.nik;
        document.getElementById('modal-dw-namalengkap').textContent = w.nama;
        document.getElementById('modal-dw-ttl').textContent = (w.tempat_lahir || '-') + ', ' + (w.tanggal_lahir || '-');
        document.getElementById('modal-dw-jk').textContent = w.jk;
        document.getElementById('modal-dw-agama').textContent = w.agama;
        document.getElementById('modal-dw-nokk').textContent = w.no_kk;
        document.getElementById('modal-dw-hubungan').textContent = w.hubungan;
        document.getElementById('modal-dw-pendidikan').textContent = w.pendidikan;
        document.getElementById('modal-dw-pekerjaan').textContent = w.pekerjaan;
        document.getElementById('modal-dw-status').innerHTML = '● ' + w.status_warga;
        document.getElementById('modal-dw-status-badge').textContent = w.status_warga;
        document.getElementById('modal-dw-alamat').textContent = w.alamat;

        document.getElementById('modal-detail-warga').style.display = 'flex';
    }

    function tutupModalDetailWarga() {
        document.getElementById('modal-detail-warga').style.display = 'none';
        activeDetailNik = '';
    }

    function editDariModalDetail() {
        if (activeDetailNik) {
            window.location.href = "{{ url('/admin/data-warga/edit-anggota') }}/" + activeDetailNik;
        }
    }

    // Modal Ubah Status Keluarga
    function bukaModalEditStatus() {
        document.getElementById('modal-edit-status-kk').style.display = 'flex';
    }

    function tutupModalEditStatus() {
        document.getElementById('modal-edit-status-kk').style.display = 'none';
    }

    // Modal Reset Password
    function konfirmasiResetPassword(nama) {
        document.getElementById('modal-reset-question').innerHTML = 'Apakah Anda yakin ingin me-reset kata sandi akun kepala keluarga / warga <strong>' + nama + '</strong> ini?';
        document.getElementById('modal-reset-password-warga').style.display = 'flex';
    }

    function tutupModalReset() {
        document.getElementById('modal-reset-password-warga').style.display = 'none';
    }

    // Modal Hapus Anggota
    function konfirmasiHapusAnggota(nik, nama) {
        var form = document.getElementById('form-hapus-anggota');
        form.action = "{{ url('/admin/data-warga/anggota') }}/" + nik;

        document.getElementById('modal-hapus-question').innerHTML = 'Apakah Anda yakin ingin menghapus anggota keluarga <strong>' + nama + '</strong> (NIK: ' + nik + ') dari Kartu Keluarga ini?';
        document.getElementById('modal-hapus-warga').style.display = 'flex';
    }

    function tutupModalHapus() {
        document.getElementById('modal-hapus-warga').style.display = 'none';
    }

    // Keyboard ESC listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalDetailWarga();
            tutupModalEditStatus();
            tutupModalReset();
            tutupModalHapus();
        }
    });
</script>
@endpush
