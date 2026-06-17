@php
    $pesan = urlencode('Halo Admin Albirru Trans, 
    Saya ingin menanyakan informasi terkait penyewaan armada. Mohon bantuannya, terima kasih.');

    $wa = "https://wa.me/6289635697054?text={$pesan}";
@endphp

<nav 
    x-data="{ open: false }"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    class="fixed top-0 left-0 z-50 border-b w-full border-[#E9ECEF] bg-white/90 backdrop-blur-md"
>

    {{-- NAVBAR --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">

        {{-- LOGO --}}
        <a href="/" class="flex items-center">
            <img 
                src="{{ asset('logo/horizontal-light.svg') }}" 
                alt="Albirru Trans"
                class="h-7 lg:h-8 w-auto object-contain"
            >
        </a>

        {{-- DESKTOP MENU --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium">

            <a 
                href="/"
                class="{{ request()->is('/') 
                    ? 'text-albirru-blue font-medium' 
                    : 'text-[#ADB5BD] hover:text-albirru-blue transition' }}"
            >
                Beranda
            </a>

            <a 
                href="/armada"
                class="{{ request()->is('armada*') 
                    ? 'text-albirru-blue font-medium text-sm' 
                    : 'text-[#ADB5BD] hover:text-albirru-blue transition' }}"
            >
                Daftar Armada
            </a>

            <a href="/galeri"
                class="{{ request()->is('galeri*') 
                ? 'text-albirru-blue font-medium text-sm' 
                : 'text-[#ADB5BD] hover:text-albirru-blue transition' }}"
            >
                Galeri Kami
            </a>

        </div>

        {{-- DESKTOP BUTTON --}}
        <a 
            href="{{ $wa }}"
            target="_blank"
            class="hidden md:flex px-5 py-3 bg-albirru-blue text-white rounded-lg text-base font-medium hover:bg-blue-700 transition duration-150 shadow-sm"
        >
            Hubungi Kami
        </a>

        {{-- HAMBURGER BUTTON --}}
        <button 
            @click="open = !open"
            class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg border border-[#E9ECEF] bg-white relative z-[60]"
        >

            {{-- ICON HAMBURGER --}}
            <svg 
                x-show="!open"
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6 text-albirru-blue"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path 
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>

            {{-- ICON CLOSE --}}
            <svg 
                x-show="open"
                x-transition
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6 text-albirru-blue"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                style="display: none;"
            >
                <path 
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>

        </button>
    </div>

    {{-- OVERLAY --}}
    <div 
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="fixed top-full left-0 w-full h-[calc(100vh-73px)] bg-black/40 backdrop-blur-sm z-40 md:hidden"
        style="display: none;"
    ></div>

    {{-- MOBILE MENU --}}
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="fixed top-[73px] left-0 w-full bg-white z-50 md:hidden"
        style="display: none;"
    >

        <div class="px-4 py-6 flex flex-col gap-5">

            {{-- MENU --}}
            <a 
                href="/"
                @click="open = false"
                class="{{ request()->is('/') 
                    ? 'text-albirru-blue font-medium hover:text-albirru-blue transition' 
                    : 'text-[#ADB5BD] font-medium' }}"
            >
                Beranda
            </a>

            <a 
                href="/armada"
                @click="open = false"
                class="{{ request()->is('armada*') 
                    ? 'text-albirru-blue font-medium hover:text-albirru-blue transition' 
                    : 'text-[#ADB5BD] font-medium' }}"
            >
                Daftar Armada
            </a>
        
            <a href="/galeri"
                @click="open = false"
                class="{{ request()->is('galeri*') 
                ? 'text-albirru-blue font-medium hover:text-albirru-blue transition' 
                : 'text-[#ADB5BD] font-medium' }}"
            >
                Galeri Kami
            </a>

            {{-- BUTTON --}}
            <a 
                href="{{ $wa }}"
                target="_blank"
                class="w-full text-center px-5 py-3 bg-albirru-blue text-white rounded-lg font-medium"
            >
                Hubungi Kami
            </a>

        </div>
    </div>
</nav>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>