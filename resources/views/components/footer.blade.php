<footer class="bg-[#E3ECF8] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 text-center md:text-left">
        <div>
            <a href="/" class="inline-flex items-center justify-center md:justify-start">
                <img 
                    src="{{ asset('logo/horizontal-light.svg') }}" 
                    alt="Albirru Trans"
                    class="h-7 lg:h-8 w-auto object-contain"
                >
            </a>
            <p class="mt-1 text-base text-[#6C757D] max-w-sm">Memberikan layanan transportasi terbaik untuk perjalanan yang tak terlupakan bersama Albirru Trans.</p>
        </div>
        <div class="flex items-start justify-center md:justify-end gap-8 text-sm font-medium text-[#6C757D]">
            <a href="/" class="hover:text-albirru-blue">Beranda</a>
            <a href="/armada" class="hover:text-albirru-blue">Daftar Armada</a>
            <a href="/galeri" class="hover:text-albirru-blue">Galeri Kami</a>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-[#ADB5BD] text-center text-sm text-[#6C757D]">
        © {{ date('Y') }} Albirru Trans.
    </div>
</footer>