<?php

namespace App\Http\Controllers;

use App\Enums\StatusPublikasi;
use App\Models\Pengumuman;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Landing Page
     */
    public function home(): View
    {
        $pengumuman = Pengumuman::where('status_publikasi', StatusPublikasi::PUBLISH)
            ->latest()
            ->take(3)
            ->get();

        return view('public.home', compact('pengumuman'));
    }

    /**
     * Daftar Pengumuman Publik
     */
    public function pengumumanIndex(): View
    {
        $pengumuman = Pengumuman::where('status_publikasi', StatusPublikasi::PUBLISH)
            ->latest()
            ->paginate(6);

        return view('public.pengumuman-index', compact('pengumuman'));
    }

    /**
     * Detail Pengumuman
     */
    public function pengumumanShow(int $id): View
    {
        $item = Pengumuman::where('status_publikasi', StatusPublikasi::PUBLISH)
            ->findOrFail($id);

        return view('public.pengumuman-show', compact('item', 'id'));
    }
}
