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
     * Handle an incoming registration request.
     * Proses pendaftaran warga baru:
     * 1. Buat record Keluarga (tabel keluargas)
     * 2. Buat User (tabel users), username = NIK pendaftar utama
     * 3. Buat Warga (tabel wargas) sebagai kepala keluarga / pendaftar utama
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Data Kartu Keluarga
            'no_kk'                 => ['required', 'string', 'size:16', 'unique:keluargas,no_kk'],
            'status_tempat_tinggal' => ['required', 'in:tetap,kontrak,kost,menumpang'],

            // Identitas Pendaftar Utama
            'nik'                   => ['required', 'string', 'size:16', 'unique:wargas,nik'],
            'nama'                  => ['required', 'string', 'max:100'],
            'jenis_kelamin'         => ['required', 'in:L,P'],

            // Alamat
            'alamat'                => ['required', 'string'],

            // Akun
            'no_hp'                 => ['required', 'string', 'max:20'],
        ], [
            'no_kk.required'                 => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'no_kk.unique'                   => 'Nomor Kartu Keluarga ini sudah terdaftar dalam sistem.',
            'no_kk.size'                     => 'Nomor Kartu Keluarga harus tepat 16 digit.',
            'status_tempat_tinggal.required' => 'Silakan pilih status tempat tinggal Anda.',
            'status_tempat_tinggal.in'       => 'Pilihan status tempat tinggal tidak valid.',

            'nik.required'                   => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.unique'                     => 'NIK ini sudah terdaftar dalam sistem.',
            'nik.size'                       => 'NIK harus tepat 16 digit.',
            'nama.required'                  => 'Nama lengkap wajib diisi sesuai KTP.',
            'nama.max'                       => 'Nama lengkap tidak boleh lebih dari 100 karakter.',
            'jenis_kelamin.required'         => 'Silakan pilih jenis kelamin Anda.',
            'jenis_kelamin.in'               => 'Pilihan jenis kelamin tidak valid.',

            'alamat.required'                => 'Alamat domisili lengkap wajib diisi.',
            'no_hp.required'                 => 'Nomor WhatsApp / HP aktif wajib diisi.',
            'no_hp.max'                      => 'Nomor WhatsApp / HP tidak boleh lebih dari 20 karakter.',
        ]);

        // Gunakan DB Transaction agar semua berhasil atau semua dibatalkan
        DB::transaction(function () use ($request) {
            // 1. Buat record Keluarga
            $keluarga = Keluarga::create([
                'no_kk'          => $request->no_kk,
                'status_ekonomi' => 'mampu', // default, admin bisa ubah nanti
                'status_tinggal' => $request->status_tempat_tinggal,
            ]);

            // 2. Buat User (username = NIK, role = warga, password default = NIK)
            $user = User::create([
                'username' => $request->nik,
                'password' => Hash::make($request->nik),
                'no_telp'  => $request->no_hp,
                'role'     => 'warga',
            ]);

            // 3. Buat Warga (kepala keluarga / pendaftar utama)
            $warga = Warga::create([
                'user_id'           => $user->id,
                'keluarga_id'       => $keluarga->id,
                'nik'               => $request->nik,
                'nama'              => $request->nama,
                'tempat_lahir'      => $request->tempat_lahir ?? null,
                'tanggal_lahir'     => $request->tanggal_lahir ?? null,
                'jenis_kelamin'     => $request->jenis_kelamin === 'L' ? 'laki_laki' : 'perempuan',
                'agama'             => $request->agama ?? null,
                'pendidikan_terakhir' => $this->mapPendidikan($request->pendidikan ?? null),
                'pekerjaan'         => $request->pekerjaan ?? null,
                'alamat'            => $request->alamat,
                'status_hubungan'   => 'kepala_keluarga',
                'status_warga'      => 'hidup',
            ]);

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect()->route('warga.beranda')
            ->with('success', 'Akun berhasil dibuat! Selamat datang di WargaKu.');
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
