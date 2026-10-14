<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPublikasi;
use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengumuman RT.
     */
    public function TampilkanPengumuman(): View
    {
        $pengumuman = Pengumuman::with('admin')->latest()->get();
        $totalPengumuman = $pengumuman->count();
        $totalPublish = $pengumuman->where('status_publikasi', StatusPublikasi::PUBLISH)->count();
        $totalDraft = $pengumuman->where('status_publikasi', StatusPublikasi::DRAFT)->count();

        return view('admin.pengumuman', compact('pengumuman', 'totalPengumuman', 'totalPublish', 'totalDraft'));
    }

    /**
     * Menampilkan form tambah pengumuman baru.
     */
    public function CreatePengumuman(): View
    {
        return view('admin.pengumuman-form', [
            'isEdit' => false,
            'pengumuman' => null,
        ]);
    }

    /**
     * Menyimpan pengumuman baru ke database.
     */
    public function simpanPengumuman(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul'            => ['required', 'string', 'max:150'],
            'isi'              => ['required', 'string'],
            'gambar'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status_publikasi' => ['nullable', Rule::enum(StatusPublikasi::class)],
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max'      => 'Judul pengumuman maksimal 150 karakter.',
            'isi.required'   => 'Isi konten pengumuman wajib diisi.',
            'gambar.image'   => 'Berkas harus berupa gambar valid.',
            'gambar.mimes'   => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max'     => 'Ukuran gambar maksimal 2 MB.',
        ]);

        // Prioritas status: dari tombol submit ('status') atau radio ('status_publikasi')
        $status = $request->input('status') ?? $request->input('status_publikasi') ?? StatusPublikasi::PUBLISH->value;

        // Upload gambar jika disertakan
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('pengumuman', 'public');
            $gambarPath = 'storage/' . $path;
        }

        Pengumuman::create([
            'judul'            => $validated['judul'],
            'isi'              => $validated['isi'],
            'gambar'           => $gambarPath,
            'status_publikasi' => $status,
            'admin_id'         => Auth::id(),
        ]);

        return redirect()->route('admin.pengumuman')
            ->with('success', 'Pengumuman RT berhasil disimpan!');
    }

    /**
     * Menampilkan form edit pengumuman.
     */
    public function editPengumuman(int|string $id): View
    {
        $pengumuman = Pengumuman::findOrFail($id);

        return view('admin.pengumuman-form', [
            'isEdit'     => true,
            'pengumuman' => $pengumuman,
            'id'         => $id,
        ]);
    }

    /**
     * Memperbarui pengumuman yang ada.
     */
    public function updatePengumuman(Request $request, int|string $id): RedirectResponse
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $validated = $request->validate([
            'judul'            => ['required', 'string', 'max:150'],
            'isi'              => ['required', 'string'],
            'gambar'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status_publikasi' => ['nullable', Rule::enum(StatusPublikasi::class)],
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max'      => 'Judul pengumuman maksimal 150 karakter.',
            'isi.required'   => 'Isi konten pengumuman wajib diisi.',
            'gambar.image'   => 'Berkas harus berupa gambar valid.',
            'gambar.mimes'   => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max'     => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $status = $request->input('status') ?? $request->input('status_publikasi') ?? $pengumuman->status_publikasi->value;

        // Update gambar jika ada upload baru
        $gambarPath = $pengumuman->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($pengumuman->gambar) {
                $oldPath = str_replace('storage/', '', $pengumuman->gambar);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('gambar')->store('pengumuman', 'public');
            $gambarPath = 'storage/' . $path;
        }

        $pengumuman->update([
            'judul'            => $validated['judul'],
            'isi'              => $validated['isi'],
            'gambar'           => $gambarPath,
            'status_publikasi' => $status,
        ]);

        return redirect()->route('admin.pengumuman')
            ->with('success', 'Perubahan pengumuman RT berhasil disimpan!');
    }

    /**
     * Menghapus pengumuman.
     */
    public function hapusPengumuman(int|string $id): RedirectResponse
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->gambar) {
            $oldPath = str_replace('storage/', '', $pengumuman->gambar);
            Storage::disk('public')->delete($oldPath);
        }

        $pengumuman->delete();

        return redirect()->route('admin.pengumuman')
            ->with('success', 'Pengumuman RT berhasil dihapus!');
    }
}
