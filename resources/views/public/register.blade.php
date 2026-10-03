<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pendaftaran Akun Warga Baru RT 04 / RW 08 - WargaKu.">
    <title>Daftar Akun Warga Baru — WargaKu</title>

    {{-- CSS Vanilla terpisah --}}
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">

    {{-- Alpine.js dari Vite --}}
    @vite('resources/js/app.js')
</head>
<body>

<div class="register-wrapper" x-data="{ 
    submitted: false,
    agreed: false,
    handleSubmit(e) {
        if (!this.agreed) {
            alert('Silakan centang pernyataan kebenaran data kependudukan terlebih dahulu.');
            return;
        }
        this.submitted = true;
    }
}">

    {{-- ═══════════════════════════════════════════
         KOLOM KIRI: HERO BANNER (STICKY)
         ═══════════════════════════════════════════ --}}
    <aside class="register-hero" aria-label="Brand Banner WargaKu">
        <div class="register-hero__bg">
            <img src="{{ asset('bg_hero.png') }}" alt="Pemandangan Asri WargaKu" class="register-hero__bg-img">
        </div>
        <div class="register-hero__overlay"></div>

        {{-- Brand Logo --}}
        <a href="{{ route('home') }}" class="register-hero__brand" title="Kembali ke Beranda">
            <img src="{{ asset('Logo_WargaKu.png') }}" alt="Logo WargaKu" class="register-hero__logo">
            <span class="register-hero__brand-name">WargaKu</span>
        </a>
    </aside>

    {{-- ═══════════════════════════════════════════
         KOLOM KANAN: FORM REGISTRASI
         ═══════════════════════════════════════════ --}}
    <main class="register-main" role="main">
        <div class="register-card">

            {{-- Badge Portal Registrasi Warga --}}
            <div class="register-badge">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
                <span>Portal Registrasi Warga</span>
            </div>

            {{-- Header Judul & Deskripsi --}}
            <header class="register-header">
                <h1 class="register-title">Daftar Akun Warga Baru</h1>
                <p class="register-subtitle">Lengkapi data Kartu Keluarga & identitas Kepala Keluarga / Pendaftar Utama</p>
            </header>

            {{-- Form Registrasi --}}
            <form action="{{ route('register.post') }}" method="POST" @submit.prevent="handleSubmit($event)">
                @csrf

                {{-- ═══ SECTION 1: DATA KARTU KELUARGA ═══ --}}
                <div class="form-section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                    <span>Data Kartu Keluarga</span>
                </div>

                {{-- Baris 1: Nomor KK & Status Tempat Tinggal --}}
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="no_kk" class="form-label">Nomor Kartu Keluarga (KK)<span class="req">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                                    <line x1="7" y1="8" x2="17" y2="8"/>
                                    <line x1="7" y1="12" x2="13" y2="12"/>
                                </svg>
                            </span>
                            <input type="text"
                                   name="no_kk"
                                   id="no_kk"
                                   class="form-control"
                                   placeholder="3271048809920001"
                                   maxlength="16"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status_tempat_tinggal" class="form-label">Status Tempat Tinggal<span class="req">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </span>
                            <select name="status_tempat_tinggal" id="status_tempat_tinggal" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Status Tempat Tinggal --</option>
                                <option value="tetap">Tetap</option>
                                <option value="kontrak">Kontrak / Sewa</option>
                                <option value="kost">Kost</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Baris 2: Nomor WhatsApp / HP Aktif --}}
                <div class="form-row-1">
                    <div class="form-group">
                        <label for="no_hp" class="form-label">Nomor WhatsApp / HP Aktif<span class="req">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                            </span>
                            <input type="tel"
                                   name="no_hp"
                                   id="no_hp"
                                   class="form-control"
                                   placeholder="08xxxxxxxxxx"
                                   required>
                        </div>
                    </div>
                </div>

                {{-- ═══ SECTION 2: BIODATA PENDAFTAR UTAMA ═══ --}}
                <div class="form-section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Biodata Pendaftar Utama</span>
                </div>

                {{-- Baris 3: NIK & Nama Lengkap Sesuai KTP --}}
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="nik" class="form-label">NIK Pendaftar Utama<span class="req">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                                    <line x1="7" y1="8" x2="17" y2="8"/>
                                    <line x1="7" y1="12" x2="13" y2="12"/>
                                </svg>
                            </span>
                            <input type="text"
                                   name="nik"
                                   id="nik"
                                   class="form-control"
                                   placeholder="3271041508800001"
                                   maxlength="16"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Lengkap Sesuai KTP<span class="req">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   placeholder="Nama lengkap sesuai KTP"
                                   required>
                        </div>
                    </div>
                </div>

                {{-- Baris 4: Tempat Lahir & Tanggal Lahir --}}
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="4" y="2" width="16" height="20" rx="2"/>
                                    <line x1="9" y1="22" x2="9" y2="22"/>
                                    <line x1="8" y1="6" x2="8.01" y2="6"/>
                                    <line x1="16" y1="6" x2="16.01" y2="6"/>
                                    <line x1="8" y1="10" x2="8.01" y2="10"/>
                                    <line x1="16" y1="10" x2="16.01" y2="10"/>
                                    <line x1="8" y1="14" x2="8.01" y2="14"/>
                                    <line x1="16" y1="14" x2="16.01" y2="14"/>
                                </svg>
                            </span>
                            <input type="text"
                                   name="tempat_lahir"
                                   id="tempat_lahir"
                                   class="form-control"
                                   placeholder="Kota / tempat lahir">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date"
                               name="tanggal_lahir"
                               id="tanggal_lahir"
                               class="form-control form-control--no-icon">
                    </div>
                </div>

                {{-- Baris 5: Jenis Kelamin & Agama --}}
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin<span class="req">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control form-control--no-icon" required>
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="agama" class="form-label">Agama</label>
                        <select name="agama" id="agama" class="form-control form-control--no-icon">
                            <option value="Islam" selected>Islam</option>
                            <option value="Kristen">Kristen Protestan</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                </div>

                {{-- Baris 6: Pendidikan Terakhir & Pekerjaan --}}
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                        <select name="pendidikan" id="pendidikan" class="form-control form-control--no-icon">
                            <option value="SMA/SMK" selected>SMA/SMK/Sederajat</option>
                            <option value="SD">SD/Sederajat</option>
                            <option value="SMP">SMP/Sederajat</option>
                            <option value="D3">Diploma (D3)</option>
                            <option value="S1">Sarjana (D4/S1)</option>
                            <option value="S2/S3">Magister/Doktor (S2/S3)</option>
                            <option value="Tidak/Belum Sekolah">Tidak/Belum Sekolah</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pekerjaan" class="form-label">Pekerjaan</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                            </span>
                            <input type="text"
                                   name="pekerjaan"
                                   id="pekerjaan"
                                   class="form-control"
                                   placeholder="Karyawan Swasta, Wiraswasta, PNS">
                        </div>
                    </div>
                </div>

                {{-- Baris 7: Alamat Rumah / Blok RT 04 --}}
                <div class="form-row-1">
                    <div class="form-group">
                        <label for="alamat" class="form-label">Alamat Rumah / Blok RT 04<span class="req">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon-left" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </span>
                            <input type="text"
                                   name="alamat"
                                   id="alamat"
                                   class="form-control"
                                   placeholder="Jl. Melati Blok C No. 12, RT 04 / RW 08"
                                   required>
                        </div>
                    </div>
                </div>

                {{-- Pernyataan Persetujuan --}}
                <div class="register-statement" @click="agreed = !agreed">
                    <input type="checkbox" id="agreed" x-model="agreed" @click.stop>
                    <label for="agreed">Saya menyatakan data kependudukan yang diisi adalah benar dan sah.</label>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" class="btn--register-submit">
                    <span>Daftar Akun Warga Baru</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            {{-- Footer Tautan Masuk & Keamanan --}}
            <footer class="register-footer">
                <p class="register-login-link">
                    Sudah punya akun warga?
                    <a href="{{ route('login') }}">Masuk di sini &rarr;</a>
                </p>
                <div class="register-security">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Dilindungi enkripsi data kependudukan RT 04 / RW 08</span>
                </div>
            </footer>

        </div>
    </main>

    {{-- ═══════════════════════════════════════════
         MODAL SUKSES REGISTRASI (PROTOTIPE FRONTEND)
         ═══════════════════════════════════════════ --}}
    <div class="modal-overlay" x-show="submitted" style="display: none;" x-transition>
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h2 class="modal-title">Pendaftaran Berhasil Diajukan!</h2>
            <p class="modal-desc">
                Data Kartu Keluarga & Pendaftar Utama Anda telah kami terima dengan status <strong>Menunggu Verifikasi Admin RT</strong>.<br><br>
                Setelah diverifikasi oleh pengurus RT 04, akun Anda akan aktif dan dapat digunakan untuk masuk aplikasi.
            </p>
            <a href="{{ route('login') }}" class="btn--register-submit" style="text-decoration:none;">
                <span>Lanjut ke Halaman Login</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>
    </div>

</div>

</body>
</html>
