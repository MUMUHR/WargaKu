<?php

namespace App\Services;

use App\Models\Iuran;
use App\Models\Keluarga;
use Carbon\Carbon;

/**
 * Service Layer untuk Mengelola Seluruh Logika Bisnis Iuran Warga (BR-06)
 */
class IuranService
{
    /**
     * Nama-nama bulan kalender dalam bahasa Indonesia
     */
    public const DAFTAR_BULAN = [
        1 => 'Januari', 
        2 => 'Februari', 
        3 => 'Maret', 
        4 => 'April',
        5 => 'Mei', 
        6 => 'Juni', 
        7 => 'Juli', 
        8 => 'Agustus',
        9 => 'September', 
        10 => 'Oktober', 
        11 => 'November', 
        12 => 'Desember',
    ];

    /**
     * BR-06: Menentukan sumber besaran nominal standar terkini sesuai aturan 3 prioritas:
     * 1. Baris terbaru di database berstatus 'belum_lunas' (tahun & bulan terbesar)
     * 2. Baris terbaru di database dengan status apa pun
     * 3. Konfigurasi config/wargaku.php (default Rp 75.000)
     */
    public function ambilNominalStandar(): float
    {
        // Cari baris iuran terbaru di database dengan aturan prioritas BR-06:
        // Utamakan yang 'belum_lunas', lalu tahun & bulan terbesar.
        $nominal = Iuran::orderByRaw("CASE WHEN status = 'belum_lunas' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderByDesc('id')
            ->value('nominal');

        return (float) ($nominal ?? config('wargaku.nominal_iuran_awal', 75000));
    }

    /**
     * Mengambil catatan/keterangan tarif pembukuan terkini yang dibuat oleh Admin RT
     */
    public function ambilCatatanTarifAdmin(): string
    {
        $catatan = Iuran::whereNotNull('catatan')
            ->where('catatan', '!=', '')
            ->orderByRaw("CASE WHEN status = 'belum_lunas' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderByDesc('id')
            ->value('catatan');

        return $catatan ?? 'Iuran operasional kebersihan sampah, keamanan pos ronda 24 jam, & kas sosial RT 04.';
    }

    /**
     * Menghitung bulan awal tagih untuk suatu keluarga pada tahun tertentu (BR-06)
     * - Jika tahun yang diminta < tahun KK terdaftar: return 13 (seluruh bulan 1-12 N/A)
     * - Jika tahun yang diminta > tahun KK terdaftar: return 1 (Januari)
     * - Jika tahun yang diminta == tahun KK terdaftar: return bulan saat KK dibuat
     */
    public function hitungBulanAwalTagih(Keluarga $keluarga, int $tahun): int
    {
        if (!$keluarga->created_at) {
            return 1;
        }

        $waktuDaftar  = $keluarga->created_at->copy()->timezone('Asia/Jakarta');
        $createdYear  = $waktuDaftar->year;
        $createdMonth = $waktuDaftar->month;

        if ($tahun < $createdYear) {
            return 13; // Tahun sebelum KK terdaftar: seluruh bulan 1-12 otomatis N/A
        }

        return ($tahun > $createdYear) ? 1 : $createdMonth;
    }

    /**
     * BR-06: Lazy generation 12 bulan untuk satu keluarga aktif
     */
    public function generateLazyIuranKeluarga(Keluarga $keluarga, int $tahun): void
    {
        if (!$keluarga->wargas()->where('status_warga', 'hidup')->exists()) {
            return;
        }

        $bulanAwal = $this->hitungBulanAwalTagih($keluarga, $tahun);
        if ($bulanAwal > 12) {
            return; // Tahun sebelum KK terdaftar: tidak ada tagihan
        }

        // Ambil daftar bulan yang sudah tercatat di database untuk keluarga ini
        $existingBulan = Iuran::where('keluarga_id', $keluarga->id)
            ->where('tahun', $tahun)
            ->pluck('bulan')
            ->flip()
            ->all();

        $nominalDefault = null;
        $catatanDefault = null;
        $now            = now('Asia/Jakarta');
        $dataBaru       = [];

        for ($b = $bulanAwal; $b <= 12; $b++) {
            if (isset($existingBulan[$b])) {
                continue; // Lewati jika baris bulan ini sudah ada
            }

            // Lazy fetch: nominal & catatan hanya dicari jika memang ada baris baru
            $nominalDefault ??= $this->ambilNominalStandar();
            $catatanDefault ??= $this->ambilCatatanTarifAdmin();

            $dataBaru[] = [
                'keluarga_id'   => $keluarga->id,
                'bulan'         => $b,
                'tahun'         => $tahun,
                'nominal'       => $nominalDefault,
                'status'        => 'belum_lunas',
                'catatan'       => $catatanDefault,
                'tanggal_bayar' => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        if (!empty($dataBaru)) {
            Iuran::insertOrIgnore($dataBaru);
        }
    }

    /**
     * BR-06: Lazy generation 12 bulan untuk seluruh KK aktif di RT 04
     */
    public function generateLazyIuranSemuaKeluargaAktif(int $tahun): void
    {
        // 1. Ambil hanya ID & created_at untuk KK aktif (hemat memori)
        $keluargasAktif = Keluarga::select('id', 'created_at')
            ->whereHas('wargas', function ($q) {
                $q->where('status_warga', 'hidup');
            })->get();

        if ($keluargasAktif->isEmpty()) {
            return;
        }

        // 2. Ambil peta bulan yang sudah ada di database untuk tahun ini (1 query cepat berbasis indeks)
        $existingBulanPerKeluarga = Iuran::where('tahun', $tahun)
            ->select('keluarga_id', 'bulan')
            ->get()
            ->groupBy('keluarga_id')
            ->map(fn($items) => $items->pluck('bulan')->flip()->all());

        $nominalDefault = null;
        $catatanDefault = null;
        $now            = now('Asia/Jakarta');
        $dataBaru       = [];

        // 3. Hanya siapkan baris yang benar-benar belum ada di database
        foreach ($keluargasAktif as $keluarga) {
            $bulanAwal = $this->hitungBulanAwalTagih($keluarga, $tahun);
            if ($bulanAwal > 12) {
                continue;
            }

            $bulanAda = $existingBulanPerKeluarga[$keluarga->id] ?? [];

            for ($b = $bulanAwal; $b <= 12; $b++) {
                if (isset($bulanAda[$b])) {
                    continue; // Lewati jika sudah ada di database
                }

                $nominalDefault ??= $this->ambilNominalStandar();
                $catatanDefault ??= $this->ambilCatatanTarifAdmin();

                $dataBaru[] = [
                    'keluarga_id'   => $keluarga->id,
                    'bulan'         => $b,
                    'tahun'         => $tahun,
                    'nominal'       => $nominalDefault,
                    'status'        => 'belum_lunas',
                    'catatan'       => $catatanDefault,
                    'tanggal_bayar' => null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        // 4. Batch insert aman dengan chunking (mencegah overflow SQL variables)
        if (!empty($dataBaru)) {
            foreach (array_chunk($dataBaru, 100) as $chunk) {
                Iuran::insertOrIgnore($chunk);
            }
        }
    }

    /**
     * Membentuk struktur data 12 card bulan (Januari s.d. Desember) untuk Warga (Layar 2.2.26 / BR-06)
     */
    public function formatCards12Bulan(Keluarga $keluarga, int $tahunDipilih, int $tahunBerjalan, int $bulanBerjalan): array
    {
        $iuranRecords = Iuran::where('keluarga_id', $keluarga->id)
            ->where('tahun', $tahunDipilih)
            ->get()
            ->keyBy('bulan');

        $bulanAwalTagih = $this->hitungBulanAwalTagih($keluarga, $tahunDipilih);
        $nominalStandar = $this->ambilNominalStandar();
        $cards12Bulan   = [];

        for ($b = 1; $b <= 12; $b++) {
            $namaBln = self::DAFTAR_BULAN[$b] ?? "Bulan {$b}";
            $record  = $iuranRecords->get($b);

            if (!$record && $b < $bulanAwalTagih) {
                $cards12Bulan[] = [
                    'bulan'               => $b,
                    'nama_bulan'          => "{$namaBln} {$tahunDipilih}",
                    'nominal_format'      => '-',
                    'status'              => 'na',
                    'badge_label'         => 'N/A',
                    'badge_class'         => 'month-badge--na',
                    'penanda_berjalan'    => false,
                    'info_tanggal'        => '-',
                    'is_lunas'            => false,
                    'footer_text'         => 'Dikecualikan dari Kewajiban',
                    'catatan'             => 'Dikecualikan dari kewajiban iuran',
                    'card_modifier_class' => 'month-card--na',
                ];
                continue;
            }

            $isLunas         = $record && $record->status === 'lunas';
            $isBulanBerjalan = !$isLunas && ($tahunDipilih === $tahunBerjalan && $b === $bulanBerjalan);
            $isMenunggak     = !$isLunas && (($tahunDipilih < $tahunBerjalan) || ($tahunDipilih === $tahunBerjalan && $b < $bulanBerjalan));

            $nominalValue  = $record ? (float) $record->nominal : $nominalStandar;
            $nominalFormat = 'Rp ' . number_format($nominalValue, 0, ',', '.');
            $catatan       = ($record && !empty($record->catatan)) ? $record->catatan : 'Iuran Kebersihan & Keamanan RT 04';

            if ($isLunas) {
                $tglBayar     = $record->tanggal_bayar ? Carbon::parse($record->tanggal_bayar)->locale('id')->translatedFormat('d M Y') : '-';
                $badgeLabel   = 'LUNAS';
                $badgeClass   = 'month-badge--lunas';
                $cardModifier = '';
                $infoTanggal  = "Dibayar: {$tglBayar}";
            } elseif ($isBulanBerjalan) {
                $badgeLabel   = 'BELUM LUNAS';
                $badgeClass   = 'month-badge--belum';
                $cardModifier = 'month-card--active';
                $infoTanggal  = '-';
            } elseif ($isMenunggak) {
                $badgeLabel   = 'MENUNGGAK';
                $badgeClass   = 'month-badge--menunggak';
                $cardModifier = 'month-card--menunggak';
                $infoTanggal  = '-';
            } else {
                $badgeLabel   = 'BELUM LUNAS';
                $badgeClass   = 'month-badge--belum';
                $cardModifier = '';
                $infoTanggal  = '-';
            }

            $cards12Bulan[] = [
                'bulan'               => $b,
                'nama_bulan'          => "{$namaBln} {$tahunDipilih}",
                'nominal_format'      => $nominalFormat,
                'status'              => $isLunas ? 'lunas' : 'belum_lunas',
                'badge_label'         => $badgeLabel,
                'badge_class'         => $badgeClass,
                'penanda_berjalan'    => $isBulanBerjalan,
                'info_tanggal'        => $infoTanggal,
                'is_lunas'            => $isLunas,
                'footer_text'         => 'Kas Wajib Bulanan RT 04',
                'catatan'             => $catatan,
                'card_modifier_class' => $cardModifier,
            ];
        }

        return $cards12Bulan;
    }

    /**
     * Mencatat pelunasan satu baris iuran oleh Admin RT (BR-06 / Layar 2.2.18)
     */
    public function catatPelunasan(Iuran $iuran, ?string $tanggalBayar = null, ?float $nominal = null, ?string $catatan = null): Iuran
    {
        $waktuBayar = !empty($tanggalBayar)
            ? Carbon::parse($tanggalBayar, 'Asia/Jakarta')->setTimeFrom(Carbon::now('Asia/Jakarta'))
            : Carbon::now('Asia/Jakarta');

        $updateData = [
            'status'        => 'lunas',
            'tanggal_bayar' => $waktuBayar,
        ];

        if ($nominal !== null) {
            $updateData['nominal'] = max(0, $nominal);
        }

        if ($catatan !== null) {
            $updateData['catatan'] = trim($catatan) ?: null;
        }

        $iuran->update($updateData);

        return $iuran;
    }

    /**
     * Memperbarui standar iuran bulanan RT mulai bulan berjalan s.d. Desember (BR-06 / Layar 2.2.17)
     */
    public function aturStandarIuran(float $nominalBaru, ?string $catatanBaru, int $tahunBerjalan, int $bulanBerjalan): int
    {
        $updateData = [
            'nominal' => max(0, $nominalBaru),
        ];

        if ($catatanBaru !== null && trim($catatanBaru) !== '') {
            $updateData['catatan'] = trim($catatanBaru);
        }

        return Iuran::where('status', 'belum_lunas')
            ->where('tahun', $tahunBerjalan)
            ->where('bulan', '>=', $bulanBerjalan)
            ->update($updateData);
    }

    /**
     * Menghitung statistik ringkasan kas untuk panel Admin RT menggunakan Eloquent murni
     */
    public function hitungStatistikAdmin(int $tahunBerjalan, int $bulanBerjalan): array
    {
        return [
            'totalPenerimaanKas' => (float) Iuran::where('status', 'lunas')->sum('nominal'),

            'totalKkLunasBulanIni' => Iuran::where('status', 'lunas')
                ->where('tahun', $tahunBerjalan)
                ->where('bulan', $bulanBerjalan)
                ->count(),

            'totalKkBelumBulanIni' => Iuran::where('status', 'belum_lunas')
                ->where('tahun', $tahunBerjalan)
                ->where('bulan', $bulanBerjalan)
                ->count(),

            'totalTunggakanKeseluruhan' => Iuran::where('status', 'belum_lunas')
                ->where('tahun', $tahunBerjalan)
                ->where('bulan', '<=', $bulanBerjalan)
                ->count(),
        ];
    }
}
