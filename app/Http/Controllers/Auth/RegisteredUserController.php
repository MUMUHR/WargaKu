<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view (menggunakan view kustom WargaKu).
     */
    public function create(): View
    {
        return view('public.register');
    }

    /**
     * Handle an incoming registration request (BR-09 / F001 / UC-01).
     * Aturan Bisnis BR-09:
     * - NIK belum ada: No. KK belum terdaftar -> buat KK, User, dan Warga baru (status hidup, kepala keluarga).
     * - NIK ada, status 'hidup': ditolak, arahkan ke Login.
     * - NIK ada, status 'meninggal': ditolak, hubungi Admin RT.
     * - NIK ada, status 'pindah_rumah': ditolak, hubungi Admin RT.
     * - NIK ada, status 'pindah_kk':
     *   - Jika No. KK sudah ada: ditolak, penambahan ke KK yang sudah ada harus melalui Admin RT.
     *   - Jika No. KK baru: izinkan membentuk KK baru! Buat record keluarga baru, buat akun user baru,
     *     dan perbarui record warga (keluarga_id KK baru, user_id akun baru, status_warga 'hidup', status_hubungan 'kepala_keluarga').
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Data Kartu Keluarga
            'no_kk'                 => ['required', 'string', 'size:16'],
            'status_tempat_tinggal' => ['required', 'in:tetap,kontrak,kost,menumpang'],

            // Identitas Pendaftar Utama
            'nik'                   => ['required', 'string', 'size:16'],
            'nama'                  => ['required', 'string', 'max:100'],
            'jenis_kelamin'         => ['required', 'in:L,P'],

            // Alamat
            'alamat'                => ['required', 'string'],

            // Akun
            'no_hp'                 => ['required', 'string', 'max:20'],
        ], [
            'no_kk.required'                 => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'no_kk.size'                     => 'Nomor Kartu Keluarga harus tepat 16 digit.',
            'status_tempat_tinggal.required' => 'Silakan pilih status tempat tinggal Anda.',
            'status_tempat_tinggal.in'       => 'Pilihan status tempat tinggal tidak valid.',

            'nik.required'                   => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.size'                       => 'NIK harus tepat 16 digit.',
            'nama.required'                  => 'Nama lengkap wajib diisi sesuai KTP.',
            'nama.max'                       => 'Nama lengkap tidak boleh lebih dari 100 karakter.',
            'jenis_kelamin.required'         => 'Silakan pilih jenis kelamin Anda.',
            'jenis_kelamin.in'               => 'Pilihan jenis kelamin tidak valid.',

            'alamat.required'                => 'Alamat domisili lengkap wajib diisi.',
            'no_hp.required'                 => 'Nomor WhatsApp / HP aktif wajib diisi.',
            'no_hp.max'                      => 'Nomor WhatsApp / HP tidak boleh lebih dari 20 karakter.',
        ]);

        $noKk = trim($request->no_kk);
        $nik = trim($request->nik);

        // Audit Aturan Bisnis BR-09 (Kependudukan & Status Warga)
        $existingWarga = Warga::where('nik', $nik)->first();
        $kkSudahAda = Keluarga::where('no_kk', $noKk)->exists();

        if ($existingWarga) {
            if ($existingWarga->status_warga === 'hidup') {
                throw ValidationException::withMessages([
                    'nik' => 'NIK ini sudah terdaftar sebagai warga aktif dalam sistem. Silakan langsung masuk (login) ke akun Anda.',
                ]);
            }

            if ($existingWarga->status_warga === 'meninggal') {
                throw ValidationException::withMessages([
                    'nik' => 'NIK ini tercatat dengan status telah meninggal dunia. Silakan hubungi Pengurus RT jika terdapat kekeliruan data.',
                ]);
            }

            if ($existingWarga->status_warga === 'pindah_rumah') {
                throw ValidationException::withMessages([
                    'nik' => 'NIK ini tercatat telah pindah domisili keluar wilayah RT 04. Silakan hubungi Pengurus RT untuk aktivasi ulang.',
                ]);
            }

            if ($existingWarga->status_warga === 'pindah_kk') {
                if ($kkSudahAda) {
                    throw ValidationException::withMessages([
                        'no_kk' => 'Nomor Kartu Keluarga ini sudah terdaftar. Warga berstatus pindah KK yang ingin bergabung ke KK yang sudah ada harus melalui penambahan anggota oleh Admin RT.',
                    ]);
                }
            }
        } else {
            // NIK baru (belum ada di tabel wargas)
            if ($kkSudahAda) {
                throw ValidationException::withMessages([
                    'no_kk' => 'Nomor Kartu Keluarga ini sudah terdaftar dalam sistem. Silakan masuk (login) menggunakan akun KK Anda atau hubungi Admin RT untuk menambah anggota.',
                ]);
            }
        }

        // Gunakan DB Transaction agar pembuatan KK, Akun User, dan data Warga atomik
        DB::transaction(function () use ($request, $existingWarga, $noKk, $nik) {
            // 1. Buat record Keluarga Baru
            $keluarga = Keluarga::create([
                'no_kk'          => $noKk,
                'status_ekonomi' => 'mampu', // default BR-11
                'status_tinggal' => $request->status_tempat_tinggal,
                'alamat'         => $request->alamat,
            ]);

            // 2. Buat atau perbarui Akun User (username = NIK, role = warga, password default = NIK)
            $user = User::where('username', $nik)->first();
            if ($user) {
                $user->update([
                    'password' => Hash::make($nik),
                    'no_telp'  => $request->no_hp,
                    'role'     => 'warga',
                ]);
            } else {
                $user = User::create([
                    'username' => $nik,
                    'password' => Hash::make($nik),
                    'no_telp'  => $request->no_hp,
                    'role'     => 'warga',
                ]);
            }

            // 3. Update data warga lama (pindah_kk) atau buat warga baru
            if ($existingWarga && $existingWarga->status_warga === 'pindah_kk') {
                $existingWarga->update([
                    'keluarga_id'         => $keluarga->id,
                    'user_id'             => $user->id,
                    'nama'                => $request->nama,
                    'tempat_lahir'        => $request->tempat_lahir ?? $existingWarga->tempat_lahir,
                    'tanggal_lahir'       => $request->tanggal_lahir ?? $existingWarga->tanggal_lahir,
                    'jenis_kelamin'       => $request->jenis_kelamin === 'L' ? 'laki_laki' : 'perempuan',
                    'agama'               => Warga::normalisasiAgama($request->agama) ?? $existingWarga->agama,
                    'pendidikan_terakhir' => $this->mapPendidikan($request->pendidikan ?? null) ?? $existingWarga->pendidikan_terakhir,
                    'pekerjaan'           => $request->pekerjaan ?? $existingWarga->pekerjaan,
                    'alamat'              => $request->alamat,
                    'status_hubungan'     => 'kepala_keluarga',
                    'status_warga'        => 'hidup',
                ]);
            } else {
                Warga::create([
                    'user_id'             => $user->id,
                    'keluarga_id'         => $keluarga->id,
                    'nik'                 => $nik,
                    'nama'                => $request->nama,
                    'tempat_lahir'        => $request->tempat_lahir ?? null,
                    'tanggal_lahir'       => $request->tanggal_lahir ?? null,
                    'jenis_kelamin'       => $request->jenis_kelamin === 'L' ? 'laki_laki' : 'perempuan',
                    'agama'               => Warga::normalisasiAgama($request->agama),
                    'pendidikan_terakhir' => $this->mapPendidikan($request->pendidikan ?? null),
                    'pekerjaan'           => $request->pekerjaan ?? null,
                    'alamat'              => $request->alamat,
                    'status_hubungan'     => 'kepala_keluarga',
                    'status_warga'        => 'hidup',
                ]);
            }

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect()->route('warga.beranda')
            ->with('success', 'Pendaftaran akun berhasil! Kartu Keluarga baru telah terdaftar dan akun Anda kini aktif.');
    }

    /**
     * Mapping nilai pendidikan dari form ke enum DB.
     */
    private function mapPendidikan(?string $value): ?string
    {
        $map = [
            'SD'              => 'sd_sederajat',
            'SMP'             => 'smp_sederajat',
            'SMA/SMK'         => 'sma_sederajat',
            'D3'              => 'd3',
            'S1'              => 'd4_s1',
            'S2/S3'           => 's2',
            'Tidak/Belum Sekolah' => 'tidak_belum_sekolah',
        ];

        return $map[$value] ?? null;
    }
}
