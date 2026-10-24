<!-- resources/views/components/partials/footer.blade.php -->
<footer class="relative mt-8 flex-shrink-0 border-t border-slate-200 bg-white/50 backdrop-blur-sm transition-colors duration-300 dark:border-slate-800 dark:bg-[#0b1220]/50">
    {{-- Aksen tipis di atas footer --}}
    <div aria-hidden="true" class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-500/40 to-transparent dark:via-brand-400/25"></div>

    <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-1 px-4 py-4 sm:flex-row sm:gap-4">
        <span class="font-mono text-[11px] tracking-wide text-slate-500">
            &copy; {{ date('Y') }} Sistem Inventaris Barang
        </span>

        <span class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500">
            <span class="h-1.5 w-1.5 rounded-full bg-brand-500 animate-pulse" aria-hidden="true"></span>
            All systems operational
        </span>
    </div>
</footer>