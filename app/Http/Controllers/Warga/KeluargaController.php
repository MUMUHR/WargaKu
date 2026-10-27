<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KeluargaController extends Controller
{
    private array $mapPendidikan = [
        'tidak_belum_sekolah' => 'Tidak / Belum Sekolah',
        'belum_tamat_sd' => 'Belum Tamat SD / Sederajat',
        'sd_sederajat' => 'SD / Sederajat',
        'smp_sederajat' => 'SMP / Sederajat',
        'sma_sederajat' => 'SLTA / SMA / SMK / Sederajat',
        'd1_d2' => 'Diploma I / II',
        'd3' => 'Diploma III',
        'd4_s1' => 'Diploma IV / Strata 1 (S1)',
        's2' => 'Strata 2 (S2)',
        's3' => 'Strata 3 (S3)',
    ];

    /**
     * Menampilkan Halaman Data Keluarga & Anggota (Layar 2.3.2)
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $keluarga = $this->getKeluargaUser();
        $kepala = $keluarga?->kepalaKeluarga ?? $user->wargas()->first();

        $anggotaKeluarga = $keluarga ? $keluarga->wargas()->urutHierarkiKk()->get() : collect();
        $totalJiwa = $anggotaKeluarga->where('status_warga', 'hidup')->count();
        $alamatKeluarga = $keluarga?->alamat ?? '-';

        $riwayatPengajuan = $keluarga
            ? Riwayat_Perubahan::with(['warga', 'admin'])
                ->where('keluarga_id', $keluarga->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $totalMenunggu = $riwayatPengajuan->where('status', 'menunggu')->count();

        // Data payload untuk Modal Detail Anggota & Modal Review Pengajuan
        $dataWargaMap = $keluarga ? $this->mapDataWargaModal($anggotaKeluarga, $keluarga, $alamatKeluarga) : [];
        $dataPengajuanMap = $keluarga ? $this->mapDataPengajuanModal($riwayatPengajuan) : [];

        return view('warga.keluarga', compact(
            'user',
            'kepala',
            'keluarga',
            'anggotaKeluarga',
            'totalJiwa',
            'totalMenunggu',
            'riwayatPengajuan',
            'alamatKeluarga',
            'dataWargaMap',
            'dataPengajuanMap'
        ));
    }

    /**
     * Menampilkan Edit Data Keluarga
     */
    public function tampilkanFormEdit(Request $request, ?string $nik = null): View
    {
        $user = auth()->user();
        $keluarga = $this->getKeluargaUser();
        $kepala = $keluarga?->kepalaKeluarga ?? $user->wargas()->first();

        $anggotaKeluarga = $keluarga
            ? $keluarga->wargas()->where('status_warga', 'hidup')->urutHierarkiKk()->get()
            : collect();

        $alamatKeluarga = $keluarga ? $keluarga->alamat : 'RT 04';

        $targetNik = $request->query('nik')
            ?? $request->query('target')
            ?? $nik
            ?? $anggotaKeluarga->first()?->nik;

        $anggotaDataEdit = [];

        foreach ($anggotaKeluarga as $ang) {
            $shdkLabel = ucwords(str_replace('_', ' ', $ang->status_hubungan ?? '-'));
            $pendidikanLabel = $this->mapPendidikan[$ang->pendidikan_terakhir] ?? ucwords(str_replace('_', ' ', $ang->pendidikan_terakhir ?? '-'));

            $words = explode(' ', trim($ang->nama));
            $avatar = strtoupper(substr($words[0] ?? 'W', 0, 1).substr($words[1] ?? ($words[0] ?? 'G'), 0, 1));

            $anggotaDataEdit[$ang->nik] = [
                'avatar' => $avatar,
                'nik' => $ang->nik,
                'nama' => $ang->nama,
                'tempatLahir' => $ang->tempat_lahir ?? '-',
                'tglLahir' => $ang->tanggal_lahir ? Carbon::parse($ang->tanggal_lahir)->format('d-m-Y') : '-',
                'tglLahirNew' => $ang->tanggal_lahir ? Carbon::parse($ang->tanggal_lahir)->format('Y-m-d') : '',
                'jk' => $ang->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : 'Perempuan',
                'agama' => ucfirst($ang->agama ?? 'Islam'),
                'pendidikanOld' => $pendidikanLabel,
                'pendidikanNew' => $pendidikanLabel,
                'pekerjaanOld' => $ang->pekerjaan ?? '-',
                'pekerjaanNew' => $ang->pekerjaan ?? '',
                'alamat' => $ang->alamat ?? $alamatKeluarga,
                'shdk' => $shdkLabel,
                'statusWarga' => $ang->status_warga === 'hidup' ? 'Hidup (Aktif)' : ucfirst($ang->status_warga),
                'alasan' => '',
            ];
        }

        return view('warga.keluarga-edit', compact(
            'user',
            'kepala',
            'keluarga',
            'anggotaKeluarga',
            'alamatKeluarga',
            'targetNik',
            'anggotaDataEdit'
        ));
    }

    /**
     * Memproses Pengajuan Perubahan Data Warga ke Tabel Riwayat_Perubahan
     */
    public function simpanPengajuanEdit(Request $request): RedirectResponse
    {
        // 1. Validasi input form
        $request->validate([
            'target_anggota' => 'required',
            'new_nama' => 'required|string|max:100',
            'new_tempat_lahir' => 'required|string|max:100',
            'new_tgl_lahir' => 'required|date',
            'alasan_perubahan' => 'required|string',
            'bukti' => 'nullable',
        ]);

        // 2. Ambil keluarga & warga sasaran perubahan
        $keluarga = $this->getKeluargaUser();
        if (! $keluarga) {
            return redirect()->route('warga.keluarga')->with('error', 'Data Kartu Keluarga tidak ditemukan.');
        }

        $targetKey = $request->input('target_anggota');
        $warga = $keluarga->wargas()
            ->where(function ($q) use ($targetKey) {
                $q->where('nik', $targetKey)->orWhere('id', $targetKey);
            })
            ->first();

        if (! $warga) {
            return redirect()->route('warga.keluarga')->with('error', 'Warga yang dipilih tidak valid.');
        }

        // 3. Deteksi hanya field yang benar-benar mengalami perubahan
        [$dataLama, $dataBaru] = $this->deteksiPerubahanData($warga, $request, $keluarga);

        // 4. Catat riwayat pengajuan dengan status menunggu verifikasi RT
        Riwayat_Perubahan::create([
            'keluarga_id' => $keluarga->id,
            'warga_id' => $warga->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'bukti_path' => $this->uploadBerkasBukti($request),
            'status' => 'menunggu',
            'alasan' => $request->input('alasan_perubahan', 'Pembaruan data kependudukan'),
        ]);

        return redirect()->route('warga.keluarga')
            ->with('success', 'Pengajuan perubahan data kependudukan berhasil dikirim dan masuk antrean verifikasi RT 04.');
    }

    /**
     * Menampilkan Formulir Pendaftaran Anggota Keluarga Baru
     */
    public function tampilkanFormTambah(Request $request): View
    {
        $user = auth()->user();
        $kepala = $user->wargaPemilik
            ?? $user->wargas()->where('status_warga', 'hidup')->where('status_hubungan', 'kepala_keluarga')->first()
            ?? $user->wargas()->where('status_warga', 'hidup')->first()
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga ?? $user->wargas()->first()?->keluarga;

        $alamatKeluarga = $keluarga ? $keluarga->alamat : 'RT 04';

        return view('warga.keluarga-tambah', compact(
            'user',
            'kepala',
            'keluarga',
            'alamatKeluarga'
        ));
    }

    /**
     * Memproses Pendaftaran Anggota Keluarga Baru ke Tabel Riwayat_Perubahan
     */
    public function simpanPengajuanTambah(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'hubungan_keluarga' => 'required',
            'agama' => 'required',
            'pendidikan_terakhir' => 'required',
            'pekerjaan' => 'required|string',
            'bukti' => 'nullable',
        ]);

        $user = auth()->user();
        $kepala = $user->wargaPemilik
            ?? $user->wargas()->where('status_warga', 'hidup')->where('status_hubungan', 'kepala_keluarga')->first()
            ?? $user->wargas()->where('status_warga', 'hidup')->first()
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga ?? $user->wargas()->first()?->keluarga;

        if (! $keluarga) {
            return redirect()->route('warga.keluarga')->with('error', 'Data Kartu Keluarga tidak ditemukan.');
        }

        $dataBaru = [
            'nik' => $request->input('nik') ?: null,
            'nama' => $request->input('nama_lengkap'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'tanggal_lahir' => $request->input('tanggal_lahir'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'agama' => $request->input('agama'),
            'pendidikan_terakhir' => $request->input('pendidikan_terakhir'),
            'pekerjaan' => $request->input('pekerjaan'),
            'alamat' => $request->input('alamat_domisili') ?? $request->input('alamat') ?? $keluarga->alamat,
            'status_hubungan' => $request->input('hubungan_keluarga'),
            'status_warga' => 'hidup',
        ];

        $buktiPath = $this->uploadBerkasBukti($request);

        Riwayat_Perubahan::create([
            'keluarga_id' => $keluarga->id,
            'warga_id' => null,
            'jenis_pengajuan' => 'tambah_anggota',
            'data_lama' => null,
            'data_baru' => $dataBaru,
            'bukti_path' => $buktiPath,
            'status' => 'menunggu',
            'alasan' => 'Pendaftaran anggota keluarga baru ('.$request->input('nama_lengkap').')',
        ]);

        return redirect()->route('warga.keluarga')
            ->with('success', 'Pendaftaran anggota keluarga baru berhasil diajukan dan menunggu verifikasi Pengurus RT 04.');
    }

    /**
     * Membatalkan pengajuan perubahan data keluarga yang masih berstatus menunggu verifikasi
     */
    public function batalkanPengajuan(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $kepala = $user->wargaPemilik
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first()
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga ?? $user->keluarga;

        if (! $keluarga) {
            return redirect()->route('warga.keluarga')->with('error', 'Data keluarga tidak ditemukan.');
        }

        $pengajuan = Riwayat_Perubahan::where('keluarga_id', $keluarga->id)->findOrFail($id);

        if ($pengajuan->status !== 'menunggu') {
            return redirect()->route('warga.keluarga')->with('error', 'Hanya pengajuan dengan status Menunggu Verifikasi yang dapat dibatalkan.');
        }

        $pengajuan->status = 'dibatalkan';
        $pengajuan->alasan = ($pengajuan->alasan ? $pengajuan->alasan.' ' : '').'(Dibatalkan oleh pemohon)';
        $pengajuan->save();

        return redirect()->route('warga.keluarga')
            ->with('success', 'Pengajuan perubahan data berhasil dibatalkan dan telah ditarik dari antrean verifikasi RT.');
    }

    /**
     * Mengunggah berkas bukti pendukung (mendukung 1 berkas maupun banyak berkas)
     */
    private function uploadBerkasBukti(Request $request): string
    {
        if (! $request->hasFile('bukti')) {
            return '';
        }

        $files = is_array($request->file('bukti')) ? $request->file('bukti') : [$request->file('bukti')];
        $buktiPaths = [];

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $namaBerkas = 'bukti_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti'), $namaBerkas);

            $buktiPaths[] = 'uploads/bukti/'.$namaBerkas;
        }

        return match (count($buktiPaths)) {
            0 => '',
            1 => $buktiPaths[0],
            default => json_encode($buktiPaths),
        };
    }

    /**
     * Mengambil entitas keluarga yang terafiliasi dengan user saat ini
     */
    private function getKeluargaUser(): ?Keluarga
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        $kepala = $user->wargaPemilik
            ?? $user->wargas()->where('status_warga', 'hidup')->where('status_hubungan', 'kepala_keluarga')->first()
            ?? $user->wargas()->where('status_warga', 'hidup')->first()
            ?? $user->wargas()->first();

        return $kepala?->keluarga ?? $user->wargas()->first()?->keluarga ?? $user->keluarga;
    }

    /**
     * Membandingkan data database dengan input form untuk menemukan perbedaannya
     */
    private function deteksiPerubahanData(Warga $warga, Request $request, Keluarga $keluarga): array
    {
        // Kamus pemetaan: nama input form => nama kolom database
        $fieldMap = [
            'new_nama' => 'nama',
            'new_tempat_lahir' => 'tempat_lahir',
            'new_tgl_lahir' => 'tanggal_lahir',
            'new_jk' => 'jenis_kelamin',
            'new_agama' => 'agama',
            'new_pendidikan' => 'pendidikan_terakhir',
            'new_pekerjaan' => 'pekerjaan',
            'new_alamat' => 'alamat',
            'new_shdk' => 'status_hubungan',
            'new_status_warga' => 'status_warga',
        ];

        $dataLama = [];
        $dataBaru = [];

        foreach ($fieldMap as $inputKey => $kolom) {
            $old = $this->formatNilaiPerubahan($kolom, $warga->$kolom ?? ($kolom === 'alamat' ? $keluarga->alamat : ''));
            $new = $this->formatNilaiPerubahan($kolom, $request->input($inputKey, $warga->$kolom));

            if ($old !== $new && $new !== '') {
                $dataLama[$kolom] = $old;
                $dataBaru[$kolom] = $new;
            }
        }

        // Fallback jika tidak ada perubahan sama sekali yang diedit
        if (empty($dataBaru)) {
            $dataLama['pekerjaan'] = $warga->pekerjaan ?? '-';
            $dataBaru['pekerjaan'] = $request->input('new_pekerjaan', $warga->pekerjaan ?? '-');
        }

        return [$dataLama, $dataBaru];
    }

    /**
     * Normalisasi format nilai (tanggal, enum, label) agar komparasi akurat
     */
    private function formatNilaiPerubahan(string $kolom, mixed $nilai): string
    {
        $val = trim((string) $nilai);

        return match ($kolom) {
            'tanggal_lahir' => $val ? Carbon::parse($val)->format('Y-m-d') : '',
            'jenis_kelamin' => (string) Warga::normalisasiJenisKelamin($val),
            'agama' => (string) Warga::normalisasiAgama($val),
            'status_hubungan' => (string) Warga::normalisasiStatusHubungan($val),
            'status_warga' => (string) Warga::normalisasiStatusWarga($val),
            'pendidikan_terakhir' => (string) ($this->mapPendidikan[Warga::normalisasiPendidikan($val)] ?? $this->mapPendidikan[$val] ?? $val),
            'pekerjaan', 'alamat' => $val === '' ? '-' : $val,
            default => $val,
        };
    }

    /**
     * Memetakan data anggota keluarga untuk Modal Detail Kependudukan
     */
    private function mapDataWargaModal($anggotaKeluarga, Keluarga $keluarga, string $alamatKeluarga): array
    {
        $dataWargaMap = [];

        foreach ($anggotaKeluarga as $w) {
            $statusDesc = match ($w->status_warga) {
                'hidup' => 'Hidup (Aktif)',
                'meninggal' => 'Meninggal (Almarhum)',
                'pindah_rumah' => 'Pindah Rumah',
                'pindah_kk' => 'Pindah KK',
                default => ucfirst($w->status_warga),
            };

            $dataWargaMap[$w->nik] = [
                'nama' => $w->nama,
                'namalengkap' => $w->nama,
                'nik' => $w->nik,
                'ttl' => ($w->tempat_lahir ? $w->tempat_lahir.', ' : '').($w->tanggal_lahir ? Carbon::parse($w->tanggal_lahir)->translatedFormat('d F Y') : '-'),
                'jk' => $w->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : 'Perempuan',
                'agama' => ucfirst($w->agama ?? 'Islam'),
                'nokk' => $keluarga->no_kk,
                'hubungan' => ucwords(str_replace('_', ' ', $w->status_hubungan ?? '-')),
                'pendidikan' => $this->mapPendidikan[$w->pendidikan_terakhir] ?? ucwords(str_replace('_', ' ', $w->pendidikan_terakhir ?? '-')),
                'pekerjaan' => $w->pekerjaan ?? '-',
                'alamat' => $w->alamat ?? $alamatKeluarga,
                'status' => $statusDesc,
                'isHidup' => $w->status_warga === 'hidup',
            ];
        }

        return $dataWargaMap;
    }

    /**
     * Memetakan riwayat pengajuan perubahan data untuk Modal Komparasi & Dokumen
     */
    private function mapDataPengajuanModal($riwayatPengajuan): array
    {
        $fieldLabels = [
            'nama' => 'Nama Lengkap',
            'nik' => 'NIK',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'agama' => 'Agama',
            'pendidikan_terakhir' => 'Pendidikan Terakhir',
            'pekerjaan' => 'Pekerjaan Utama',
            'status_perkawinan' => 'Status Perkawinan',
            'status_hubungan' => 'Hubungan Keluarga',
            'alamat' => 'Alamat Domisili',
            'status_warga' => 'Status Keberadaan',
        ];

        $dataPengajuanMap = [];

        foreach ($riwayatPengajuan as $r) {
            $targetWarga = $r->warga;
            $targetNama = $targetWarga?->nama ?? ($r->data_baru['nama'] ?? 'Anggota Baru');
            $targetHub = $targetWarga ? ucwords(str_replace('_', ' ', $targetWarga->status_hubungan)) : 'Anggota Baru';
            $targetJK = $targetWarga ? ($targetWarga->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : 'Perempuan') : '-';
            $targetTTL = $targetWarga ? (($targetWarga->tempat_lahir ? $targetWarga->tempat_lahir.', ' : '').($targetWarga->tanggal_lahir ? Carbon::parse($targetWarga->tanggal_lahir)->format('d-m-Y') : '')) : '-';

            $statusLabel = match ($r->status) {
                'menunggu' => 'Menunggu Persetujuan RT',
                'disetujui' => 'Telah Disetujui RT',
                'ditolak' => 'Ditolak oleh RT',
                'dibatalkan' => 'Dibatalkan',
                default => ucfirst($r->status),
            };

            $footerText = match ($r->status) {
                'menunggu' => 'Pengajuan ini sedang dalam antrean verifikasi oleh Pengurus RT 04. Mohon menunggu konfirmasi.',
                'disetujui' => 'Pengajuan telah disetujui oleh RT 04. Data keluarga Anda telah resmi diperbarui.',
                'ditolak' => 'Pengajuan ditolak oleh Ketua RT 04. Silakan ajukan ulang perubahan berkas.',
                'dibatalkan' => 'Pengajuan ini telah Anda batalkan dan tidak diproses oleh Pengurus RT 04.',
                default => '-',
            };

            // Komparasi field yang diubah
            $komparasi = [];
            $dataLama = (array) ($r->data_lama ?? []);
            $dataBaru = (array) ($r->data_baru ?? []);
            $allKeys = array_unique(array_merge(array_keys($dataLama), array_keys($dataBaru)));

            foreach ($allKeys as $k) {
                if (in_array($k, ['id', 'warga_id', 'keluarga_id', 'created_at', 'updated_at'])) {
                    continue;
                }

                $valOld = $this->formatNilaiTampilan($k, $dataLama[$k] ?? '-');
                $valNew = $this->formatNilaiTampilan($k, $dataBaru[$k] ?? '-');

                $isChanged = ($valOld !== $valNew);

                if ($isChanged || $r->jenis_pengajuan === 'tambah_anggota') {
                    $komparasi[] = [
                        'field' => $fieldLabels[$k] ?? ucwords(str_replace('_', ' ', $k)),
                        'old' => $valOld,
                        'new' => $valNew,
                        'changed' => true,
                    ];
                }
            }

            if (empty($komparasi)) {
                $komparasi[] = [
                    'field' => 'Data Perubahan',
                    'old' => '-',
                    'new' => 'Pembaruan data kependudukan',
                    'changed' => true,
                ];
            }

            // Berkas bukti pendukung
            $docs = [];
            foreach ($r->bukti_list as $idx => $bPath) {
                $docUrl = (str_starts_with($bPath, 'http://') || str_starts_with($bPath, 'https://'))
                    ? $bPath
                    : asset(file_exists(public_path('storage/'.$bPath)) ? 'storage/'.$bPath : $bPath);

                $bName = basename($bPath);
                $isPdf = str_ends_with(strtolower($bName), '.pdf');

                $docs[] = [
                    'label' => ($idx + 1).'. '.$bName,
                    'filename' => $bName,
                    'icon' => $isPdf ? 'bxs-file-pdf' : 'bx-file-blank',
                    'iconColor' => $isPdf ? '#dc2626' : '#0284c7',
                    'src' => $docUrl,
                    'isPdf' => $isPdf,
                    'verified' => $r->status === 'disetujui' ? 'Dokumen Asli Terverifikasi' : ($r->status === 'ditolak' ? 'Perlu Unggah Ulang Berkas Asli' : 'Dokumen Terlampir'),
                ];
            }

            $dataPengajuanMap[$r->id] = [
                'status' => $r->status,
                'statusLabel' => $statusLabel,
                'targetTitle' => 'Target Data: '.$targetNama.' ('.$targetHub.')',
                'targetSub' => 'NIK: '.($targetWarga?->nik ?? '-').' • '.$targetJK.' • Lahir: '.$targetTTL,
                'tgl' => 'Diajukan pada: '.$r->created_at->translatedFormat('d M Y').', '.$r->created_at->format('H:i').' WIB',
                'attrCount' => count($komparasi).' Atribut Diperbarui',
                'komparasi' => $komparasi,
                'alasan' => $r->alasan ?? 'Pembaruan data kependudukan keluarga.',
                'catatanRT' => $r->catatan_admin ?? ($r->status === 'ditolak' ? $r->alasan : ''),
                'footerText' => $footerText,
                'docs' => $docs,
            ];
        }

        return $dataPengajuanMap;
    }

    /**
     * Memformat nilai data warga menjadi teks tampilan yang rapi dan mudah dibaca
     */
    private function formatNilaiTampilan(string $kolom, mixed $nilai): string
    {
        $val = trim((string) $nilai);
        if ($val === '' || $val === '-') {
            return '-';
        }

        return match ($kolom) {
            'pendidikan_terakhir' => $this->mapPendidikan[$val] ?? $val,
            'jenis_kelamin' => match ($val) {
                'laki_laki' => 'Laki-Laki',
                'perempuan' => 'Perempuan',
                default => $val,
            },
            'status_warga' => match ($val) {
                'hidup', 'Hidup (Aktif)' => 'Hidup (Aktif)',
                'pindah_kk', 'Pindah KK' => 'Pindah KK',
                'pindah_rumah', 'Pindah Rumah' => 'Pindah Rumah',
                'meninggal', 'Meninggal' => 'Meninggal',
                default => ucfirst($val),
            },
            'status_hubungan' => ucwords(str_replace('_', ' ', $val)),
            default => $val,
        };
    }
}
