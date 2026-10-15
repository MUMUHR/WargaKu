<?php

namespace App\Http\Controllers\Warga;

use App\Enums\StatusSurat;
use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\Warga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratController extends Controller
{
    /**
     * Menampilkan daftar riwayat dan formulir pengajuan surat warga (Layar 2.3.4)
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Ambil data kepala keluarga / warga yang terikat pada akun KK
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        // Ambil seluruh anggota keluarga yang masih hidup dalam satu KK
        $anggotaKeluarga = $keluarga
            ? $keluarga->wargas()
                ->where('status_warga', 'hidup')
                ->orderByRaw("CASE 
                    WHEN status_hubungan = 'kepala_keluarga' THEN 1 
                    WHEN status_hubungan = 'suami' THEN 2 
                    WHEN status_hubungan = 'istri' THEN 3 
                    WHEN status_hubungan = 'anak' THEN 4 
                    ELSE 5 
                END ASC")
                ->orderBy('tanggal_lahir', 'asc')
                ->get()
            : collect();

        // Kumpulkan ID seluruh anggota keluarga untuk filter surat keluarga
        $keluargaWargaIds = $anggotaKeluarga->pluck('id')->toArray();
        if (empty($keluargaWargaIds) && $kepala) {
            $keluargaWargaIds = [$kepala->id];
        }

        // Ambil seluruh pengajuan surat yang terkait dengan keluarga ini
        $allSurats = Surat::with(['pemohon', 'wargaTujuan'])
            ->where(function ($q) use ($keluargaWargaIds) {
                $q->whereIn('pemohon_id', $keluargaWargaIds)
                  ->orWhereIn('warga_tujuan_id', $keluargaWargaIds);
            })
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 1 WHEN status = 'disetujui' THEN 2 ELSE 3 END")
            ->latest('id')
            ->get();

        // Hitung statistik ringkasan status surat
        $statMenunggu = $allSurats->filter(fn($s) => $s->isMenunggu())->count();
        $statDisetujui = $allSurats->filter(fn($s) => $s->isDisetujui())->count();
        $statDitolak = $allSurats->filter(fn($s) => $s->isDitolak())->count();
        $totalSurat = $allSurats->count();

        // Alamat domisili keluarga
        $alamatKeluarga = ($kepala && !empty($kepala->alamat))
            ? $kepala->alamat
            : ($anggotaKeluarga->first(fn($w) => !empty($w->alamat))?->alamat ?? 'Jl. Melati Blok A No. 1, RT 04/RW 08');

        // Siapkan mapping master data anggota keluarga untuk JavaScript auto-fill form
        $dataWargaMap = [];
        foreach ($anggotaKeluarga as $w) {
            $salutation = match ($w->status_hubungan) {
                'kepala_keluarga', 'suami' => 'Bpk. ',
                'istri' => 'Ibu ',
                default => '',
            };
            $tglLahirFormat = $w->tanggal_lahir ? $w->tanggal_lahir->translatedFormat('d F Y') : '-';
            $umur = $w->tanggal_lahir ? ' (' . $w->tanggal_lahir->age . ' Th)' : '';

            $dataWargaMap[$w->id] = [
                'id' => $w->id,
                'nama' => $salutation . $w->nama,
                'namaLengkap' => $w->nama,
                'nik' => $w->nik,
                'ttl' => ($w->tempat_lahir ? $w->tempat_lahir . ', ' : '') . $tglLahirFormat . $umur,
                'ttlSurat' => ($w->tempat_lahir ? $w->tempat_lahir . ', ' : '') . $tglLahirFormat,
                'jk' => $w->jenis_kelamin === 'laki_laki' ? 'Laki-Laki' : 'Perempuan',
                'agama' => ucfirst($w->agama ?? 'Islam'),
                'alamat' => $w->alamat ?? $alamatKeluarga,
                'hubungan' => ucwords(str_replace('_', ' ', $w->status_hubungan ?? 'Anggota')),
            ];
        }

        return view('warga.surat', compact(
            'allSurats',
            'anggotaKeluarga',
            'keluarga',
            'kepala',
            'statMenunggu',
            'statDisetujui',
            'statDitolak',
            'totalSurat',
            'dataWargaMap',
            'alamatKeluarga'
        ));
    }

    /**
     * Menyimpan pengajuan surat pengantar baru dari warga (Layar 2.3.4 Tab Buat Pengajuan)
     */
    public function simpan(Request $request): RedirectResponse
    {
        $request->validate([
            'warga_tujuan_id' => 'required|exists:wargas,id',
            'keperluan'       => 'required|string|min:5|max:1000',
        ], [
            'warga_tujuan_id.required' => 'Silakan pilih anggota keluarga yang diajukan surat.',
            'warga_tujuan_id.exists'   => 'Anggota keluarga yang dipilih tidak valid.',
            'keperluan.required'       => 'Keperluan pengajuan surat pengantar wajib diisi.',
            'keperluan.min'            => 'Keperluan pengajuan minimal berisi 5 karakter.',
        ]);

        $user = auth()->user();
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        $targetWarga = Warga::findOrFail($request->input('warga_tujuan_id'));

        // Keamanan: pastikan warga tujuan berada dalam KK yang sama dengan pengguna login
        if ($keluarga && $targetWarga->keluarga_id !== $keluarga->id) {
            return back()->with('error', 'Anda hanya dapat mengajukan permohonan surat untuk anggota dalam Kartu Keluarga Anda.')->withInput();
        }

        $pemohon = $kepala ?? $user->wargaPemilik ?? $targetWarga;

        Surat::create([
            'pemohon_id'      => $pemohon->id,
            'warga_tujuan_id' => $targetWarga->id,
            'keperluan'       => trim($request->input('keperluan')),
            'nomor_surat'     => null,
            'status'          => StatusSurat::MENUNGGU,
            'alasan'          => null,
        ]);

        return redirect()->route('warga.surat')
            ->with('success', 'Pengajuan surat pengantar berhasil dikirim dan masuk dalam antrean verifikasi Pengurus RT 04.');
    }

    /**
     * Membatalkan pengajuan surat yang masih berstatus menunggu verifikasi
     */
    public function batalkan(Request $request, int $id): RedirectResponse
    {
        $surat = Surat::findOrFail($id);

        $user = auth()->user();
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;
        $keluargaWargaIds = $keluarga ? $keluarga->wargas()->pluck('id')->toArray() : [$kepala?->id];

        // Keamanan: verifikasi kepemilikan surat
        if (!in_array($surat->pemohon_id, $keluargaWargaIds) && !in_array($surat->warga_tujuan_id, $keluargaWargaIds)) {
            return back()->with('error', 'Anda tidak memiliki hak untuk membatalkan pengajuan surat ini.');
        }

        if (!$surat->isMenunggu()) {
            return back()->with('error', 'Hanya pengajuan surat dengan status Menunggu Verifikasi yang dapat dibatalkan.');
        }

        $surat->status = StatusSurat::DIBATALKAN;
        $surat->save();

        return redirect()->route('warga.surat')
            ->with('success', 'Pengajuan surat pengantar berhasil dibatalkan.');
    }

    /**
     * Menampilkan dokumen resmi surat pengantar yang telah disetujui untuk dicetak (Layar 2.3.5)
     */
    public function cetak(int $id)
    {
        $surat = Surat::with(['pemohon', 'wargaTujuan'])->findOrFail($id);

        $user = auth()->user();
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;
        $keluargaWargaIds = $keluarga ? $keluarga->wargas()->pluck('id')->toArray() : [$kepala?->id];

        // Keamanan: verifikasi hak akses dokumen surat
        if (!in_array($surat->pemohon_id, $keluargaWargaIds) && !in_array($surat->warga_tujuan_id, $keluargaWargaIds)) {
            return redirect()->route('warga.surat')->with('error', 'Anda tidak memiliki hak akses untuk mencetak surat ini.');
        }

        // Hanya surat yang disetujui yang dapat diunduh/dicetak resmi
        if (!$surat->isDisetujui()) {
            return redirect()->route('warga.surat')->with('error', 'Surat belum disetujui oleh Pengurus RT dan belum dapat dicetak.');
        }

        return view('warga.surat-cetak', compact('surat'));
    }
}
