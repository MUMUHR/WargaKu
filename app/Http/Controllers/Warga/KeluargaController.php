<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeluargaController extends Controller
{
    private array $mapPendidikan = [
        'tidak_belum_sekolah' => 'Tidak / Belum Sekolah',
        'sd_sederajat' => 'SD / Sederajat',
        'smp_sederajat' => 'SMP / Sederajat',
        'sma_sederajat' => 'SMA / Sederajat',
        'd1_d3' => 'D1 - D3',
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

        // Ambil data kepala keluarga / warga yang terikat pada akun
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        $anggotaKeluarga = collect();
        $totalJiwa = 0;
        $totalMenunggu = 0;
        $riwayatPengajuan = collect();
        $alamatKeluarga = '-';
        $dataWargaMap = [];
        $dataPengajuanMap = [];

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
        ];

        if ($keluarga) {
            $anggotaKeluarga = $keluarga->wargas()
                ->where('status_warga', 'hidup')
                ->urutHierarkiKk()
                ->get();

            $totalJiwa = $anggotaKeluarga->count();

            $alamatKeluarga = $keluarga->alamat;

            $riwayatPengajuan = Riwayat_Perubahan::with(['warga', 'admin'])
                ->where('keluarga_id', $keluarga->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalMenunggu = $riwayatPengajuan->where('status', 'menunggu')->count();

            // Map data warga untuk Modal Detail Kependudukan
            foreach ($anggotaKeluarga as $w) {
                $salutation = match ($w->status_hubungan) {
                    'kepala_keluarga', 'suami' => 'Bpk. ',
                    'istri' => 'Ibu ',
                    default => '',
                };

                $dataWargaMap[$w->nik] = [
                    'nama' => $salutation . $w->nama,
                    'namalengkap' => $w->nama,
                    'nik' => $w->nik,
                    'ttl' => ($w->tempat_lahir ? $w->tempat_lahir . ', ' : '') . ($w->tanggal_lahir ? Carbon::parse($w->tanggal_lahir)->translatedFormat('d F Y') : '-'),
                    'jk' => $w->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : 'Perempuan',
                    'agama' => ucfirst($w->agama ?? 'Islam'),
                    'nokk' => $keluarga->no_kk,
                    'hubungan' => ucwords(str_replace('_', ' ', $w->status_hubungan ?? '-')),
                    'pendidikan' => $this->mapPendidikan[$w->pendidikan_terakhir] ?? ucwords(str_replace('_', ' ', $w->pendidikan_terakhir ?? '-')),
                    'pekerjaan' => $w->pekerjaan ?? '-',
                    'alamat' => $w->alamat ?? $alamatKeluarga,
                ];
            }

            // Map data pengajuan untuk Modal Komparasi & Dokumen
            foreach ($riwayatPengajuan as $r) {
                $targetWarga = $r->warga;
                $targetNama = $targetWarga?->nama ?? ($r->data_baru['nama'] ?? 'Anggota Baru');
                $targetHub = $targetWarga ? ucwords(str_replace('_', ' ', $targetWarga->status_hubungan)) : 'Anggota Baru';
                $targetJK = $targetWarga ? ($targetWarga->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : 'Perempuan') : '-';
                $targetTTL = $targetWarga ? (($targetWarga->tempat_lahir ? $targetWarga->tempat_lahir . ', ' : '') . ($targetWarga->tanggal_lahir ? Carbon::parse($targetWarga->tanggal_lahir)->format('d-m-Y') : '')) : '-';

                $statusLabel = match($r->status) {
                    'menunggu' => 'Menunggu Persetujuan RT',
                    'disetujui' => 'Telah Disetujui RT',
                    'ditolak' => 'Ditolak oleh RT',
                    default => ucfirst($r->status),
                };

                $footerText = match($r->status) {
                    'menunggu' => 'Pengajuan ini sedang dalam antrean verifikasi oleh Pengurus RT 04. Mohon menunggu konfirmasi.',
                    'disetujui' => 'Pengajuan telah disetujui oleh RT 04. Data keluarga Anda telah resmi diperbarui.',
                    'ditolak' => 'Pengajuan ditolak oleh Ketua RT 04. Silakan ajukan ulang perubahan berkas.',
                    default => '-',
                };

                // Komparasi field (HANYA menampilkan atribut yang berubah untuk ubah_data)
                $komparasi = [];
                $dataLama = (array) ($r->data_lama ?? []);
                $dataBaru = (array) ($r->data_baru ?? []);
                $allKeys = array_unique(array_merge(array_keys($dataLama), array_keys($dataBaru)));

                foreach ($allKeys as $k) {
                    if (in_array($k, ['id', 'warga_id', 'keluarga_id', 'created_at', 'updated_at'])) continue;
                    $valOld = $dataLama[$k] ?? '-';
                    $valNew = $dataBaru[$k] ?? '-';

                    if ($k === 'pendidikan_terakhir') {
                        $valOld = $this->mapPendidikan[$valOld] ?? $valOld;
                        $valNew = $this->mapPendidikan[$valNew] ?? $valNew;
                    } elseif ($k === 'jenis_kelamin') {
                        $valOld = $valOld === 'laki_laki' ? 'Laki-Laki' : ($valOld === 'perempuan' ? 'Perempuan' : $valOld);
                        $valNew = $valNew === 'laki_laki' ? 'Laki-Laki' : ($valNew === 'perempuan' ? 'Perempuan' : $valNew);
                    } elseif ($k === 'status_warga') {
                        $valOld = $valOld === 'hidup' ? 'Hidup (Aktif)' : ($valOld === 'meninggal' ? 'Meninggal' : $valOld);
                        $valNew = $valNew === 'hidup' ? 'Hidup (Aktif)' : ($valNew === 'meninggal' ? 'Meninggal' : $valNew);
                    } elseif ($k === 'status_hubungan') {
                        $valOld = ucwords(str_replace('_', ' ', $valOld));
                        $valNew = ucwords(str_replace('_', ' ', $valNew));
                    }

                    $isChanged = (trim((string)$valOld) !== trim((string)$valNew));

                    if ($isChanged || $r->jenis_pengajuan === 'tambah_anggota') {
                        $komparasi[] = [
                            'field' => $fieldLabels[$k] ?? ucwords(str_replace('_', ' ', $k)),
                            'old' => $valOld ?: '-',
                            'new' => $valNew ?: '-',
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

                // Berkas Bukti Pendukung (Mendukung Multi-Berkas)
                $docs = [];
                $buktiList = $r->bukti_list;

                foreach ($buktiList as $idx => $bPath) {
                    $docUrl = '';
                    if (str_starts_with($bPath, 'http://') || str_starts_with($bPath, 'https://')) {
                        $docUrl = $bPath;
                    } elseif (file_exists(public_path($bPath))) {
                        $docUrl = asset($bPath);
                    } elseif (file_exists(public_path('storage/' . $bPath))) {
                        $docUrl = asset('storage/' . $bPath);
                    } else {
                        $docUrl = asset($bPath);
                    }

                    $bName = basename($bPath);
                    $isPdf = str_ends_with(strtolower($bName), '.pdf');

                    $docs[] = [
                        'label' => ($idx + 1) . '. ' . $bName,
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
                    'targetTitle' => 'Target Data: ' . $targetNama . ' (' . $targetHub . ')',
                    'targetSub' => 'NIK: ' . ($targetWarga?->nik ?? '-') . ' • ' . $targetJK . ' • Lahir: ' . $targetTTL,
                    'tgl' => 'Diajukan pada: ' . $r->created_at->translatedFormat('d M Y') . ', ' . $r->created_at->format('H:i') . ' WIB',
                    'attrCount' => count($komparasi) . ' Atribut Diperbarui',
                    'komparasi' => $komparasi,
                    'alasan' => $r->alasan ?? 'Pembaruan data kependudukan keluarga.',
                    'catatanRT' => $r->catatan_admin ?? ($r->status === 'ditolak' ? $r->alasan : ''),
                    'footerText' => $footerText,
                    'docs' => $docs,
                ];
            }
        }

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
     * Menampilkan Formulir Pengajuan Perubahan Data Warga
     */
    public function tampilkanFormEdit(Request $request, ?string $nik = null): View
    {
        $user = auth()->user();
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        $anggotaKeluarga = $keluarga 
            ? $keluarga->wargas()->where('status_warga', 'hidup')->urutHierarkiKk()->get() 
            : collect();

        $alamatKeluarga = $keluarga ? $keluarga->alamat : 'RT 04 / RW 08';

        $targetNik = $request->query('nik') 
            ?? $request->query('target') 
            ?? $nik 
            ?? $anggotaKeluarga->first()?->nik;

        $anggotaDataEdit = [];
        foreach ($anggotaKeluarga as $ang) {
            $shdkLabel = ucwords(str_replace('_', ' ', $ang->status_hubungan));
            $pendidikanLabel = $this->mapPendidikan[$ang->pendidikan_terakhir] ?? ucwords(str_replace('_', ' ', $ang->pendidikan_terakhir ?? '-'));

            $words = explode(' ', trim($ang->nama));
            $avatar = strtoupper(substr($words[0] ?? 'W', 0, 1) . substr($words[1] ?? ($words[0] ?? 'G'), 0, 1));

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
        $request->validate([
            'target_anggota' => 'required',
            'new_nama' => 'required|string|max:100',
            'new_tempat_lahir' => 'required|string|max:100',
            'new_tgl_lahir' => 'required|date',
            'alasan_perubahan' => 'required|string',
            'bukti' => 'nullable',
        ]);

        $user = auth()->user();
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        if (!$keluarga) {
            return redirect()->route('warga.keluarga')->with('error', 'Data Kartu Keluarga tidak ditemukan.');
        }

        $targetKey = $request->input('target_anggota');
        $warga = Warga::where('keluarga_id', $keluarga->id)
            ->where(function ($q) use ($targetKey) {
                $q->where('nik', $targetKey)->orWhere('id', $targetKey);
            })
            ->first();

        if (!$warga) {
            return redirect()->route('warga.keluarga')->with('error', 'Warga yang dipilih tidak valid.');
        }

        // Evaluasi perbandingan field lama vs baru secara presisi
        $allPossibleFields = [
            'nama' => [
                'old' => trim((string)$warga->nama),
                'new' => trim((string)$request->input('new_nama', $warga->nama)),
            ],
            'tempat_lahir' => [
                'old' => trim((string)$warga->tempat_lahir),
                'new' => trim((string)$request->input('new_tempat_lahir', $warga->tempat_lahir)),
            ],
            'tanggal_lahir' => [
                'old' => $warga->tanggal_lahir ? Carbon::parse($warga->tanggal_lahir)->format('Y-m-d') : '',
                'new' => $request->input('new_tgl_lahir') ? Carbon::parse($request->input('new_tgl_lahir'))->format('Y-m-d') : '',
            ],
            'jenis_kelamin' => [
                'old' => strtolower(trim((string)$warga->jenis_kelamin)),
                'new' => strtolower(trim((string)$request->input('new_jk', $warga->jenis_kelamin))),
            ],
            'agama' => [
                'old' => strtolower(trim((string)($warga->agama ?? 'islam'))),
                'new' => strtolower(trim((string)$request->input('new_agama', $warga->agama ?? 'islam'))),
            ],
            'pendidikan_terakhir' => [
                'old' => strtolower(trim((string)$warga->pendidikan_terakhir)),
                'new' => strtolower(trim((string)$request->input('new_pendidikan', $warga->pendidikan_terakhir))),
            ],
            'pekerjaan' => [
                'old' => trim((string)($warga->pekerjaan ?? '-')),
                'new' => trim((string)$request->input('new_pekerjaan', $warga->pekerjaan ?? '-')),
            ],
            'alamat' => [
                'old' => trim((string)($warga->alamat ?? $keluarga->alamat ?? '-')),
                'new' => trim((string)$request->input('new_alamat', $warga->alamat ?? $keluarga->alamat ?? '-')),
            ],
            'status_hubungan' => [
                'old' => strtolower(trim((string)$warga->status_hubungan)),
                'new' => strtolower(trim((string)$request->input('new_shdk', $warga->status_hubungan))),
            ],
            'status_warga' => [
                'old' => strtolower(trim((string)$warga->status_warga)),
                'new' => strtolower(trim((string)$request->input('new_status_warga', $warga->status_warga))),
            ],
        ];

        // HANYA ambil field yang benar-benar mengalami perbedaan
        $dataLama = [];
        $dataBaru = [];
        foreach ($allPossibleFields as $k => $f) {
            if ($f['old'] !== $f['new'] && $f['new'] !== '') {
                $dataLama[$k] = $f['old'];
                $dataBaru[$k] = $f['new'];
            }
        }

        // Jika tidak ada perbedaan sama sekali yang terdeteksi, fallback simpan field nama/pekerjaan
        if (empty($dataBaru)) {
            $dataLama['pekerjaan'] = $allPossibleFields['pekerjaan']['old'];
            $dataBaru['pekerjaan'] = $allPossibleFields['pekerjaan']['new'];
        }

        $buktiPath = $this->uploadBerkasBukti($request);

        Riwayat_Perubahan::create([
            'keluarga_id' => $keluarga->id,
            'warga_id' => $warga->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'bukti_path' => $buktiPath,
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
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        $alamatKeluarga = $keluarga ? $keluarga->alamat : 'RT 04 / RW 08';

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
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        if (!$keluarga) {
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
            'alasan' => 'Pendaftaran anggota keluarga baru (' . $request->input('nama_lengkap') . ')',
        ]);

        return redirect()->route('warga.keluarga')
            ->with('success', 'Pendaftaran anggota keluarga baru berhasil diajukan dan menunggu verifikasi Pengurus RT 04.');
    }

    /**
     * Helper unggah berkas pendukung (Mendukung berkas tunggal maupun multi-berkas)
     */
    private function uploadBerkasBukti(Request $request): string
    {
        $buktiPaths = [];
        if ($request->hasFile('bukti')) {
            $files = $request->file('bukti');
            if (! is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $ext = $file->getClientOriginalExtension();
                    $filename = 'bukti_' . time() . '_' . uniqid() . '.' . $ext;
                    $file->move(public_path('uploads/bukti'), $filename);
                    $buktiPaths[] = 'uploads/bukti/' . $filename;
                }
            }
        }

        if (empty($buktiPaths)) {
            return '';
        }

        return count($buktiPaths) === 1 ? $buktiPaths[0] : json_encode($buktiPaths);
    }
}
