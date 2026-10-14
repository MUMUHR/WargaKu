<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusSurat;
use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengajuan surat pengantar warga (Layar 2.2.13).
     */
    public function index(Request $request): View
    {
        $statMenunggu = Surat::where('status', StatusSurat::MENUNGGU->value)->count();
        $statDisetujui = Surat::where('status', StatusSurat::DISETUJUI->value)->count();
        $statDitolak = Surat::where('status', StatusSurat::DITOLAK->value)->count();
        $totalSurat = Surat::count();

        $query = Surat::with(['pemohon', 'wargaTujuan']);

        // Filter status bila ada
        if ($request->filled('status')) {
            $statusInput = strtolower($request->input('status'));
            $query->where('status', $statusInput);
        }

        // Pencarian teks bila ada
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function ($sub) use ($search) {
                      $sub->where('nama', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                  })
                  ->orWhereHas('wargaTujuan', function ($sub) use ($search) {
                      $sub->where('nama', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan status menunggu terlebih dahulu, lalu berdasarkan waktu rilis terbaru
        $surats = $query->orderByRaw("CASE WHEN status = 'menunggu' THEN 1 WHEN status = 'disetujui' THEN 2 ELSE 3 END")
            ->latest('id')
            ->get();

        $rekomendasiNomorSurat = Surat::generateNomorSurat();

        return view('admin.surat', compact(
            'surats',
            'statMenunggu',
            'statDisetujui',
            'statDitolak',
            'totalSurat',
            'rekomendasiNomorSurat'
        ));
    }

    /**
     * Pratinjau draf atau surat pengantar resmi A4 (Layar X5).
     */
    public function preview(int $id): View
    {
        $surat = Surat::with(['pemohon', 'wargaTujuan'])->findOrFail($id);
        $rekomendasiNomorSurat = $surat->nomor_surat ?? Surat::generateNomorSurat();

        return view('admin.surat-preview', compact('surat', 'rekomendasiNomorSurat'));
    }

    /**
     * Menyetujui permohonan surat dan menerbitkan nomor registrasi resmi (BR-05 / Layar 2.2.14).
     */
    public function setujui(Request $request, int $id): RedirectResponse
    {
        $surat = Surat::findOrFail($id);

        if (! $surat->isMenunggu()) {
            return back()->with('error', 'Hanya permohonan surat dengan status Menunggu yang dapat disetujui.');
        }

        $nomorSurat = $request->filled('nomor_surat')
            ? trim($request->input('nomor_surat'))
            : Surat::generateNomorSurat();

        // Validasi keunikan nomor surat
        $isDuplicate = Surat::where('nomor_surat', $nomorSurat)
            ->where('id', '!=', $surat->id)
            ->exists();

        if ($isDuplicate) {
            return back()->with('error', "Nomor surat '{$nomorSurat}' sudah digunakan pada surat lain.")->withInput();
        }

        $surat->update([
            'nomor_surat' => $nomorSurat,
            'status'      => StatusSurat::DISETUJUI,
            'alasan'      => null,
        ]);

        return redirect()->route('admin.surat')
            ->with('success', "Permohonan surat pengantar untuk {$surat->wargaTujuan?->nama} berhasil disetujui dengan Nomor Surat: {$nomorSurat}.");
    }

    /**
     * Menolak permohonan surat disertai catatan alasan penolakan (Layar 2.2.15).
     */
    public function tolak(Request $request, int $id): RedirectResponse
    {
        $surat = Surat::findOrFail($id);

        if (! $surat->isMenunggu()) {
            return back()->with('error', 'Hanya permohonan surat dengan status Menunggu yang dapat ditolak.');
        }

        $request->validate([
            'alasan' => 'nullable|string|max:1000',
        ]);

        $alasan = $request->filled('alasan')
            ? trim($request->input('alasan'))
            : 'Berkas permohonan belum melengkapi persyaratan administrasi lingkungan RT.';

        $surat->update([
            'status' => StatusSurat::DITOLAK,
            'alasan' => $alasan,
        ]);

        return redirect()->route('admin.surat')
            ->with('info', "Permohonan surat pengantar telah ditolak dengan catatan alasan.");
    }
}
