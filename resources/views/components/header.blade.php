<header class="bg-purple-700 text-white p-4 flex justify-between items-center fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center space-x-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-white text-2xl focus:outline-none">
            <i :class="sidebarOpen ? 'fa-solid fa-bars' : 'fa-solid fa-times'"></i>
        </button>
        <h1 class="text-xl font-bold">Inventaris Barang</h1>
    </div>
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center space-x-4 focus:outline-none">
            <span class="text-sm">{{ Auth::user()->nama }} ({{ Auth::user()->getRoleNames()->first() }})</span>
            <img src="{{ Auth::user()->avatar_url }}"
                onerror="this.src='{{ asset('images/1.jpg') }}'"
                class="rounded-full w-8 h-8 object-cover"
                alt="User">
        </button>
        <div x-show="open" x-cloak @click.away="open = false"
            class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-md z-50">
            <a href="{{ route('profile.edit') }}"
                class="px-4 py-2 text-gray-700 hover:bg-gray-200 flex items-center space-x-2">
                <i class="fa-solid fa-user"></i> <span>Profil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-200 flex items-center space-x-2">
                    <i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>