@if ($paginator->hasPages())
    <div class="warga-pagination-nav" role="navigation" aria-label="Navigasi Halaman" style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 8px;">
        <div style="font-size: 12.5px; color: #6c757d;">
            Menampilkan <strong>{{ $paginator->firstItem() }}</strong> sampai <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> entri
        </div>

        <div class="warga-page-buttons" style="display: flex; align-items: center; gap: 4px;">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <button type="button" class="warga-page-btn warga-page-btn--disabled" disabled aria-disabled="true">&laquo; Sebelumnya</button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="warga-page-btn" rel="prev">&laquo; Sebelumnya</a>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik Tiga --}}
                @if (is_string($element))
                    <span class="warga-page-btn warga-page-btn--disabled" style="cursor: default;">{{ $element }}</span>
                @endif

                {{-- Array Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button type="button" class="warga-page-btn warga-page-btn--active" aria-current="page">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="warga-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="warga-page-btn" rel="next">Berikutnya &raquo;</a>
            @else
                <button type="button" class="warga-page-btn warga-page-btn--disabled" disabled aria-disabled="true">Berikutnya &raquo;</button>
            @endif
        </div>
    </div>
@endif
