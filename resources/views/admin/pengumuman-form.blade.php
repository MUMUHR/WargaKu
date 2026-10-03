@extends('layouts.admin')

@section('title', 'Tambah Pengumuman Baru')
@section('meta_description', 'Form tambah pengumuman baru RT 04 / RW 08 — WargaKu')

@section('breadcrumb')
    <a href="{{ route('admin.pengumuman') }}" class="admin-topbar__breadcrumb-link">Kelola Pengumuman</a>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-pengumuman-form.css') }}">
@endpush

@section('content')

{{-- Page Header --}}
<div class="admin-page-header">
    <div>
        <nav style="font-size:12px;color:#6c757d;margin-bottom:4px">
            Home / <a href="{{ route('admin.pengumuman') }}" style="color:#6c757d;text-decoration:none">Kelola Pengumuman</a> / <span style="color:#212529;font-weight:600">Tambah Pengumuman Baru</span>
        </nav>
        <h1 class="admin-page-header__title">Form Tambah Pengumuman RT</h1>
        <p class="admin-page-header__sub">Buat dan publikasikan informasi resmi untuk seluruh warga RT 04 / RW 08.</p>
    </div>
    <a href="{{ route('admin.pengumuman') }}" class="btn-kembali">
        <i class='bx bx-arrow-back'></i> Kembali ke Daftar
    </a>
</div>

{{-- Form Grid --}}
<form id="form-pengumuman" action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="form-pengumuman-grid">

    {{-- ═══ KOLOM KIRI: Konten Utama ═══ --}}
    <div>
        <div class="form-card">
            <div class="form-card__head">
                <h2 class="form-card__title">
                    <i class='bx bx-notepad'></i>
                    Konten & Informasi Pengumuman
                </h2>
                <span class="form-card__badge">RT 04 / RW 08</span>
            </div>
            <div class="form-card__body">

                {{-- Judul --}}
                <div class="form-group">
                    <div class="form-label">
                        <div class="form-label__left">
                            <span>Judul Pengumuman</span>
                            <span style="color:#dc3545">*</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px">
                            <span class="form-label__required">(Wajib diisi)</span>
                            <span class="form-label__counter"><span id="judul-count">58</span> / 150</span>
                        </div>
                    </div>
                    <input type="text" id="judul" name="judul" class="form-input"
                           value="Kerja Bakti Lingkungan Serentak & PSN Menjelang Musim Hujan"
                           maxlength="150" required
                           oninput="document.getElementById('judul-count').textContent=this.value.length">
                    <p class="form-hint">Maksimal 150 karakter. Pastikan judul mencerminkan topik utama kegiatan/edaran warga.</p>
                </div>

                {{-- Isi --}}
                <div class="form-group" style="margin-bottom:0">
                    <div class="form-label">
                        <div class="form-label__left">
                            <span>Isi Pengumuman</span>
                            <span style="color:#dc3545">*</span>
                        </div>
                        <span style="font-size:12px;color:#28a745;font-weight:600;display:flex;align-items:center;gap:4px">
                            <i class='bx bx-edit'></i> Editor Format Teks Lengkap
                        </span>
                    </div>
                    <div class="editor-toolbar">
                        <button type="button" class="editor-toolbar__btn" onclick="formatTeks('bold')" title="Bold"><strong>B</strong></button>
                        <button type="button" class="editor-toolbar__btn" onclick="formatTeks('italic')" title="Italic"><em>I</em></button>
                        <button type="button" class="editor-toolbar__btn" onclick="formatTeks('underline')" title="Underline" style="text-decoration:underline">U</button>
                        <div class="editor-toolbar__sep"></div>
                        <button type="button" class="editor-toolbar__btn" onclick="sisipkanList('bullet')" title="Bullet List">
                            <i class='bx bx-list-ul'></i>
                        </button>
                        <button type="button" class="editor-toolbar__btn" onclick="sisipkanList('number')" title="Numbered List">
                            <i class='bx bx-list-ol'></i>
                        </button>
                        <div class="editor-toolbar__sep"></div>
                        <select class="editor-toolbar__select" onchange="formatHeading(this.value)">
                            <option value="">Gaya Teks...</option>
                            <option value="p">Normal</option>
                            <option value="h2">Judul H2</option>
                            <option value="h3">Subjudul H3</option>
                        </select>
                        <div class="editor-toolbar__sep"></div>
                        <button type="button" class="editor-toolbar__btn" onclick="sisipkanLink()" title="Tautan Link">
                            <i class='bx bx-link'></i>
                        </button>
                        <button type="button" class="editor-toolbar__btn" onclick="hapusFormat()" title="Hapus Format">
                            <i class='bx bx-unlink'></i>
                        </button>
                    </div>
                    <textarea id="isi" name="isi" class="editor-textarea" required
                              oninput="updateEditorMeta()">Kepada Yth. Seluruh Bapak/Ibu Warga RT 04 / RW 08,

