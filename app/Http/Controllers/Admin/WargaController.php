<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\Warga;
use App\Services\KependudukanService;
use App\Services\PengajuanPerubahanService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

/**
 * Controller untuk Mengelola Data Warga & Verifikasi Perubahan Data di Area Admin RT
 */
class WargaController extends Controller
{
    /**
     * Dependency Injection Service Layer
     */
    public function __construct(
        protected KependudukanService $kependudukanService,
        protected PengajuanPerubahanService $pengajuanService
    ) {}

    /**
     * Menampilkan daftar Kartu Keluarga & tab verifikasi perubahan data kependudukan (Layar 2.2.7)
     */
    public function tampilkanDaftarWargaDanVerifikasi(Request $request): View
    {
        // 1. Statistik Kependudukan via Service
        $statistik = $this->kependudukanService->hitungStatistikKependudukan();

        // 2. Query Daftar Kartu Keluarga (Tab 1)
        $keluargas = $this->kependudukanService->getKeluargaQuery($request)->latest()->get();

        // 3. Query Pengajuan Verifikasi (Tab 2) - Tidak menampilkan pengajuan yang telah dibatalkan oleh warga
        $pengajuanQuery = Riwayat_Perubahan::where('status', '!=', 'dibatalkan')->with([
            'keluarga.kepalaKeluarga' => fn ($q) => $q->where('status_warga', 'hidup'),
            'warga',
            'admin',
        ]);

        if ($request->filled('status_verifikasi')) {
            $pengajuanQuery->where('status', $request->input('status_verifikasi'));
        }

        $daftarPengajuan = $pengajuanQuery->latest()->paginate(10, ['*'], 'verifikasi_page')->withQueryString();

        return view('admin.data-warga', array_merge($statistik, [
            'keluargas' => $keluargas,
            'daftarPengajuan' => $daftarPengajuan,
        ]));
    }

    /**
     * Menampilkan halaman detail satu Kartu Keluarga (Layar 2.2.8)
     */
    public function tampilkanDetailKeluarga(string $no_kk): View
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();

        $semuaAnggota = $keluarga->wargas()
            ->with('user')
            ->urutHierarkiKk()
            ->get();

        $anggotaHidup = $semuaAnggota->where('status_warga', 'hidup');

        $totalJiwaAktif = $anggotaHidup->count();
        $totalLakiKk = $anggotaHidup->where('jenis_kelamin', 'laki_laki')->count();
        $totalPerempuanKk = $anggotaHidup->where('jenis_kelamin', 'perempuan')->count();

        $kepalaKeluarga = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga') 
            ?? $semuaAnggota->firstWhere('status_hubungan', 'kepala_keluarga') 
            ?? $keluarga->kepalaKeluarga;
        $alamatKeluarga = $keluarga->alamat;

        $akunKk = $this->kependudukanService->ambilAkunKeluarga($keluarga);
        $noTelpKeluarga = $akunKk?->no_telp ?? '-';

        $terakhirDiupdate = collect([$keluarga->updated_at, $semuaAnggota->max('updated_at')])
            ->filter()
            ->max();

