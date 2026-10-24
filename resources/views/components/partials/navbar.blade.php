<!-- resources/views/components/partials/navbar.blade.php -->
<nav class="relative z-50 flex h-16 items-center justify-between border-b border-slate-200 bg-white/80 px-4 text-slate-800 backdrop-blur-lg transition-colors duration-300 dark:border-slate-800 dark:bg-[#0b1220]/80 dark:text-slate-100">
    {{-- Left: Toggle + Brand --}}
    <div class="flex items-center gap-3">
        {{-- Sidebar Toggle --}}
        <button @click="sidebarOpen = !sidebarOpen" type="button" aria-label="Toggle Sidebar"
                class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-brand-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-brand-400">
            <i class="fa-solid fa-bars text-lg" x-show="!sidebarOpen"></i>
            <i class="fa-solid fa-xmark text-lg" x-show="sidebarOpen" x-cloak></i>
        </button>

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="ml-1 flex items-center gap-2.5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 dark:bg-brand-600 text-white font-bold text-xs shadow-[0_0_10px_rgba(6,182,212,0.3)]">
                INV
            </span>
            <span class="hidden sm:block font-semibold text-base tracking-tight text-slate-900 dark:text-white">
                INV<span class="text-brand-600 dark:text-brand-400">ENTARIS</span>
            </span>
        </a>
    </div>

    {{-- Right: Theme Toggle + User Profile --}}
    <div class="flex items-center gap-3">
        {{-- Theme Toggle --}}
        <button id="theme-toggle-dashboard" type="button" 
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition-colors hover:border-brand-400 hover:text-brand-600 dark:border-slate-700 dark:text-slate-300 dark:hover:border-brand-500 dark:hover:text-brand-400">
            <i id="theme-icon-dashboard" class="fas fa-moon text-sm"></i>
        </button>

        @auth
            @php
                $authUser = Auth::user();
                $avatar = $authUser->avatar_url ?: asset('images/1.jpg');
                $nama = $authUser->nama ?? $authUser->name;
                $roleName = $authUser->getRoleNames()->first();
            @endphp

            <div class="relative" x-data="{ open: false }">
                {{-- Profile Button --}}
                <button @click="open = !open" class="flex items-center gap-3 rounded-lg p-1 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white leading-tight">{{ $nama }}</p>
                        <p class="text-[11px] text-brand-600 dark:text-brand-400 capitalize">{{ $roleName }}</p>
                    </div>
                    <img src="{{ $avatar }}" onerror="this.src='{{ asset('images/1.jpg') }}'" alt="User"
                         class="h-9 w-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 ring-2 ring-transparent hover:ring-brand-400/50 transition-all">
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" x-cloak @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-[#0b1220] dark:shadow-brand-950/40">
                    
                    <div class="border-b border-slate-100 p-4 text-center dark:border-slate-800/50">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $nama }}</p>
                        <p class="text-xs text-slate-500">{{ $authUser->email }}</p>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-brand-600 dark:text-slate-300 dark:hover:bg-slate-800/50 dark:hover:text-brand-400 transition-colors">
                        <i class="fa-solid fa-user w-4 text-center"></i> Profil Saya
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>

<script>
    // --- Script Toggle Tema di Dashboard ---
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggleBtn = document.getElementById('theme-toggle-dashboard');
        const themeToggleIcon = document.getElementById('theme-icon-dashboard');
        const html = document.documentElement;

        function updateIcon() {
            if (html.classList.contains('dark')) {
                themeToggleIcon.classList.remove('fa-moon');
                themeToggleIcon.classList.add('fa-sun');
            } else {
                themeToggleIcon.classList.remove('fa-sun');
                themeToggleIcon.classList.add('fa-moon');
            }
        }
        updateIcon();

        themeToggleBtn?.addEventListener('click', function() {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
            updateIcon();
        });
    });
</script>