Menindaklanjuti imbauan kebersihan lingkungan dan pencegahan genangan musim penghujan, Pengurus RT 04 mengundang kehadiran segenap warga dalam kegiatan Gerakan Kerja Bakti Serentak & Pemberantasan Sarang Nyamuk (PSN) yang akan dilaksanakan pada:

• Hari / Tanggal : Minggu, 18 Mei 2025
• Waktu         : Pukul 06.30 WIB s.d Selesai
• Titik Kumpul  : Balai Warga RT 04 & Lapangan Voli
• Agenda Utama  : Pembersihan selokan air utama, pemangkasan ranting pohon rawan patah, serta pemilahan sampah anorganik.

Diharapkan setiap rumah tangga mengirimkan sekurang-kurangnya 1 orang perwakilan. Ibu-ibu PKK akan mengoordinasikan dapur umum konsumsi sarapan pagi bersama di Balai RT.</textarea>
                    <div class="editor-meta">
                        <span id="editor-kata">128 Kata</span>
                        <span>|</span>
                        <span id="editor-char">859 Karakter</span>
                    </div>
                </div>

                {{-- Panduan Publikasi Box --}}
                <div class="form-info-box">
                    <i class='bx bx-info-circle form-info-box__icon'></i>
                    <div>
                        <div class="form-info-box__title">Panduan Publikasi WargaKu RT</div>
                        <div class="form-info-box__text">Pengumuman yang dipublikasikan akan langsung dikirimkan sebagai notifikasi push ke aplikasi mobile warga dan muncul pada banner prioritas portal RT. Pastikan tanggal dan lokasi sudah terkonfirmasi.</div>
                    </div>
                </div>

            </div>

            <div class="form-card__footer">
                <span class="form-card__footer-meta">
                    <i class='bx bx-time-five'></i>
                    Terakhir diubah: Hari ini, pukul <span id="current-time">10.42 WIB</span>
                </span>
                <div class="form-card__footer-actions">
                    <a href="{{ route('admin.pengumuman') }}" class="btn-cancel">
                        <i class='bx bx-x'></i> Batal & Kembali
                    </a>
                    <button type="submit" name="status" value="draft" class="btn-draft">
                        <i class='bx bx-file-blank'></i> Simpan Draf
                    </button>
                    <button type="submit" name="status" value="publish" class="btn-publish">
                        <i class='bx bx-send'></i> Simpan & Publikasikan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ KOLOM KANAN: Sidebar ═══ --}}
    <div>

        {{-- Gambar Sampul / Thumbnail --}}
        <div class="sidebar-card">
            <div class="sidebar-card__head">
                <h3 class="sidebar-card__title">
                    <i class='bx bx-image'></i>
                    Gambar Sampul / Thumbnail
                </h3>
            </div>
            <div class="sidebar-card__body">
                <div class="thumb-preview__label">
                    <span>Pratinjau Sampul Aktif:</span>
                    <span id="thumb-status-indicator">● Tersedia</span>
                </div>
                
                <div class="thumb-preview-card" id="thumb-preview-card">
                    <span class="thumb-preview__badge">Format: 1920 × 1080 (16:9)</span>
                    <img src="{{ asset('images/pengumuman/kerjabakti.jpg') }}" alt="Pratinjau Sampul"
                         class="thumb-preview__img" id="thumb-preview-img">
                    <div class="thumb-preview__overlay-bar">
                        <span class="thumb-preview__filename" id="thumb-filename">foto_kerja_bakti_rt04.jpg</span>
                        <div class="thumb-preview__actions">
                            <button type="button" class="btn-thumb-change" onclick="document.getElementById('input-gambar').click()">
                                <i class='bx bx-sync'></i> Ganti
                            </button>
                            <button type="button" class="btn-thumb-delete" onclick="hapusThumbnail()" title="Hapus Gambar">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Upload Dropzone --}}
                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('input-gambar').click()">
                    <div class="upload-zone__icon">
                        <i class='bx bx-cloud-upload'></i>
                    </div>
                    <p class="upload-zone__text">
                        Tarik & letakkan foto di sini, atau
                        <a href="#" class="upload-zone__link" onclick="event.preventDefault()">Pilih Berkas</a>
                    </p>
                    <span class="upload-zone__hint">JPG, JPEG, PNG</span>
                    <input type="file" id="input-gambar" name="gambar" accept="image/*"
                           style="display:none" onchange="previewGambar(this)">
                </div>

                <div class="upload-zone__disclaimer">
                    <i class='bx bx-image-alt'></i>
                    <span>Gambar akan ditampilkan di daftar pengumuman portal warga dan aplikasi mobile RT.</span>
                </div>
            </div>
        </div>

        {{-- Status Publikasi --}}
        <div class="sidebar-card">
            <div class="sidebar-card__head">
                <h3 class="sidebar-card__title">
                    <i class='bx bx-slider-alt'></i>
                    Status & Informasi Publikasi
                </h3>
                <span style="background:#28a745;color:#ffffff;font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:3px;">Pengaturan</span>
            </div>
            <div class="sidebar-card__body">
                <p style="font-size:12.5px;font-weight:700;color:#212529;margin:0 0 10px">Status Publikasi *</p>

                <label class="status-option status-option--active" for="status-publish" id="label-status-publish">
                    <input type="radio" id="status-publish" name="status_publikasi"
                           value="publish" checked class="status-option__radio">
                    <div>
                        <p class="status-option__label">
                            <i class='bx bx-check-circle' style="color:#28a745;font-size:16px;"></i>
                            Publish / Terbitkan Langsung
                        </p>
                        <p class="status-option__desc">Pengumuman langsung tayang di portal seluruh warga dan memicu notifikasi seluler.</p>
                    </div>
                </label>

                <label class="status-option" for="status-draft" id="label-status-draft">
                    <input type="radio" id="status-draft" name="status_publikasi"
                           value="draft" class="status-option__radio">
                    <div>
                        <p class="status-option__label">
                            Simpan sebagai Draf
                            <span style="background:#6c757d;color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:3px;margin-left:4px;">Draf</span>
                        </p>
                        <p class="status-option__desc">Hanya dapat dilihat dan disunting oleh pengurus RT. Tidak tampil bagi warga umum.</p>
                    </div>
                </label>

                <hr style="border:none;border-top:1px solid #e9ecef;margin:16px 0">

                <p style="font-size:12.5px;font-weight:700;color:#212529;margin:0 0 10px">Informasi Pembuat <span style="font-weight:400;color:#6c757d;font-size:11.5px">(Sistem)</span></p>
                <div class="pembuat-box">
                    <div class="pembuat-row">
                        <span class="pembuat-row__key">Penulis Akun:</span>
                        <span class="pembuat-row__val">
                            <i class='bx bx-user-circle' style="color:#007bff;font-size:15px"></i>
                            Admin RT
                        </span>
                    </div>
                    <div class="pembuat-row" style="margin-top:2px;">
                        <span class="pembuat-row__key">Waktu Pembuatan:</span>
                        <span class="pembuat-row__val">Hari ini (Otomatis)</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
    // Waktu sekarang
    var now = new Date();
    var jam  = String(now.getHours()).padStart(2,'0');
    var menit = String(now.getMinutes()).padStart(2,'0');
    var el = document.getElementById('current-time');
    if (el) el.textContent = jam + '.' + menit + ' WIB';

    // Update word/char count textarea editor
    function updateEditorMeta() {
        var isi = document.getElementById('isi').value;
        var kata = isi.trim() === '' ? 0 : isi.trim().split(/\s+/).length;
        var char = isi.length;
        document.getElementById('editor-kata').textContent = kata + ' Kata';
        document.getElementById('editor-char').textContent = char + ' Karakter';
    }

    // Inisialisasi hitung kata & judul
    updateEditorMeta();
    var judulInput = document.getElementById('judul');
    if (judulInput) {
        document.getElementById('judul-count').textContent = judulInput.value.length;
    }

    // Preview gambar saat dipilih
    function previewGambar(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById('thumb-preview-img');
            var card = document.getElementById('thumb-preview-card');
            var fn = document.getElementById('thumb-filename');
            var indicator = document.getElementById('thumb-status-indicator');

            if (img) img.src = e.target.result;
            if (card) card.style.display = 'block';
            if (fn) fn.textContent = file.name;
            if (indicator) {
                indicator.textContent = '● Tersedia';
                indicator.style.color = '#28a745';
            }
        };
        reader.readAsDataURL(file);
    }

    // Hapus thumbnail
    function hapusThumbnail() {
        var card = document.getElementById('thumb-preview-card');
        var input = document.getElementById('input-gambar');
        var indicator = document.getElementById('thumb-status-indicator');
        if (input) input.value = '';
        if (card) card.style.display = 'none';
        if (indicator) {
            indicator.textContent = '○ Kosong';
            indicator.style.color = '#6c757d';
        }
    }

    // Drag and drop upload zone
    var zone = document.getElementById('upload-zone');
    if (zone) {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            zone.style.borderColor = '#80bdff';
            zone.style.background = '#f0f7ff';
        });
        zone.addEventListener('dragleave', function() {
            zone.style.borderColor = '';
            zone.style.background = '';
        });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            zone.style.borderColor = '';
            zone.style.background = '';
            var files = e.dataTransfer.files;
            if (files.length) {
                var inp = document.getElementById('input-gambar');
                inp.files = files;
                previewGambar(inp);
            }
        });
    }

    // Radio status highlight
    document.querySelectorAll('input[name="status_publikasi"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.status-option').forEach(function(el) {
                el.classList.remove('status-option--active');
            });
            this.closest('.status-option').classList.add('status-option--active');
        });
    });

    // Helper text formatting
    function formatTeks(command) {
        var textarea = document.getElementById('isi');
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;
        var text = textarea.value;
        var selected = text.substring(start, end);
        var replacement = '';

        if (command === 'bold') replacement = '**' + (selected || 'teks tebal') + '**';
        else if (command === 'italic') replacement = '*' + (selected || 'teks miring') + '*';
        else if (command === 'underline') replacement = '<u>' + (selected || 'teks garis bawah') + '</u>';

        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        textarea.focus();
        updateEditorMeta();
    }

    function sisipkanList(type) {
        var textarea = document.getElementById('isi');
        var start = textarea.selectionStart;
        var text = textarea.value;
        var prefix = type === 'bullet' ? '\n• ' : '\n1. ';
        textarea.value = text.substring(0, start) + prefix + text.substring(start);
        textarea.focus();
        updateEditorMeta();
    }

    function sisipkanLink() {
        var url = prompt('Masukkan tautan URL:', 'https://');
        if (!url) return;
        var textarea = document.getElementById('isi');
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;
        var text = textarea.value;
        var selected = text.substring(start, end) || 'Teks Tautan';
        var replacement = '[' + selected + '](' + url + ')';
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        textarea.focus();
        updateEditorMeta();
    }

    function hapusFormat() {
        var textarea = document.getElementById('isi');
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;
        var text = textarea.value;
        var selected = text.substring(start, end);
        var clean = selected.replace(/[*_~`#<>]/g, '');
        textarea.value = text.substring(0, start) + clean + text.substring(end);
        textarea.focus();
        updateEditorMeta();
    }

    function formatHeading(val) {
        if (!val) return;
        var textarea = document.getElementById('isi');
        var start = textarea.selectionStart;
        var text = textarea.value;
        var prefix = val === 'h2' ? '\n## ' : (val === 'h3' ? '\n### ' : '\n');
        textarea.value = text.substring(0, start) + prefix + text.substring(start);
        textarea.focus();
        updateEditorMeta();
    }
</script>
@endpush