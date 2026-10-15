<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    /**
     * Menampilkan daftar Kartu Keluarga & tab verifikasi perubahan data kependudukan (Layar 2.2.7)
     */
    public function tampilkanDaftarWargaDanVerifikasi(Request $request)
    {
        // 1. Statistik Kependudukan
        $totalKkAktif = Keluarga::whereHas('wargas', function ($q) {
            $q->where('status_warga', 'hidup');
        })->count();

        $totalWargaHidup = Warga::where('status_warga', 'hidup')->count();
        $totalLakiLaki = Warga::where('status_warga', 'hidup')->where('jenis_kelamin', 'laki_laki')->count();
        $totalPerempuan = Warga::where('status_warga', 'hidup')->where('jenis_kelamin', 'perempuan')->count();
        $totalMenungguVerifikasi = Riwayat_Perubahan::where('status', 'menunggu')->count();

        // 2. Query Daftar Kartu Keluarga (Tab 1)
        $keluargaQuery = Keluarga::with([
            'kepalaKeluarga' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
            'wargas' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
        ])->withCount([
            'wargas as jumlah_anggota_hidup' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
        ]);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $keluargaQuery->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                    ->orWhereHas('wargas', function ($wq) use ($search) {
                        $wq->where('status_warga', 'hidup')
                            ->where(function ($sub) use ($search) {
                                $sub->where('nama', 'like', "%{$search}%")
                                    ->orWhere('alamat', 'like', "%{$search}%")
                                    ->orWhere('nik', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter Status Tinggal
        if ($request->filled('status_tinggal')) {
            $keluargaQuery->where('status_tinggal', $request->input('status_tinggal'));
        }

        // Filter Status Ekonomi
        if ($request->filled('status_ekonomi')) {
            $keluargaQuery->where('status_ekonomi', $request->input('status_ekonomi'));
        }

        $keluargas = $keluargaQuery->latest()->paginate(10)->withQueryString();

        // 3. Query Pengajuan Verifikasi (Tab 2)
        $pengajuanQuery = Riwayat_Perubahan::with([
            'keluarga.kepalaKeluarga' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
            'warga',
            'admin',
        ]);

        if ($request->filled('status_verifikasi')) {
            $pengajuanQuery->where('status', $request->input('status_verifikasi'));
        }

        $daftarPengajuan = $pengajuanQuery->latest()->paginate(10, ['*'], 'verifikasi_page')->withQueryString();

        return view('admin.data-warga', compact(
            'totalKkAktif',
            'totalWargaHidup',
            'totalLakiLaki',
            'totalPerempuan',
            'totalMenungguVerifikasi',
            'keluargas',
            'daftarPengajuan'
        ));
    }

    /**
     * Menampilkan halaman detail satu Kartu Keluarga (Layar 2.2.8)
     */
    public function tampilkanDetailKeluarga(string $no_kk)
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();

        $anggotaHidup = $keluarga->wargas()
            ->where('status_warga', 'hidup')
            ->with('user')
            ->orderByRaw("FIELD(status_hubungan, 'kepala_keluarga', 'suami', 'istri', 'anak', 'menantu', 'cucu', 'orang_tua', 'mertua', 'famili_lain', 'lainnya')")
            ->orderBy('tanggal_lahir', 'asc')
            ->get();

        $totalJiwaAktif = $anggotaHidup->count();
        $totalLakiKk = $anggotaHidup->where('jenis_kelamin', 'laki_laki')->count();
        $totalPerempuanKk = $anggotaHidup->where('jenis_kelamin', 'perempuan')->count();

        $kepalaKeluarga = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga') ?? $keluarga->kepalaKeluarga;
        $alamatKeluarga = ($kepalaKeluarga && ! empty($kepalaKeluarga->alamat))
            ? $kepalaKeluarga->alamat
            : ($anggotaHidup->first(fn ($w) => ! empty($w->alamat))?->alamat ?? 'RT 04 / RW 08');

        $akunKk = $this->ambilAkunKeluarga($keluarga);
        $noTelpKeluarga = $akunKk?->no_telp ?? '-';

        $terakhirDiupdate = collect([$keluarga->updated_at, $anggotaHidup->max('updated_at')])
            ->filter()
            ->max();

        return view('admin.detail-keluarga', compact(
            'keluarga',
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
    public function tampilkanFormTambahAnggota(Request $request, ?string $no_kk = null)
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
        $alamatKeluarga = ($kepalaKeluarga && ! empty($kepalaKeluarga->alamat))
            ? $kepalaKeluarga->alamat
            : ($anggotaHidup->first(fn ($w) => ! empty($w->alamat))?->alamat ?? 'RT 04 / RW 08');
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
    public function simpanTambahAnggota(Request $request, ?string $no_kk = null)
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

        $nik = trim($validated['nik']);
        $jenisKelamin = $this->normalisasiJenisKelamin($request->input('jenis_kelamin') ?? $request->input('jk'));
        $statusHubungan = $this->normalisasiStatusHubungan($request->input('status_hubungan') ?? $request->input('shdk'));
        $agama = $this->normalisasiAgama($request->input('agama'));
        $pendidikan = $this->normalisasiPendidikan($request->input('pendidikan_terakhir') ?? $request->input('pendidikan'));

        // Cek validasi NIK sesuai BR-14
        $existingWarga = Warga::where('nik', $nik)->first();

        // Akun KK tujuan
        $akunKk = $this->ambilAkunKeluarga($keluarga);
        $userId = $akunKk?->id;

        $alamatFinal = $validated['alamat'] ?? null;
        if (empty($alamatFinal)) {
            $kepala = $keluarga->wargas()->where('status_hubungan', 'kepala_keluarga')->where('status_warga', 'hidup')->first();
            $alamatFinal = $kepala?->alamat ?? 'RT 04 / RW 08';
        }

        if ($existingWarga) {
            // Rule 4: NIK sudah ada pada KK yang sama
            if ($existingWarga->keluarga_id == $keluarga->id && $existingWarga->status_warga === 'hidup') {
                return redirect()->back()->withInput()->with('error', 'NIK sudah terdaftar pada Kartu Keluarga ini.');
            }

            // Rule 2: NIK ada dengan status_warga = pindah_kk -> jalankan BR-08 (tarik data lama, KK baru, status hidup)
            if ($existingWarga->status_warga === 'pindah_kk') {
                $existingWarga->update([
                    'keluarga_id' => $keluarga->id,
                    'user_id' => $userId,
                    'nama' => $validated['nama'],
                    'tempat_lahir' => $validated['tempat_lahir'] ?? $existingWarga->tempat_lahir,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? $existingWarga->tanggal_lahir,
                    'jenis_kelamin' => $jenisKelamin ?? $existingWarga->jenis_kelamin,
                    'agama' => $agama ?? $existingWarga->agama,
                    'pendidikan_terakhir' => $pendidikan ?? $existingWarga->pendidikan_terakhir,
                    'pekerjaan' => $validated['pekerjaan'] ?? $existingWarga->pekerjaan,
                    'alamat' => $alamatFinal,
                    'status_hubungan' => $statusHubungan ?? $existingWarga->status_hubungan,
                    'status_warga' => 'hidup',
                ]);

                return redirect()->route('admin.data-warga.detail', $keluarga->no_kk)
                    ->with('success', "Warga berstatus pindah KK ({$existingWarga->nama}) berhasil didaftarkan ke KK No. {$keluarga->no_kk}.");
            }

            // Rule 3: NIK ada pada KK lain dengan status selain pindah_kk
            return redirect()->back()->withInput()->with('error', 'NIK sudah terdaftar dalam Kartu Keluarga lain.');
        }

        // Rule 1: NIK belum ada di tabel warga -> buat record baru
        Warga::create([
            'keluarga_id' => $keluarga->id,
            'user_id' => $userId,
            'nik' => $nik,
            'nama' => $validated['nama'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $jenisKelamin ?? 'laki_laki',
            'agama' => $agama,
            'pendidikan_terakhir' => $pendidikan,
            'pekerjaan' => $validated['pekerjaan'] ?? null,
            'alamat' => $alamatFinal,
            'status_hubungan' => $statusHubungan ?? 'anak',
            'status_warga' => 'hidup',
        ]);

        return redirect()->route('admin.data-warga.detail', $keluarga->no_kk)
            ->with('success', "Anggota keluarga baru ({$validated['nama']}) berhasil ditambahkan ke KK No. {$keluarga->no_kk}.");
    }

    /**
     * Menampilkan form edit data warga oleh Admin RT (Layar 2.2.10)
     */
    public function tampilkanFormEditAnggota(Request $request, string $nik)
    {
        $warga = Warga::with('keluarga')->where('nik', $nik)->firstOrFail();
        $keluarga = $warga->keluarga;

        $anggotaHidup = $keluarga->wargas()->where('status_warga', 'hidup')->get();
        $kepalaKeluarga = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga') ?? $keluarga->kepalaKeluarga;
        $alamatKeluarga = ($kepalaKeluarga && ! empty($kepalaKeluarga->alamat))
            ? $kepalaKeluarga->alamat
            : ($anggotaHidup->first(fn ($w) => ! empty($w->alamat))?->alamat ?? 'RT 04 / RW 08');

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
    public function updateDataAnggota(Request $request, ?string $nik = null)
    {
        $targetNik = $nik ?? $request->input('nik');
        $warga = Warga::where('nik', $targetNik)->firstOrFail();
        $statusLama = $warga->status_warga;

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

        $jenisKelamin = $this->normalisasiJenisKelamin($request->input('jenis_kelamin') ?? $request->input('jk')) ?? $warga->jenis_kelamin;
        $statusHubungan = $this->normalisasiStatusHubungan($request->input('status_hubungan') ?? $request->input('shdk')) ?? $warga->status_hubungan;
        $agama = $this->normalisasiAgama($request->input('agama')) ?? $warga->agama;
        $pendidikan = $this->normalisasiPendidikan($request->input('pendidikan_terakhir') ?? $request->input('pendidikan')) ?? $warga->pendidikan_terakhir;
        $statusBaru = $this->normalisasiStatusWarga($request->input('status_warga') ?? $request->input('status_keberadaan')) ?? $warga->status_warga;

        $updateData = [
            'nama' => $validated['nama'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? $warga->tempat_lahir,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? $warga->tanggal_lahir,
            'jenis_kelamin' => $jenisKelamin,
            'agama' => $agama,
            'pendidikan_terakhir' => $pendidikan,
            'pekerjaan' => $validated['pekerjaan'] ?? $warga->pekerjaan,
            'alamat' => $validated['alamat'] ?? $warga->alamat,
            'status_hubungan' => $statusHubungan,
            'status_warga' => $statusBaru,
        ];

        // Validasi dan perbarui NIK jika diubah oleh Admin RT
        $newNik = $request->filled('nik') ? trim($request->input('nik')) : null;
        if (! empty($newNik) && $newNik !== $warga->nik) {
            $nikSudahAda = Warga::where('nik', $newNik)->where('id', '!=', $warga->id)->exists();
            if ($nikSudahAda) {
                return back()->withInput()->with('error', "NIK {$newNik} sudah digunakan oleh warga lain.");
            }
            $updateData['nik'] = $newNik;

            // Jika akun login warga ini terikat dengan NIK lama (username = NIK lama), update usernamenya
            if ($warga->user && $warga->user->username === $warga->nik) {
                $warga->user->update(['username' => $newNik]);
            }
        }

        $warga->update($updateData);

        // BR-02: Jika status_warga berubah dari 'hidup' ke 'pindah_kk', 'meninggal', atau 'pindah_rumah'
        if ($statusLama === 'hidup' && in_array($statusBaru, ['pindah_kk', 'meninggal', 'pindah_rumah'])) {
            $this->prosesTransferAkunOtomatis($warga);
        }

        $noKk = $warga->keluarga?->no_kk;
        if ($noKk) {
            return redirect()->route('admin.data-warga.detail', $noKk)
                ->with('success', "Pembaruan data warga ({$warga->nama}) berhasil disimpan.");
        }

        return redirect()->route('admin.data-warga')
            ->with('success', "Pembaruan data warga ({$warga->nama}) berhasil disimpan.");
    }

    /**
     * Memperbarui status ekonomi dan status tinggal satu Kartu Keluarga
     */
    public function ubahStatusKeluarga(Request $request, string $no_kk)
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();

        $validated = $request->validate([
            'status_tinggal' => ['required', Rule::in(['tetap', 'kontrak', 'kos', 'menumpang'])],
            'status_ekonomi' => ['required', Rule::in(['mampu', 'kurang_mampu'])],
        ]);

        $keluarga->update($validated);

        return redirect()->back()->with('success', "Status Kartu Keluarga No. {$no_kk} berhasil diperbarui.");
    }

    /**
     * Me-reset password akun keluarga menjadi NIK Kepala Keluarga / Username Akun (BR-04)
     */
    public function resetPasswordKeluarga(Request $request, string $no_kk)
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();
        $akun = $this->ambilAkunKeluarga($keluarga);

        if (! $akun) {
            return redirect()->back()->with('error', 'Akun login untuk Kartu Keluarga ini belum ditemukan.');
        }

        // Reset password ke username (NIK) default sesuai BR-04
        $akun->update([
            'password' => Hash::make($akun->username),
        ]);

        return redirect()->back()->with('success', "Kata sandi akun warga ({$akun->username}) berhasil di-reset. Warga dapat login kembali menggunakan NIK.");
    }

    /**
     * Menghapus anggota keluarga (Layar 2.2.8 & BR-02/BR-03)
     */
    public function hapusAnggota(Request $request, string $nik)
    {
        $warga = Warga::where('nik', $nik)->firstOrFail();
        $nama = $warga->nama;
        $noKk = $warga->keluarga?->no_kk;

        // Cegah menghapus jika kepala keluarga masih memiliki anggota lain yang hidup
        if ($warga->status_hubungan === 'kepala_keluarga') {
            $anggotaLain = Warga::where('keluarga_id', $warga->keluarga_id)
                ->where('id', '!=', $warga->id)
                ->where('status_warga', 'hidup')
                ->count();

            if ($anggotaLain > 0) {
                return redirect()->back()->with('error', "Tidak dapat menghapus Kepala Keluarga ({$nama}) karena masih ada {$anggotaLain} anggota keluarga lainnya. Ubah status hubungan kepala keluarga terlebih dahulu.");
            }
        }

        // Jalankan transfer akun jika yang dihapus adalah pemilik akun
        $this->prosesTransferAkunOtomatis($warga);

        $warga->delete();

        return redirect()->back()->with('success', "Anggota keluarga {$nama} (NIK: {$nik}) berhasil dihapus dari Kartu Keluarga.");
    }

    /**
     * Menghapus seluruh data Kartu Keluarga beserta anggota
     */
    public function hapusKeluarga(Request $request, string $no_kk)
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();
        $akun = $this->ambilAkunKeluarga($keluarga);

        // Hapus akun warga terkait jika ada
        if ($akun && $akun->role !== 'admin') {
            $akun->delete();
        }

        $keluarga->delete();

        return redirect()->route('admin.data-warga')->with('success', "Kartu Keluarga No. {$no_kk} beserta seluruh data anggotanya berhasil dihapus.");
    }

    /**
     * Menyetujui atau menolak permohonan perubahan data kependudukan (Layar 2.2.7 & X6, UC-11, BR-08)
     */
    public function verifikasiPengajuan(Request $request, int $id)
    {
        $pengajuan = Riwayat_Perubahan::with('keluarga')->findOrFail($id);

        $validated = $request->validate([
            'tindakan' => ['required', Rule::in(['setujui', 'tolak'])],
            'alasan' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['tindakan'] === 'setujui') {
            DB::transaction(function () use ($pengajuan, $validated) {
                // Skenario A: Ubah Data Warga Eksisting
                if ($pengajuan->jenis_pengajuan === 'ubah_data' || $pengajuan->warga_id) {
                    $warga = Warga::find($pengajuan->warga_id);
                    if ($warga && ! empty($pengajuan->data_baru)) {
                        $statusLama = $warga->status_warga;

                        $fillableData = collect($pengajuan->data_baru)
                            ->only([
                                'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
                                'agama', 'pendidikan_terakhir', 'pekerjaan', 'alamat',
                                'status_hubungan', 'status_warga',
                            ])->toArray();

                        // Normalisasi enum jika perlu
                        if (isset($fillableData['jenis_kelamin'])) {
                            $fillableData['jenis_kelamin'] = $this->normalisasiJenisKelamin($fillableData['jenis_kelamin']);
                        }
                        if (isset($fillableData['status_hubungan'])) {
                            $fillableData['status_hubungan'] = $this->normalisasiStatusHubungan($fillableData['status_hubungan']);
                        }
                        if (isset($fillableData['agama'])) {
                            $fillableData['agama'] = $this->normalisasiAgama($fillableData['agama']);
                        }
                        if (isset($fillableData['pendidikan_terakhir'])) {
                            $fillableData['pendidikan_terakhir'] = $this->normalisasiPendidikan($fillableData['pendidikan_terakhir']);
                        }
                        if (isset($fillableData['status_warga'])) {
                            $fillableData['status_warga'] = $this->normalisasiStatusWarga($fillableData['status_warga']);
                        }

                        $warga->update($fillableData);

                        // Trigger BR-02 jika status_warga berubah keluar dari hidup
                        if ($statusLama === 'hidup' && in_array($warga->status_warga, ['pindah_kk', 'meninggal', 'pindah_rumah'])) {
                            $this->prosesTransferAkunOtomatis($warga);
                        }
                    }
                }
                // Skenario B: Tambah Anggota Keluarga Baru (BR-08)
                elseif ($pengajuan->jenis_pengajuan === 'tambah_anggota' && ! empty($pengajuan->data_baru)) {
                    $keluarga = $pengajuan->keluarga ?? Keluarga::find($pengajuan->keluarga_id);
                    $akunKk = $keluarga ? $this->ambilAkunKeluarga($keluarga) : null;
                    $dataBaru = $pengajuan->data_baru;

                    $nikBaru = $dataBaru['nik'] ?? null;
                    if ($nikBaru) {
                        $existing = Warga::where('nik', $nikBaru)->first();
                        if ($existing && $existing->status_warga === 'pindah_kk') {
                            // Pindahkan warga pindah_kk ke KK baru
                            $existing->update([
                                'keluarga_id' => $keluarga->id,
                                'user_id' => $akunKk?->id,
                                'nama' => $dataBaru['nama'] ?? $existing->nama,
                                'status_warga' => 'hidup',
                            ]);
                        } elseif (! $existing) {
                            Warga::create([
                                'keluarga_id' => $pengajuan->keluarga_id,
                                'user_id' => $akunKk?->id,
                                'nik' => $nikBaru,
                                'nama' => $dataBaru['nama'] ?? 'Anggota Baru',
                                'tempat_lahir' => $dataBaru['tempat_lahir'] ?? null,
                                'tanggal_lahir' => $dataBaru['tanggal_lahir'] ?? null,
                                'jenis_kelamin' => $this->normalisasiJenisKelamin($dataBaru['jenis_kelamin'] ?? null) ?? 'laki_laki',
                                'agama' => $this->normalisasiAgama($dataBaru['agama'] ?? null),
                                'pendidikan_terakhir' => $this->normalisasiPendidikan($dataBaru['pendidikan_terakhir'] ?? null),
                                'pekerjaan' => $dataBaru['pekerjaan'] ?? null,
                                'alamat' => $dataBaru['alamat'] ?? ($keluarga?->kepalaKeluarga?->alamat ?? 'RT 04 / RW 08'),
                                'status_hubungan' => $this->normalisasiStatusHubungan($dataBaru['status_hubungan'] ?? null) ?? 'anak',
                                'status_warga' => 'hidup',
                            ]);
                        }
                    }
                }

                $pengajuan->update([
                    'status' => 'disetujui',
                    'admin_id' => auth()->id(),
                    'alasan' => $validated['alasan'] ?? 'Disetujui oleh Pengurus RT.',
                ]);
            });

            $pesan = 'Pengajuan perubahan data kependudukan berhasil disetujui dan data sistem telah diperbarui.';
        } else {
            $pengajuan->update([
                'status' => 'ditolak',
                'admin_id' => auth()->id(),
                'alasan' => $validated['alasan'] ?? 'Pengajuan ditolak oleh Pengurus RT.',
            ]);

            $pesan = 'Pengajuan perubahan data kependudukan telah ditolak.';
        }

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * Business Rule BR-02 (Transfer Akun Otomatis) & BR-03 (Pembersihan Akun)
     */
    protected function prosesTransferAkunOtomatis(Warga $warga): void
    {
        $akunLama = $warga->user;
        $keluargaId = $warga->keluarga_id;

        // 1. Set warga.user_id = null
        $warga->update(['user_id' => null]);

        if (! $akunLama) {
            return;
        }

        // 2. Cek apakah warga ini adalah pemilik akun (username == nik)
        if ($akunLama->username !== $warga->nik) {
            // Bukan pemilik akun, akun KK tetap aktif untuk pemilik saat ini
            return;
        }

        // 3. Cek apakah masih ada anggota berstatus 'hidup' pada keluarga yang sama
        $anggotaHidup = Warga::where('keluarga_id', $keluargaId)
            ->where('status_warga', 'hidup')
            ->where('id', '!=', $warga->id)
            ->get();

        if ($anggotaHidup->isEmpty()) {
            // BR-03: Pembersihan Akun. Seluruh anggota KK habis -> hapus akun users
            $akunLama->delete();
            return;
        }

        // Urutan pemilihan pemilik baru:
        // a. Kepala Keluarga
        $pemilikBaru = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga');

        // b. Istri / Suami
        if (! $pemilikBaru) {
            $pemilikBaru = $anggotaHidup->first(fn ($w) => in_array($w->status_hubungan, ['istri', 'suami']));
        }

        // c. Anak Tertua usia 17+
        if (! $pemilikBaru) {
            $pemilikBaru = $anggotaHidup
                ->filter(function ($w) {
                    if ($w->status_hubungan !== 'anak' || empty($w->tanggal_lahir)) {
                        return false;
                    }
                    return Carbon::parse($w->tanggal_lahir)->age >= 17;
                })
                ->sortBy('tanggal_lahir')
                ->first();
        }

        // d. Anggota Tertua
        if (! $pemilikBaru) {
            $pemilikBaru = $anggotaHidup->sortBy('tanggal_lahir')->first();
        }

        if ($pemilikBaru) {
            // Perbarui username ke NIK pemilik baru, password tetap
            $akunLama->update([
                'username' => $pemilikBaru->nik,
            ]);

            // Pastikan user_id mengarah ke akun KK
            $pemilikBaru->update([
                'user_id' => $akunLama->id,
            ]);
        }
    }

    /**
     * Helper mengambil Akun User untuk Keluarga
     */
    protected function ambilAkunKeluarga(Keluarga $keluarga): ?User
    {
        // 1. Cek user_id dari kepala keluarga
        if ($keluarga->kepalaKeluarga && $keluarga->kepalaKeluarga->user_id) {
            $user = User::find($keluarga->kepalaKeluarga->user_id);
            if ($user) {
                return $user;
            }
        }

        // 2. Cek user_id dari anggota keluarga mana pun
        $wargaWithUser = $keluarga->wargas()->whereNotNull('user_id')->first();
        if ($wargaWithUser && $wargaWithUser->user_id) {
            $user = User::find($wargaWithUser->user_id);
            if ($user) {
                return $user;
            }
        }

        // 3. Cek User berdasarkan username = NIK kepala keluarga
        if ($keluarga->kepalaKeluarga && $keluarga->kepalaKeluarga->nik) {
            $user = User::where('username', $keluarga->kepalaKeluarga->nik)->first();
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Helper normalisasi Jenis Kelamin
     */
    protected function normalisasiJenisKelamin(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim($value));
        if (in_array($v, ['laki_laki', 'laki-laki', 'laki laki', 'pria', 'l'])) {
            return 'laki_laki';
        }
        if (in_array($v, ['perempuan', 'wanita', 'p'])) {
            return 'perempuan';
        }

        return 'laki_laki';
    }

    /**
     * Helper normalisasi Status Hubungan Dalam Keluarga (SHDK)
     */
    protected function normalisasiStatusHubungan(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim(str_replace(['-', ' '], '_', $value)));
        $allowed = [
            'kepala_keluarga', 'suami', 'istri', 'anak', 'menantu',
            'cucu', 'orang_tua', 'mertua', 'famili_lain', 'lainnya',
        ];

        if (in_array($v, $allowed)) {
            return $v;
        }

        return match ($v) {
            'anak_kandung' => 'anak',
            'kepala' => 'kepala_keluarga',
            'famili' => 'famili_lain',
            default => 'lainnya',
        };
    }

    /**
     * Helper normalisasi Agama
     */
    protected function normalisasiAgama(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim($value));
        $allowed = ['islam', 'katolik', 'protestan', 'buddha', 'hindu', 'konghucu'];

        if (in_array($v, $allowed)) {
            return $v;
        }

        if (str_contains($v, 'kristen') || str_contains($v, 'protestan')) {
            return 'protestan';
        }
        if (str_contains($v, 'katolik')) {
            return 'katolik';
        }

        return 'islam';
    }

    /**
     * Helper normalisasi Pendidikan Terakhir
     */
    protected function normalisasiPendidikan(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim(str_replace(['/', '-', ' '], '_', $value)));
        $allowed = [
            'tidak_belum_sekolah', 'belum_tamat_sd', 'sd_sederajat',
            'smp_sederajat', 'sma_sederajat', 'd1_d2', 'd3', 'd4_s1', 's2', 's3',
        ];

        if (in_array($v, $allowed)) {
            return $v;
        }

        if (str_contains($v, 's3') || str_contains($v, 'doktor')) {
            return 's3';
        }
        if (str_contains($v, 's2') || str_contains($v, 'magister')) {
            return 's2';
        }
        if (str_contains($v, 's1') || str_contains($v, 'sarjana') || str_contains($v, 'd4')) {
            return 'd4_s1';
        }
        if (str_contains($v, 'd3') || str_contains($v, 'diploma_3')) {
            return 'd3';
        }
        if (str_contains($v, 'd1') || str_contains($v, 'd2')) {
            return 'd1_d2';
        }
        if (str_contains($v, 'sma') || str_contains($v, 'smk') || str_contains($v, 'slta')) {
            return 'sma_sederajat';
        }
        if (str_contains($v, 'smp') || str_contains($v, 'sltp')) {
            return 'smp_sederajat';
        }
        if (str_contains($v, 'sd')) {
            return 'sd_sederajat';
        }

        return 'tidak_belum_sekolah';
    }

    /**
     * Helper normalisasi Status Warga
     */
    protected function normalisasiStatusWarga(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim(str_replace([' ', '-', '(', ')'], '_', $value)));
        if (str_contains($v, 'hidup')) {
            return 'hidup';
        }
        if (str_contains($v, 'pindah_kk')) {
            return 'pindah_kk';
        }
        if (str_contains($v, 'pindah_rumah') || str_contains($v, 'pindah')) {
            return 'pindah_rumah';
        }
        if (str_contains($v, 'meninggal')) {
            return 'meninggal';
        }

        return 'hidup';
    }
}