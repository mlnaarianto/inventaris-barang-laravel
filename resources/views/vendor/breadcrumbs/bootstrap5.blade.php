@if (count($breadcrumbs))
    <nav aria-label="breadcrumb" class="mb-6">
        {{-- Menggunakan gap-2 untuk spasi antar item --}}
        <ol class="flex flex-wrap items-center gap-2 sm:gap-3 text-sm sm:text-base">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="flex items-center">
                    @if ($breadcrumb->url && !$loop->last)
                        {{-- Link (Warna Aqua/Cyan) --}}
                        <a href="{{ $breadcrumb->url }}" class="flex items-center gap-2 text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300 transition-colors font-medium">
                            @if ($loop->first)
                                <i class="fas fa-home text-base"></i>
                            @endif
                            <span>{{ $breadcrumb->title }}</span>
                        </a>
                    @else
                        {{-- Halaman Aktif (Teks Tebal & Putih saat Dark Mode) --}}
                        <span class="flex items-center gap-2 font-bold text-slate-800 dark:text-white">
                            @if ($loop->first)
                                <i class="fas fa-home text-base text-cyan-600 dark:text-cyan-400"></i>
                            @endif
                            <span>{{ $breadcrumb->title }}</span>
                        </span>
                    @endif
                </li>
                
                {{-- Ikon Pemisah (Chevron) --}}
                @if (!$loop->last)
                    <li class="flex items-center text-slate-400 dark:text-slate-600">
                        <i class="fas fa-chevron-right text-[10px] sm:text-xs"></i>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif