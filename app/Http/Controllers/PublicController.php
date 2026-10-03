<?php

namespace App\Http\Controllers;

use App\Data\DummyData;

class PublicController extends Controller
{
    /**
     * Landing Page (2.2.1)
     * Menampilkan pengumuman publish terbaru (maks 3 untuk preview)
     */
    public function home()
    {
        $pengumuman = array_slice(DummyData::pengumumanPublish(), 0, 3);
        return view('public.home', compact('pengumuman'));
    }

    /**
     * Daftar Semua Pengumuman Publish — halaman Pengumuman RT (2.2.1 / Landing_Page_Pengumuman)
     * Dummy pagination: 5 per halaman
     */
    public function pengumumanIndex()
    {
        $semua = DummyData::pengumumanPublish();
        $perHalaman = 5;
        $halaman = max(1, (int) request('halaman', 1));
        $total = count($semua);
        $totalHalaman = (int) ceil($total / $perHalaman);
        $offset = ($halaman - 1) * $perHalaman;
        $pengumuman = array_slice($semua, $offset, $perHalaman);

        return view('public.pengumuman-index', [
            'pengumuman'   => $pengumuman,
            'halaman'      => $halaman,
            'total'        => $total,
            'totalHalaman' => $totalHalaman,
            'perHalaman'   => $perHalaman,
            'offset'       => $offset,
        ]);
    }

    /**
     * Detail Pengumuman (X2)
     */
    public function pengumumanShow(int $id)
    {
        $item = DummyData::findPengumuman($id);

        if (! $item || $item['status_publikasi'] !== 'publish') {
            abort(404, 'Pengumuman tidak ditemukan.');
        }

        return view('public.pengumuman-show', ['item' => $item]);
    }
}
