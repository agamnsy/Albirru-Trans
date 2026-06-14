@extends('layouts.app')

@section('content')
<section class="py-12 mt-16 lg:mt-20 min-h-screen bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-7xl mx-auto mb-12 text-center">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-semibold text-[#212529] leading-tight">
                Cari Armada Untuk Anda
            </h1>
            <p class="mt-2 text-[#6C757D]">Temukan armada yang sesuai dengan kebutuhan perjalanan Anda</p>

            <div class="mt-10 bg-white p-5 rounded-xl border border-[#DEE2E6]">
                <form action="{{ route('armada.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end text-left">
                    <div class="sm:col-span-2">
                        <label class="block text-base font-medium text-[#212529] mb-1 ml-1">Tanggal Awal Sewa</label>
                        <div class="relative group">
                            <input type="text" name="tgl_mulai" id="tgl_mulai" value="{{ request('tgl_mulai') }}" placeholder="Pilih tanggal" readonly 
                                class="w-full px-4 py-3 border border-[#DEE2E6] rounded-lg text-base cursor-pointer transition-all focus:ring-1 focus:ring-[#6C757D]">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                                <i class="ph ph-calendar-dots text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-base font-medium text-[#212529] mb-1 ml-1">Tanggal Akhir Sewa</label>
                        <div class="relative group">
                            <input type="text" name="tgl_selesai" id="tgl_selesai" value="{{ request('tgl_selesai') }}" placeholder="Pilih tanggal" readonly
                                class="w-full px-4 py-3 border border-[#DEE2E6] rounded-lg text-base cursor-pointer transition-all focus:ring-1 focus:ring-[#6C757D]">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                                <i class="ph ph-calendar-dots text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-1 w-full">
                        <button type="submit" class="w-full px-5 py-3 cursor-pointer bg-albirru-blue text-white rounded-lg text-base font-medium hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i class="ph ph-magnifying-glass text-2xl"></i>
                            Cari Armada
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @if(!$tgl_mulai || !$tgl_selesai)
                {{-- KEADAAN 1: BELUM INPUT TANGGAL --}}
                <div class="col-span-full py-8 px-5 text-center bg-white rounded-xl">
                    <div class="inline-flex items-center justify-center mb-6">
                        <div class="w-56 h-56">
                            {!! file_get_contents(public_path('illustration/date-picker.svg')) !!}
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-[#212529]">Tentukan Tanggal Perjalanan Anda</h3>
                    <p class="text-[#6C757D] mt-1">Silakan masukkan tanggal mulai dan selesai sewa untuk melihat daftar armada yang tersedia</p>
                </div>

            @else
                {{-- KEADAAN 2: SUDAH INPUT TANGGAL --}}
                @forelse($armadas as $armada)
                    <div class="bg-white rounded-xl overflow-hidden border border-[#DEE2E6] hover:shadow-lg transition-all duration-200">
                        <!-- Foto Bus Carousel -->
                        @php
                            $fotos = $armada->foto ?? [];

                            if (is_string($fotos)) {
                                $decoded = json_decode($fotos, true);
                                $fotos = is_array($decoded) ? $decoded : [$fotos];
                            }

                            $fotos = is_array($fotos) ? array_filter($fotos) : [];

                            $fotoUrls = collect($fotos)
                                ->filter(fn ($foto) => Storage::disk('public')->exists($foto))
                                ->map(fn ($foto) => Storage::url($foto))
                                ->values()
                                ->toArray();

                            if (count($fotoUrls) === 0) {
                                $fotoUrls = [asset('images/default-bus.jpg')];
                            }
                        @endphp

                        <div 
                            class="relative aspect-[16/10] bg-[#E9ECEF] overflow-hidden group"
                            data-armada-carousel
                        >
                            @foreach ($fotoUrls as $index => $url)
                                <img 
                                    src="{{ $url }}"
                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                                    alt="Foto {{ $armada->nama_bus }}"
                                    data-carousel-image
                                    data-index="{{ $index }}"
                                >
                            @endforeach

                            @if(count($fotoUrls) > 1)
                                <!-- Tombol Prev -->
                                <button 
                                    type="button"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:bg-black/60"
                                    data-carousel-prev
                                    aria-label="Foto sebelumnya"
                                >
                                    <i class="ph ph-caret-left text-xl"></i>
                                </button>

                                <!-- Tombol Next -->
                                <button 
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:bg-black/60"
                                    data-carousel-next
                                    aria-label="Foto berikutnya"
                                >
                                    <i class="ph ph-caret-right text-xl"></i>
                                </button>

                                <!-- Dot Indicator -->
                                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5">
                                    @foreach ($fotoUrls as $index => $url)
                                        <button 
                                            type="button"
                                            class="w-2 h-2 rounded-full transition-all {{ $index === 0 ? 'bg-white w-5' : 'bg-white/60' }}"
                                            data-carousel-dot
                                            data-index="{{ $index }}"
                                            aria-label="Lihat foto {{ $index + 1 }}"
                                        ></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="text-xl font-semibold text-[#212529] mb-3">{{ $armada->nama_bus }}</h3>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <div class="flex items-center gap-2 px-3 py-2 bg-[#F8F9FA] rounded-md text-[#495057]">
                                    <i class="ph-fill ph-users-three text-albirru-blue text-xl"></i>
                                    <span class="text-sm font-medium text-[#495057]">{{ $armada->kapasitas }} Seats</span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-2 bg-[#F8F9FA] rounded-md text-[#495057]">
                                    <i class="ph-fill ph-lightning text-albirru-blue"></i>
                                    <span class="text-sm font-medium text-[#495057]">Full AC</span>
                                </div>
                            </div>
                            <div class="pt-5 mt-4 border-t border-[#E9ECEF]">
                                <a href="{{ route('armada.show', [$armada->id, 'tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]) }}" 
                                class="block w-full text-center py-3 bg-blue-50 text-albirru-blue font-semibold rounded-lg hover:bg-albirru-blue hover:text-white transition-all duration-200">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- KEADAAN 3: SUDAH INPUT TANGGAL TAPI GADA YANG KOSONG --}}
                    <div class="col-span-full py-20 text-center rounded-xl border border-[#DEE2E6]">
                        <div class="inline-flex items-center justify-center">
                            <div class="w-64 h-64">
                                {!! file_get_contents(public_path('illustration/empty-street.svg')) !!}
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-[#212529] -mt-16">Armada Tidak Tersedia</h3>
                        <p class="text-[#6C757D] mt-1">Maaf, semua armada kami sudah terpesan pada tanggal tersebut. Silakan coba tanggal lainnya.</p>
                    </div>
                @endforelse
            @endif
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

    document.addEventListener('DOMContentLoaded', function () {
        const carousels = document.querySelectorAll('[data-armada-carousel]');

        carousels.forEach((carousel) => {
            const images = carousel.querySelectorAll('[data-carousel-image]');
            const dots = carousel.querySelectorAll('[data-carousel-dot]');
            const prevButton = carousel.querySelector('[data-carousel-prev]');
            const nextButton = carousel.querySelector('[data-carousel-next]');

            if (images.length <= 1) {
                return;
            }

            let activeIndex = 0;
            let interval;

            function showSlide(index) {
                activeIndex = index;

                images.forEach((image, imageIndex) => {
                    image.classList.toggle('opacity-100', imageIndex === activeIndex);
                    image.classList.toggle('opacity-0', imageIndex !== activeIndex);
                });

                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('bg-white', dotIndex === activeIndex);
                    dot.classList.toggle('w-5', dotIndex === activeIndex);
                    dot.classList.toggle('bg-white/60', dotIndex !== activeIndex);
                    dot.classList.toggle('w-2', dotIndex !== activeIndex);
                });
            }

            function nextSlide() {
                const nextIndex = (activeIndex + 1) % images.length;
                showSlide(nextIndex);
            }

            function prevSlide() {
                const prevIndex = activeIndex === 0 ? images.length - 1 : activeIndex - 1;
                showSlide(prevIndex);
            }

            function startAutoSlide() {
                interval = setInterval(nextSlide, 3000);
            }

            function resetAutoSlide() {
                clearInterval(interval);
                startAutoSlide();
            }

            nextButton?.addEventListener('click', function () {
                nextSlide();
                resetAutoSlide();
            });

            prevButton?.addEventListener('click', function () {
                prevSlide();
                resetAutoSlide();
            });

            dots.forEach((dot) => {
                dot.addEventListener('click', function () {
                    showSlide(Number(this.dataset.index));
                    resetAutoSlide();
                });
            });

            carousel.addEventListener('mouseenter', function () {
                clearInterval(interval);
            });

            carousel.addEventListener('mouseleave', function () {
                startAutoSlide();
            });

            startAutoSlide();
        });
    });
</script>
@endpush