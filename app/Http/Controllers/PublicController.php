<?php

namespace App\Http\Controllers;

use App\Enums\StatusPublikasi;
use App\Models\Pengumuman;

class PublicController extends Controller
{
    /**
     * Landing Page
     */
    public function home()
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
    public function pengumumanIndex()
    {
        $pengumuman = Pengumuman::where('status_publikasi', StatusPublikasi::PUBLISH)
            ->latest()
            ->paginate(6);

        return view('public.pengumuman-index', compact('pengumuman'));
    }

    /**
     * Detail Pengumuman
     */
    public function pengumumanShow(int $id)
    {
        $item = Pengumuman::where('status_publikasi', StatusPublikasi::PUBLISH)
            ->findOrFail($id);

        return view('public.pengumuman-show', compact('item', 'id'));
    }
}
