@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section id="beranda" class="relative min-h-screen flex items-center overflow-hidden">
        {{-- BACKGROUND IMAGE FULL --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('landing/image-1.jpg') }}" alt="Background Albirru Trans" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div> {{-- Gelapkan sedikit lagi agar form putihnya 'pop out' --}}
        </div>

        {{-- CONTENT --}}
        <div class="relative mt-14 md:mt-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="text-center">
                {{-- BADGE --}}
                <span class="inline-block px-4 py-2 rounded-full text-white border border-white/20 bg-white/6 backdrop-blur-sm text-sm font-medium tracking-wide mb-5">
                    Selamat Datang di Albirru Trans!
                </span>

                {{-- JUDUL --}}
                <h1 class="text-4xl md:text-7xl font-semibold text-white leading-tight max-w-4xl mx-auto">
                    Sewa Bus Mudah untuk Perjalanan Anda
                </h1>

                {{-- DESKRIPSI --}}
                <p class="mt-5 text-base text-[#DEE2E6] max-w-2xl mx-auto leading-relaxed">
                    Temukan armada terbaik yang tersedia sesuai tanggal perjalanan Anda. Lakukan pemesanan dengan cepat, aman, dan praktis dalam satu genggaman.
                </p>

                {{-- 2. DATE SELECTION --}}
                <div class="mt-10 max-w-5xl mx-auto">
                    <form action="{{ route('armada.index') }}" method="GET" 
                        class="bg-white p-1 rounded-2xl md:rounded-full shadow-2xl flex flex-col md:flex-row items-center gap-2 border border-white/20">
                        
                        {{-- Input Tanggal Mulai --}}
                        <div class="relative w-full group flex-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-6 pointer-events-none text-[#ADB5BD] group-focus-within:text-blue-600 transition-colors">
                                <i class="ph ph-calendar-dots text-2xl"></i>
                            </div>
                            <input type="text" name="tgl_mulai" id="tgl_mulai" placeholder="Pilih tanggal mulai" readonly
                                class="w-full pl-15 pr-6 py-4 bg-transparent rounded-full text-base outline-none cursor-pointer border-none focus:ring-0 placeholder:text-[#ADB5BD]" required>
                        </div>

                        {{-- Divider Garis --}}
                        <div class="hidden md:block h-10 w-[1px] bg-[#E9ECEF]"></div>

                        {{-- Input Tanggal Selesai --}}
                        <div class="relative w-full group flex-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-6 pointer-events-none text-[#ADB5BD] group-focus-within:text-blue-600 transition-colors">
                                <i class="ph ph-calendar-dots text-2xl"></i>
                            </div>
                            <input type="text" name="tgl_selesai" id="tgl_selesai" placeholder="Pilih tanggal selesai" readonly
                                class="w-full pl-14 pr-6 py-4 bg-transparent rounded-full text-base outline-none cursor-pointer border-none focus:ring-0 placeholder:text-[#ADB5BD]" required>
                        </div>

                        {{-- Tombol Cari --}}
                        <div class="w-full md:w-auto">
                            <button type="submit" 
                                class="w-full md:w-auto px-8 py-4 bg-albirru-blue hover:bg-blue-700 text-white font-medium rounded-xl md:rounded-full transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                                <i class="ph ph-magnifying-glass text-2xl"></i>
                                Cari Armada
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


    {{-- 3. TENTANG KAMI --}}
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-6 md:gap-16 items-center">
            <div class="aspect-[16/10] rounded-2xl bg-[#E9ECEF] overflow-hidden">
                <img src="{{ asset('landing/image-6.jpg') }}" alt="About Image - Albirru Trans" class="w-full h-full object-cover object-center">
            </div>
            <div class="text-center lg:text-left">
                <span class="inline-block px-4 py-2 rounded-full text-albirru-blue border border-[#DEE2E6] text-sm font-medium tracking-wide mb-5 shadow-xl">
                    Tentang Kami
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-5xl font-semibold text-[#212529] leading-tight">
                    Partner Perjalanan Anda
                </h1>
                <p class="mt-4 text-base text-[#6C757D] w-full lg:max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Albirru Trans adalah penyedia layanan penyewaan bus yang berkomitmen memberikan perjalanan yang aman, nyaman, dan terpercaya. Kami menyediakan berbagai pilihan armada bus yang dapat digunakan untuk berbagai kebutuhan perjalanan, seperti wisata, kegiatan sekolah, acara perusahaan, hingga perjalanan keluarga. Dengan armada yang terawat dan pelayanan yang profesional, kami berupaya memberikan pengalaman perjalanan terbaik bagi setiap pelanggan.
                </p>
            </div>
        </div>
    </section>

    {{-- 4. MENGAPA PILIH KAMI --}}
    <section class="py-16 md:py-24 bg-[#F8F9FA]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-2 rounded-full text-albirru-blue border border-[#DEE2E6] text-sm font-medium tracking-wide mb-5 shadow-xl">
                    Mengapa Memilih Kami
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-5xl font-semibold text-[#212529] leading-tight">
                Mengapa Memilih Bersama <br class="hidden md:block"> Albirru Trans
                </h1>
            </div>

            <div class="grid lg:grid-cols-12 gap-8 items-stretch">
                <div class="lg:col-span-8 grid md:grid-cols-2 gap-6">
                    <!-- Card -->
                    <div class="p-6 bg-white rounded-2xl border border-[#E9ECEF] hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                            <i class="ph ph-bus text-albirru-blue text-3xl"></i>
                        </div>
                        <h3 class="text-base font-semibold text-[#212529] mb-2">Armada Terawat</h3>
                        <p class="text-[#6C757D] text-base leading-relaxed">
                            Armada selalu dirawat secara berkala untuk memastikan keamanan dan kenyamanan perjalanan.
                        </p>
                    </div>
                    
                    <!-- Card -->
                    <div class="p-6 bg-white rounded-2xl border border-[#E9ECEF] hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                            <i class="ph ph-seal-check text-albirru-blue text-3xl"></i>
                        </div>
                        <h3 class="text-base font-semibold text-[#212529] mb-2">Pengemudi Profesional</h3>
                        <p class="text-[#6C757D] text-base leading-relaxed">
                            Pengemudi berpengalaman yang siap memberikan perjalanan yang aman dan nyaman.
                        </p>
                    </div>
                    
                    <!-- Card -->
                    <div class="p-6 bg-white rounded-2xl border border-[#E9ECEF] hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                            <i class="ph ph-list-checks text-albirru-blue text-3xl"></i>
                        </div>
                        <h3 class="text-base font-semibold text-[#212529] mb-2">Pilihan Armada Beragam</h3>
                        <p class="text-[#6C757D] text-base leading-relaxed">
                            Berbagai pilihan bus dengan kapasitas yang dapat disesuaikan dengan kebutuhan perjalanan.
                        </p>
                    </div>
                    
                    <!-- Card -->
                    <div class="p-6 bg-white rounded-2xl border border-[#E9ECEF] hover:shadow-lg transition-shadow">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                            <i class="ph ph-calendar-check text-albirru-blue text-3xl"></i>
                        </div>
                        <h3 class="text-base font-semibold text-[#212529] mb-2">Pemesanan Mudah</h3>
                        <p class="text-[#6C757D] text-base leading-relaxed">
                            Pilih tanggal perjalanan dan temukan armada yang tersedia dengan cepat.
                        </p>
                    </div>
                </div>

                <!-- Image -->
                <div class="lg:col-span-4 h-full">
                    <div class="relative h-full min-h-[400px] w-full rounded-3xl bg-[#E9ECEF] overflow-hidden">
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                        <img src="{{ asset('landing/image-2.jpg') }}" alt="About Image - Albirru Trans" class="w-full h-full object-cover object-center">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. CARA PEMESANAN --}}
    <section class="py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-10">
                <span class="inline-block px-4 py-2 rounded-full text-albirru-blue border border-[#DEE2E6] text-sm font-medium tracking-wide mb-5 shadow-xl">
                    Cara Pemesanan
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-5xl font-semibold text-[#212529] leading-tight">
                Langkah Mudah Sewa <br> Armada di Albirru Trans
                </h1>
            </div>

            <div class="space-y-3">
                
                <div class="flex items-center gap-6 p-6 bg-white rounded-xl border border-[#DEE2E6] hover:shadow-md">
                    <div class="flex-shrink-0 w-14 h-14 bg-blue-50 text-albirru-blue rounded-full flex items-center justify-center text-large font-bold">
                        1
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-[#212529] mb-1">Pilih Tanggal Perjalanan</h3>
                        <p class="text-base text-[#6C757D] leading-relaxed">
                            Masukkan tanggal mulai dan tanggal selesai perjalanan untuk melihat armada bus yang tersedia.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6 p-6 bg-white rounded-xl border border-[#DEE2E6] hover:shadow-md">
                    <div class="flex-shrink-0 w-14 h-14 bg-blue-50 text-albirru-blue rounded-full flex items-center justify-center text-large font-bold">
                        2
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-[#212529] mb-1">Pilih Armada</h3>
                        <p class="text-base text-[#6C757D] leading-relaxed">
                            Telusuri daftar armada yang tersedia dan pilih bus yang sesuai dengan kebutuhan perjalanan Anda.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6 p-6 bg-white rounded-xl border border-[#DEE2E6] hover:shadow-md">
                    <div class="flex-shrink-0 w-14 h-14 bg-blue-50 text-albirru-blue rounded-full flex items-center justify-center text-large font-bold">
                        3
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-[#212529] mb-1">Isi Formulir Pemesanan</h3>
                        <p class="text-base text-[#6C757D] leading-relaxed">
                            Lengkapi data pemesanan seperti nama, nomor kontak, dan informasi perjalanan yang diperlukan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6 p-6 bg-white rounded-xl border border-[#DEE2E6] hover:shadow-md">
                    <div class="flex-shrink-0 w-14 h-14 bg-blue-50 text-albirru-blue rounded-full flex items-center justify-center text-large font-bold">
                        4
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-[#212529] mb-1">Konfirmasi Pemesanan</h3>
                        <p class="text-base text-[#6C757D] leading-relaxed">
                            Setelah data terkirim, kami akan memproses pemesanan dan memastikan ketersediaan armada sesuai tanggal yang dipilih.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 6. CTA --}}
    <section class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto relative rounded-[2rem] overflow-hidden shadow-lg">
            
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('landing/image-3.jpg') }}" 
                    alt="Albirru Trans" 
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gray-900/50 "></div>
            </div>

            <div class="relative py-12 px-4 md:py-16 text-center">
                <h1 class="text-3xl md:text-5xl lg:text-5xl font-semibold text-white leading-tight mb-4">
                Sewa Armada Sekarang dan <br class="hidden md:block"> Mudahkan Perjalananmu!
                </h1>
                <p class="text-base text-gray-200 mb-10 max-w-2xl mx-auto">
                    Pilih armada, isi formulir, dan hubungi kami! Proses cepat, tanpa ribet!
                </p>
                
                <a href="/armada" class="inline-block px-8 py-3 bg-white text-albirru-blue rounded-xl font-medium text-base hover:bg-[#E9ECEF] transition duration-300 transform">
                    Sewa Armada
                </a>
            </div>
            
        </div>
    </section>

    {{-- 7. KONTAK & MAPS --}}
    <section class="py-16 md:py-24 bg-[#F8F9FA]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                
                <div>
                    <span class="inline-block px-4 py-2 rounded-full text-albirru-blue border border-[#DEE2E6] text-sm font-medium tracking-wide mb-5 shadow-xl">
                        Kontak Kami
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-5xl font-semibold text-[#212529] leading-tight mb-4">
                    Punya Pertanyaan? <br> Kami Siap Membantu!
                    </h1>
                    <p class="text-[#6C757D] text-base mb-10 leading-relaxed">
                        Tim kami selalu siap menjawab pertanyaan Anda dan memberikan solusi transportasi terbaik untuk kebutuhan perjalanan Anda.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ph ph-phone text-albirru-blue text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-[#ADB5BD]">WhatsApp / Telepon</h4>
                                <p class="text-base font-medium text-[#212529] mt-1">+62 812-3456-789</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ph ph-clock text-albirru-blue text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-[#ADB5BD]">Jam Operasional</h4>
                                <p class="text-base font-medium text-[#212529] mt-1">Setiap hari, 24 jam</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ph ph-map-pin text-albirru-blue text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-[#ADB5BD]">Alamat Garasi</h4>
                                <p class="text-base font-medium text-[#212529] mt-1">Perum Griya Indah, Dukuh Ringin, Dukuhwringin, Kec. Slawi, Kabupaten Tegal, Jawa Tengah</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative group">
                    <!-- <div class="absolute -inset-4 bg-blue-50 rounded-lg -z-10 transform group-hover:rotate-1 transition-transform duration-500 border border-[#000]"></div> -->
                    
                    <div class="aspect-square md:aspect-video lg:aspect-square w-full rounded-xl overflow-hidden shadow-lg border-4 border-white">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.0876567765745!2d109.13114157483614!3d-6.998958593002244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbee6cd160c8b%3A0xb8f67d209640697!2sAlbirru%20Trans!5e0!3m2!1sid!2sid!4v1777041329099!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startPicker = flatpickr("#tgl_mulai", {
            "locale": "id",
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr) {
                // Set batas minimal tanggal akhir agar tidak bisa pilih sebelum tanggal mulai
                endPicker.set('minDate', dateStr);
                
                // Reset tanggal akhir jika ternyata tanggal awal yang baru dipilih lebih besar 
                // dari tanggal akhir yang sudah dipilih sebelumnya
                if (endPicker.selectedDates[0] && endPicker.selectedDates[0] < selectedDates[0]) {
                    endPicker.clear();
                }
            }
        });

        const endPicker = flatpickr("#tgl_selesai", {
            "locale": "id",
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            minDate: "{{ request('tgl_mulai') ?? 'today' }}", // Biar sinkron saat halaman di-refresh
            onChange: function(selectedDates, dateStr) {
                // Validasi manual: jika tanggal akhir dipilih lebih kecil dari awal, reset keduanya
                const startDate = startPicker.selectedDates[0];
                if (startDate && selectedDates[0] < startDate) {
                    alert('Tanggal akhir tidak boleh sebelum tanggal awal!');
                    this.clear();
                    startPicker.clear(); // Opsional: reset dua-duanya sesuai request kamu
                }
            }
        });
    });
</script>
@endpush