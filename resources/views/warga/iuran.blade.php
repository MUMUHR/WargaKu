@extends('layouts.warga')

@section('title', 'Monitoring Iuran Warga RT 04')
@section('meta_description', 'Sistem monitoring status pembayaran iuran warga per bulan resmi tercatat oleh Bendahara RT 04 / RW 08.')

@section('topbar_section')
    <span class="admin-topbar__breadcrumb-link">Layanan Keuangan</span>
    <span class="admin-topbar__breadcrumb-sep" aria-hidden="true">/</span>
    <span class="admin-topbar__breadcrumb-current">Iuran Warga RT 04</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/warga-iuran.css') }}">
@endpush

@section('content')

{{-- ========================================================
     HEADER & BREADCRUMB
     ======================================================== --}}
<div class="warga-iuran-header">
    <nav class="warga-iuran-header__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('warga.beranda') }}">Beranda</a>
        <span class="sep">/</span>
        <span>Layanan Keuangan</span>
        <span class="sep">/</span>
        <span style="color: #007bff; font-weight: 600;">Iuran Warga RT 04</span>
    </nav>
    <h1 class="warga-iuran-header__title">Monitoring Iuran Warga RT 04</h1>
    <p class="warga-iuran-header__sub">Sistem monitoring status pembayaran iuran warga per bulan (read-only) resmi tercatat oleh Bendahara RT 04 / RW 08.</p>
</div>

{{-- ========================================================
     FILTER BAR TAHUN PERIODE
     ======================================================== --}}
<div class="warga-iuran-filter-bar">
    <div class="warga-iuran-filter-left">
        <span class="warga-iuran-filter-label">
            <i class='bx bx-filter-alt'></i> TAHUN PERIODE:
        </span>
        <select id="select-tahun-iuran" class="warga-iuran-select" onchange="gantiTahunIuran(this.value)" aria-label="Pilih Tahun Periode Iuran">
            <option value="2025" selected>Tahun 2025 (Tahun Berjalan)</option>
            <option value="2024">Tahun 2024 (Riwayat Penuh)</option>
        </select>
    </div>
    <div class="warga-iuran-filter-right">
        Keluarga: <strong>Bpk. Bambang Pamungkas</strong>
    </div>
</div>

{{-- ========================================================
     2 BIG STAT BANNER CARDS
     ======================================================== --}}
<div class="warga-iuran-banner-grid">
    {{-- Card Kiri: Status Bulan Ini (Merah) --}}
    <div class="warga-iuran-banner warga-iuran-banner--merah" id="card-status-bulan-ini">
        <div class="warga-iuran-banner__body">
            <div class="warga-iuran-banner__nominal" id="val-status-nominal">Rp 75.000</div>
            <div class="warga-iuran-banner__subtitle" id="val-status-subtitle">STATUS BULAN INI (MEI 2025)</div>
            <div>
                <span class="warga-iuran-banner__badge" id="badge-status-bulan-ini">BELUM LUNAS</span>
            </div>
            <i class='bx bx-calendar-event warga-iuran-banner__watermark' aria-hidden="true"></i>
        </div>
        <div class="warga-iuran-banner__footer">
            <span id="footer-text-bulan-ini">Periode Bulan Berjalan</span>
            <i class='bx bx-error-circle' aria-hidden="true"></i>
        </div>
    </div>

    {{-- Card Kanan: Tarif Iuran RT 04 (Biru) --}}
    <div class="warga-iuran-banner warga-iuran-banner--biru">
        <div class="warga-iuran-banner__body">
            <div class="warga-iuran-banner__nominal">
                Rp 75.000 <span class="warga-iuran-banner__nominal-sub">/ bln</span>
            </div>
            <div class="warga-iuran-banner__subtitle">TARIF IURAN RT 04</div>
            <p class="warga-iuran-banner__desc">Kebersihan Rp 30.000 • Keamanan Rp 45.000</p>
            <i class='bx bx-wallet warga-iuran-banner__watermark' aria-hidden="true"></i>
        </div>
        <div class="warga-iuran-banner__footer">
            <span>Ketentuan Musyawarah Warga RT 04</span>
            <i class='bx bx-info-circle' aria-hidden="true"></i>
        </div>
    </div>
</div>

{{-- ========================================================
     CARD UTAMA: STATUS PEMBAYARAN IURAN PER BULAN (12 BULAN)
     ======================================================== --}}
