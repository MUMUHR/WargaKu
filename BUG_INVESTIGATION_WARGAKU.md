# Catatan Bug: 404 Gagal Pembatalan Pengajuan Perubahan Data

## Kronologi Temuan
- Ditemukan bug 404 Not Found saat warga membatalkan pengajuan perubahan data melalui portal warga.
- Gejala: Setelah mengisi form pengajuan perubahan dan menekan “Ya, Batalkan Pengajuan”, browser menampilkan 404, serta status pengajuan tidak berubah tetap 'menunggu'.
- Bug terjadi di laptop lain, tidak selalu terdeteksi di laptop utama.

## Analisis Penyebab
- JS pembentukan action URL pada form konfirmasi batal membentuk URL manual:
  `form.action = '{{ url('/warga/keluarga/pengajuan') }}/' + id + '/batal';`
- Jika base URL atau ID tidak cocok (misal data pengajuan tidak milik keluarga-user, atau session/caching beda), maka POST menuju endpoint tidak valid, menghasilkan 404 dari Laravel.
- Di Controller, validasi `where('keluarga_id', $keluarga->id)->findOrFail($id);`—muncul 404 jika ID tidak cocok/keluarga tidak resolve.

## Solusi / Perbaikan
- Patch form JS pada Blade untuk pakai route helper:
  `form.action = @json(route('warga.keluarga.pengajuan.batal',['id'=>''])) + id;`
- Aksi ini menjamin URL selalu canonikal + valid ke route Laravel.
- Regression test: ajukan pengajuan → batalkan → status otomatis berubah jadi 'dibatalkan' tanpa error.

## Lessons Learned
- Hindari hardcoded/manipulasi path route manual di JS pada project Laravel; gunakan route helper.
- Usahakan validasi ownership data pada controller, error tetap redirect, **bukan** raw 404.

## Rekomendasi
Lanjutkan regression test serupa untuk semua POST/PATCH/DELETE via JS-form action!