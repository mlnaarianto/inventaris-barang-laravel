<header class="bg-purple-700 text-white p-4 flex justify-between items-center fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center space-x-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-white text-2xl focus:outline-none">
            <i :class="sidebarOpen ? 'fa-solid fa-bars' : 'fa-solid fa-times'"></i>
        </button>
        <h1 class="text-xl font-bold">Inventaris Barang</h1>
    </div>
    <div class="relative" x-data="{ open: false, showModal: false }">
        <button @click="open = !open" class="flex items-center space-x-4 focus:outline-none">
            <span class="text-sm">{{ Auth::user()->nama }} ({{ Auth::user()->role }})</span>
            <img src="{{ Auth::user()->avatar_url }}"
                onerror="this.src='{{ asset('images/1.jpg') }}'"
                class="rounded-full w-8 h-8 object-cover"
                alt="User">
        </button>
        <div x-show="open" x-cloak @click.away="open = false"
            class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-md z-50">
            @if(!Auth::user()->google_id)
            <a href="#" @click.prevent="showModal = true"
                class="px-4 py-2 text-gray-700 hover:bg-gray-200 flex items-center space-x-2">
                <i class="fa-solid fa-key"></i> <span>Change Password</span>
            </a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-200 flex items-center space-x-2">
                    <i class="fa-solid fa-sign-out-alt"></i> <span>Logout</span>
                </button>
            </form>
        </div>


        @if(!Auth::user()->google_id)

        <!-- Modal Change Password -->
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="showModal = false" class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">
                <h2 class="text-xl font-semibold mb-6 text-gray-800">Change Password</h2>
                <form method="POST" action="{{ route('password.change') }}" class="space-y-4">
                    @csrf

                    <!-- Current Password -->
                    <div x-data="{ show: false }">
                        <p class="text-sm text-gray-700 mb-1">Password Saat Ini</p>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="current_password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                required>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                                <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 013.108-4.682M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m6 0a3 3 0 01-3 3m0 0a3 3 0 01-3-3m0 0L4.121 4.121m15.758 15.758L12 12">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- New Password with Strength Bar -->
                    <div x-data="{ show: false, password: '', strength: 0 }" x-init="$watch('password', value => {
                        strength =
                            value.length >= 8 &&
                            /[A-Z]/.test(value) &&
                            /\d/.test(value) ?
                            3 :
                            value.length >= 6 ?
                            2 :
                            1
                    })">
                        <p class="text-sm text-gray-700 mb-1">Password Baru</p>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="new_password" x-model="password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                required>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                                <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 013.108-4.682M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m6 0a3 3 0 01-3 3m0 0a3 3 0 01-3-3m0 0L4.121 4.121m15.758 15.758L12 12">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <!-- Password Strength Bar -->
                        <div class="h-2 mt-2 rounded"
                            :class="{
                                'bg-red-500': strength === 1,
                                'bg-yellow-400': strength === 2,
                                'bg-green-500': strength === 3
                            }">
                        </div>
                        <p class="text-xs mt-1 text-gray-600"
                            x-text="strength === 1 ? 'Password lemah' : strength === 2 ? 'Password cukup' : strength === 3 ? 'Password kuat' : ''">
                        </p>
                    </div>

                    <!-- Confirm New Password -->
                    <div x-data="{ show: false }">
                        <p class="text-sm text-gray-700 mb-1">Konfirmasi Password Baru</p>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="new_password_confirmation"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                required>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                                <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 013.108-4.682M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m6 0a3 3 0 01-3 3m0 0a3 3 0 01-3-3m0 0L4.121 4.121m15.758 15.758L12 12">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800 transition">
                            Change
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @endif

    </div>
</header>