<div class="warga-iuran-main-card">
    <div class="warga-iuran-main-header">
        <h2 class="warga-iuran-main-title" id="main-card-title">
            Status Pembayaran Iuran Warga per Bulan (Tahun 2025)
        </h2>
        <div class="warga-iuran-badge-readonly">
            <i class='bx bx-lock-alt'></i> SIFAT DATA: MONITORING READ-ONLY TERCATAT OLEH BENDAHARA RT
        </div>
    </div>

    <div class="warga-iuran-main-body">
        <div class="warga-iuran-months-grid" id="months-grid-container">

            {{-- 1. Januari 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Januari 2025</span>
                    <span class="month-badge month-badge--lunas">LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--lunas">
                        <i class='bx bx-check-circle'></i> Dibayar: 06 Jan 2025
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Kebersihan &amp; Keamanan RT 04 (Lunas)
                </div>
            </div>

            {{-- 2. Februari 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Februari 2025</span>
                    <span class="month-badge month-badge--lunas">LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--lunas">
                        <i class='bx bx-check-circle'></i> Dibayar: 04 Feb 2025
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Kebersihan &amp; Keamanan RT 04 (Lunas)
                </div>
            </div>

            {{-- 3. Maret 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Maret 2025</span>
                    <span class="month-badge month-badge--lunas">LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--lunas">
                        <i class='bx bx-check-circle'></i> Dibayar: 07 Mar 2025
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Kebersihan &amp; Keamanan RT 04 (Lunas)
                </div>
            </div>

            {{-- 4. April 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">April 2025</span>
                    <span class="month-badge month-badge--lunas">LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--lunas">
                        <i class='bx bx-check-circle'></i> Dibayar: 05 Apr 2025
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Kebersihan &amp; Keamanan RT 04 (Lunas)
                </div>
            </div>

            {{-- 5. Mei 2025 (BULAN BERJALAN AKTIF) --}}
            <div class="month-card month-card--active">
                <div class="month-card-header">
                    <div class="month-card-title-wrap">
                        <span class="month-card-name">Mei 2025</span>
                        <span class="month-badge month-badge--berjalan">BERJALAN</span>
                    </div>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--berjalan">
                        <i class='bx bx-calendar-event'></i> Jatuh tempo: 10 Mei 2025
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

            {{-- 6. Juni 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Juni 2025</span>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--future">
                        <i class='bx bx-time'></i> Periode bulan depan
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

            {{-- 7. Juli 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Juli 2025</span>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--future">
                        <i class='bx bx-time'></i> Belum masuk periode
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

            {{-- 8. Agustus 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Agustus 2025</span>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--future">
                        <i class='bx bx-time'></i> Belum masuk periode
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

            {{-- 9. September 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">September 2025</span>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--future">
                        <i class='bx bx-time'></i> Belum masuk periode
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

            {{-- 10. Oktober 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Oktober 2025</span>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--future">
                        <i class='bx bx-time'></i> Belum masuk periode
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

            {{-- 11. November 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">November 2025</span>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--future">
                        <i class='bx bx-time'></i> Belum masuk periode
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

            {{-- 12. Desember 2025 --}}
            <div class="month-card">
                <div class="month-card-header">
                    <span class="month-card-name">Desember 2025</span>
                    <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                </div>
                <div class="month-card-body">
                    <div class="month-card-nominal">Rp 75.000</div>
                    <div class="month-card-info month-card-info--future">
                        <i class='bx bx-time'></i> Belum masuk periode
                    </div>
                </div>
                <div class="month-card-footer">
                    Iuran Wajib Bulanan RT 04
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Data interaktif jika memilih tahun berbeda
    var dataIuranPerTahun = {
        "2025": {
            title: "Status Pembayaran Iuran Warga per Bulan (Tahun 2025)",
            statusBulanIniNominal: "Rp 75.000",
            statusBulanIniSub: "STATUS BULAN INI (MEI 2025)",
            statusBulanIniBadge: "BELUM LUNAS",
            statusBulanIniIsLunas: false,
            footerText: "Periode Bulan Berjalan",
            footerIcon: "bx-error-circle",
            months: [
                { name: "Januari 2025", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 06 Jan 2025", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Februari 2025", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 04 Feb 2025", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Maret 2025", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 07 Mar 2025", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "April 2025", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 05 Apr 2025", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Mei 2025", nominal: "Rp 75.000", status: "berjalan", infoText: "Jatuh tempo: 10 Mei 2025", footer: "Iuran Wajib Bulanan RT 04" },
                { name: "Juni 2025", nominal: "Rp 75.000", status: "future", infoText: "Periode bulan depan", footer: "Iuran Wajib Bulanan RT 04" },
                { name: "Juli 2025", nominal: "Rp 75.000", status: "future", infoText: "Belum masuk periode", footer: "Iuran Wajib Bulanan RT 04" },
                { name: "Agustus 2025", nominal: "Rp 75.000", status: "future", infoText: "Belum masuk periode", footer: "Iuran Wajib Bulanan RT 04" },
                { name: "September 2025", nominal: "Rp 75.000", status: "future", infoText: "Belum masuk periode", footer: "Iuran Wajib Bulanan RT 04" },
                { name: "Oktober 2025", nominal: "Rp 75.000", status: "future", infoText: "Belum masuk periode", footer: "Iuran Wajib Bulanan RT 04" },
                { name: "November 2025", nominal: "Rp 75.000", status: "future", infoText: "Belum masuk periode", footer: "Iuran Wajib Bulanan RT 04" },
                { name: "Desember 2025", nominal: "Rp 75.000", status: "future", infoText: "Belum masuk periode", footer: "Iuran Wajib Bulanan RT 04" }
            ]
        },
        "2024": {
            title: "Status Pembayaran Iuran Warga per Bulan (Tahun 2024)",
            statusBulanIniNominal: "Rp 75.000",
            statusBulanIniSub: "STATUS AKHIR TAHUN (2024)",
            statusBulanIniBadge: "LUNAS LENGKAP",
            statusBulanIniIsLunas: true,
            footerText: "Seluruh Periode 2024 Selesai Terbayar",
            footerIcon: "bx-check-double",
            months: [
                { name: "Januari 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 05 Jan 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Februari 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 06 Feb 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Maret 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 04 Mar 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "April 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 08 Apr 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Mei 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 06 Mei 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Juni 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 05 Jun 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Juli 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 07 Jul 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Agustus 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 05 Agu 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "September 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 06 Sep 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Oktober 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 05 Okt 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "November 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 07 Nov 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" },
                { name: "Desember 2024", nominal: "Rp 75.000", status: "lunas", infoText: "Dibayar: 06 Des 2024", footer: "Iuran Kebersihan & Keamanan RT 04 (Lunas)" }
            ]
        }
    };

    function gantiTahunIuran(tahun) {
        var data = dataIuranPerTahun[tahun] || dataIuranPerTahun["2025"];

        // Update Card Title
        document.getElementById('main-card-title').textContent = data.title;

        // Update Card Stat Kiri
        var cardKiri = document.getElementById('card-status-bulan-ini');
        var valSub = document.getElementById('val-status-subtitle');
        var badgeStatus = document.getElementById('badge-status-bulan-ini');
        var footerText = document.getElementById('footer-text-bulan-ini');
        var footerIcon = cardKiri.querySelector('.warga-iuran-banner__footer i');

        valSub.textContent = data.statusBulanIniSub;
        badgeStatus.textContent = data.statusBulanIniBadge;
        footerText.textContent = data.footerText;
        footerIcon.className = 'bx ' + data.footerIcon;

        if (data.statusBulanIniIsLunas) {
            cardKiri.classList.remove('warga-iuran-banner--merah');
            cardKiri.style.backgroundColor = '#28a745';
            badgeStatus.style.color = '#28a745';
            var footerEl = cardKiri.querySelector('.warga-iuran-banner__footer');
            footerEl.style.backgroundColor = '#1e7e34';
            footerEl.style.color = '#d4edda';
        } else {
            cardKiri.classList.add('warga-iuran-banner--merah');
            cardKiri.style.backgroundColor = '';
            badgeStatus.style.color = '#dc3545';
            var footerEl = cardKiri.querySelector('.warga-iuran-banner__footer');
            footerEl.style.backgroundColor = '';
            footerEl.style.color = '';
        }

        // Render Months Grid
        var grid = document.getElementById('months-grid-container');
        grid.innerHTML = '';

        data.months.forEach(function(m) {
            var cardDiv = document.createElement('div');
            cardDiv.className = 'month-card' + (m.status === 'berjalan' ? ' month-card--active' : '');

            var headerHtml = '';
            var bodyHtml = '';

            if (m.status === 'lunas') {
                headerHtml = `
                    <div class="month-card-header">
                        <span class="month-card-name">${m.name}</span>
                        <span class="month-badge month-badge--lunas">LUNAS</span>
                    </div>
                `;
                bodyHtml = `
                    <div class="month-card-body">
                        <div class="month-card-nominal">${m.nominal}</div>
                        <div class="month-card-info month-card-info--lunas">
                            <i class='bx bx-check-circle'></i> ${m.infoText}
                        </div>
                    </div>
                `;
            } else if (m.status === 'berjalan') {
                headerHtml = `
                    <div class="month-card-header">
                        <div class="month-card-title-wrap">
                            <span class="month-card-name">${m.name}</span>
                            <span class="month-badge month-badge--berjalan">BERJALAN</span>
                        </div>
                        <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                    </div>
                `;
                bodyHtml = `
                    <div class="month-card-body">
                        <div class="month-card-nominal">${m.nominal}</div>
                        <div class="month-card-info month-card-info--berjalan">
                            <i class='bx bx-calendar-event'></i> ${m.infoText}
                        </div>
                    </div>
                `;
            } else {
                headerHtml = `
                    <div class="month-card-header">
                        <span class="month-card-name">${m.name}</span>
                        <span class="month-badge month-badge--belum">BELUM LUNAS</span>
                    </div>
                `;
                bodyHtml = `
                    <div class="month-card-body">
                        <div class="month-card-nominal">${m.nominal}</div>
                        <div class="month-card-info month-card-info--future">
                            <i class='bx bx-time'></i> ${m.infoText}
                        </div>
                    </div>
                `;
            }

            var footerHtml = `
                <div class="month-card-footer">
                    ${m.footer}
                </div>
            `;

            cardDiv.innerHTML = headerHtml + bodyHtml + footerHtml;
            grid.appendChild(cardDiv);
        });
    }
</script>
@endpush