        return view('admin.detail-keluarga', compact(
            'keluarga',
            'semuaAnggota',
            'anggotaHidup',
            'totalJiwaAktif',
            'totalLakiKk',
            'totalPerempuanKk',
            'kepalaKeluarga',
            'alamatKeluarga',
            'akunKk',
            'noTelpKeluarga',
            'terakhirDiupdate'
        ));
    }

    /**
     * Menampilkan form tambah anggota keluarga baru oleh Admin RT (Layar 2.2.12)
     */
    public function tampilkanFormTambahAnggota(Request $request, ?string $no_kk = null): View|RedirectResponse
    {
        $targetKk = $no_kk ?? $request->query('no_kk');

        if ($targetKk) {
            $keluarga = Keluarga::where('no_kk', $targetKk)->firstOrFail();
        } else {
            $keluarga = Keluarga::latest()->first();
            if (! $keluarga) {
                return redirect()->route('admin.data-warga')->with('error', 'Belum ada Kartu Keluarga terdaftar di sistem.');
            }
        }

        $anggotaHidup = $keluarga->wargas()->where('status_warga', 'hidup')->get();
        $kepalaKeluarga = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga') ?? $keluarga->kepalaKeluarga;
        $alamatKeluarga = $keluarga->alamat;
        $totalJiwa = $anggotaHidup->count();

        return view('admin.warga-tambah-anggota', compact(
            'keluarga',
            'kepalaKeluarga',
            'alamatKeluarga',
            'totalJiwa'
        ));
    }

    /**
     * Menyimpan data anggota keluarga baru oleh Admin RT (BR-14 & BR-08)
     */
    public function simpanTambahAnggota(Request $request, ?string $no_kk = null): RedirectResponse
    {
        $targetNoKk = $no_kk ?? $request->input('no_kk');
        $keluarga = Keluarga::where('no_kk', $targetNoKk)->firstOrFail();

        $validated = $request->validate([
            'nik' => ['required', 'numeric', 'digits:16', 'regex:/^[0-9]{16}$/'],
            'nama' => ['required', 'string', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jk' => ['nullable', 'string'],
            'jenis_kelamin' => ['nullable', 'string'],
            'agama' => ['nullable', 'string'],
            'pendidikan' => ['nullable', 'string'],
            'pendidikan_terakhir' => ['nullable', 'string'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
            'shdk' => ['nullable', 'string'],
            'status_hubungan' => ['nullable', 'string'],
        ], [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.numeric'  => 'NIK hanya boleh berisi karakter angka (0-9) tanpa huruf atau simbol.',
            'nik.digits'   => 'NIK harus tepat terdiri dari 16 digit angka.',
            'nik.regex'    => 'NIK harus tepat terdiri dari 16 digit angka numerik.',
            'nama.required' => 'Nama lengkap warga wajib diisi.',
        ]);

        try {
            $inputData = array_merge($validated, [
                'jenis_kelamin' => $request->input('jenis_kelamin') ?? $request->input('jk'),
                'status_hubungan' => $request->input('status_hubungan') ?? $request->input('shdk'),
                'pendidikan_terakhir' => $request->input('pendidikan_terakhir') ?? $request->input('pendidikan'),
            ]);

            $warga = $this->kependudukanService->tambahAnggotaOlehAdmin($keluarga, $inputData);

            return redirect()->route('admin.data-warga.detail', $keluarga->no_kk)
                ->with('success', "Anggota keluarga ({$warga->nama}) berhasil ditambahkan ke KK No. {$keluarga->no_kk}.");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit data warga oleh Admin RT (Layar 2.2.10)
     */
    public function tampilkanFormEditAnggota(Request $request, string $nik): View
    {
        $warga = Warga::with('keluarga')->where('nik', $nik)->firstOrFail();
        $keluarga = $warga->keluarga;

        $anggotaHidup = $keluarga->wargas()->where('status_warga', 'hidup')->get();
        $kepalaKeluarga = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga') ?? $keluarga->kepalaKeluarga;
        $alamatKeluarga = $keluarga->alamat;

        return view('admin.warga-edit-anggota', compact(
            'warga',
            'keluarga',
            'kepalaKeluarga',
            'alamatKeluarga',
            'nik'
        ));
    }

    /**
     * Memperbarui data master warga oleh Admin RT (Layar 2.2.10, BR-02 & BR-03)
     */
    public function updateDataAnggota(Request $request, ?string $nik = null): RedirectResponse
    {
        $targetNik = $nik ?? $request->input('nik');
        $warga = Warga::where('nik', $targetNik)->firstOrFail();

        $validated = $request->validate([
            'nik' => ['nullable', 'numeric', 'digits:16', 'regex:/^[0-9]{16}$/'],
            'nama' => ['required', 'string', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jk' => ['nullable', 'string'],
            'jenis_kelamin' => ['nullable', 'string'],
            'agama' => ['nullable', 'string'],
            'pendidikan' => ['nullable', 'string'],
            'pendidikan_terakhir' => ['nullable', 'string'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
            'shdk' => ['nullable', 'string'],
            'status_hubungan' => ['nullable', 'string'],
            'status_warga' => ['nullable', 'string'],
            'status_keberadaan' => ['nullable', 'string'],
        ], [
            'nik.numeric'  => 'NIK hanya boleh berisi karakter angka (0-9) tanpa huruf atau simbol.',
            'nik.digits'   => 'NIK harus tepat terdiri dari 16 digit angka.',
            'nik.regex'    => 'NIK harus tepat terdiri dari 16 digit angka numerik.',
            'nama.required' => 'Nama lengkap warga wajib diisi.',
        ]);

        try {
            $inputData = array_merge($validated, [
                'nik' => $request->filled('nik') ? trim($request->input('nik')) : null,
                'jenis_kelamin' => $request->input('jenis_kelamin') ?? $request->input('jk'),
                'status_hubungan' => $request->input('status_hubungan') ?? $request->input('shdk'),
                'pendidikan_terakhir' => $request->input('pendidikan_terakhir') ?? $request->input('pendidikan'),
                'status_warga' => $request->input('status_warga') ?? $request->input('status_keberadaan'),
            ]);

            $this->kependudukanService->koreksiDataWarga($warga, $inputData);

            $noKk = $warga->keluarga?->no_kk;
            if ($noKk) {
                return redirect()->route('admin.data-warga.detail', $noKk)
                    ->with('success', "Pembaruan data warga ({$warga->nama}) berhasil disimpan.");
            }

            return redirect()->route('admin.data-warga')
                ->with('success', "Pembaruan data warga ({$warga->nama}) berhasil disimpan.");
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Memperbarui status ekonomi dan status tinggal satu Kartu Keluarga
     */
    public function ubahStatusKeluarga(Request $request, string $no_kk): RedirectResponse
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();

        $validated = $request->validate([
            'status_tinggal' => ['required', Rule::in(['tetap', 'kontrak', 'kos', 'menumpang'])],
            'status_ekonomi' => ['required', Rule::in(['mampu', 'kurang_mampu'])],
        ]);

        $this->kependudukanService->ubahStatusKeluarga(
            $keluarga,
            $validated['status_tinggal'],
            $validated['status_ekonomi']
        );

        return redirect()->back()->with('success', "Status Kartu Keluarga No. {$no_kk} berhasil diperbarui.");
    }

    /**
     * Me-reset password akun keluarga menjadi NIK Kepala Keluarga / Username Akun (BR-04)
     */
    public function resetPasswordKeluarga(Request $request, string $no_kk): RedirectResponse
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();

        try {
            $akun = $this->kependudukanService->resetPasswordKeluarga($keluarga);

            return redirect()->back()->with('success', "Kata sandi akun warga ({$akun->username}) berhasil di-reset. Warga dapat login kembali menggunakan NIK.");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menghapus anggota keluarga (Layar 2.2.8 & BR-02/BR-03)
     */
    public function hapusAnggota(Request $request, string $nik): RedirectResponse
    {
        $warga = Warga::where('nik', $nik)->firstOrFail();
        $nama = $warga->nama;

        try {
            $this->kependudukanService->hapusAnggota($warga);

            return redirect()->back()->with('success', "Anggota keluarga {$nama} (NIK: {$nik}) berhasil dihapus dari Kartu Keluarga.");
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menghapus seluruh data Kartu Keluarga beserta anggota
     */
    public function hapusKeluarga(Request $request, string $no_kk): RedirectResponse
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();

        $this->kependudukanService->hapusKeluarga($keluarga);

        return redirect()->route('admin.data-warga')->with('success', "Kartu Keluarga No. {$no_kk} beserta seluruh data anggotanya berhasil dihapus.");
    }

    /**
     * Menyetujui atau menolak permohonan perubahan data kependudukan (Layar 2.2.7 & X6, UC-11, BR-08)
     */
    public function verifikasiPengajuan(Request $request, int $id): RedirectResponse
    {
        $pengajuan = Riwayat_Perubahan::with('keluarga')->findOrFail($id);

        $validated = $request->validate([
            'tindakan' => ['required', Rule::in(['setujui', 'tolak'])],
            'alasan' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->pengajuanService->verifikasiPengajuan(
                $pengajuan,
                $validated['tindakan'],
                $validated['alasan'] ?? null,
                auth()->id()
            );

            $pesan = $validated['tindakan'] === 'setujui'
                ? 'Pengajuan perubahan data kependudukan berhasil disetujui dan data sistem telah diperbarui.'
                : 'Pengajuan perubahan data kependudukan telah ditolak.';

            return redirect()->back()->with('success', $pesan);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}