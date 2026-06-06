@if ($paginator->hasPages())
<div class="flex items-center justify-between mt-2">

    {{-- Info --}}
    <p class="text-xs text-gray-400">
        Halaman <strong class="text-gray-600">{{ $paginator->currentPage() }}</strong>
        dari <strong class="text-gray-600">{{ $paginator->lastPage() }}</strong>
        &nbsp;·&nbsp; {{ $paginator->total() }} data
    </p>

    {{-- Tombol --}}
    <div class="flex items-center gap-1.5 ml-4">

        {{-- Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-xs font-black text-gray-300 bg-gray-50 rounded-xl border border-gray-100 cursor-not-allowed">←</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-black text-gray-600 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-all">←</a>
        @endif

        {{-- Nomor halaman: tampilkan 2 sebelum dan 2 sesudah halaman aktif --}}
        @php
            $current = $paginator->currentPage();
            $last    = $paginator->lastPage();
            $start   = max(1, $current - 2);
            $end     = min($last, $current + 2);
        @endphp

        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}" class="px-3 py-1.5 text-xs font-black text-gray-600 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-all">1</a>
            @if ($start > 2)
                <span class="px-1 text-xs text-gray-400">...</span>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            @if ($i === $current)
                <span class="px-3 py-1.5 text-xs font-black text-white bg-blue-600 rounded-xl">{{ $i }}</span>
            @else
                <a href="{{ $paginator->url($i) }}" class="px-3 py-1.5 text-xs font-black text-gray-600 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-all">{{ $i }}</a>
            @endif
        @endfor

        @if ($end < $last)
            @if ($end < $last - 1)
                <span class="px-1 text-xs text-gray-400">...</span>
            @endif
            <a href="{{ $paginator->url($last) }}" class="px-3 py-1.5 text-xs font-black text-gray-600 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-all">{{ $last }}</a>
        @endif

        {{-- Selanjutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-black text-gray-600 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:text-blue-600 transition-all">→</a>
        @else
            <span class="px-3 py-1.5 text-xs font-black text-gray-300 bg-gray-50 rounded-xl border border-gray-100 cursor-not-allowed">→</span>
        @endif

    </div>
</div>
@endif