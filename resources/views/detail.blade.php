@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen pb-24">
    <nav class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <ol class="flex items-center space-x-2 text-sm text-[#ADB5BD]">
            <li><a href="{{ route('armada.index') }}" class="hover:text-albirru-blue transition-colors">Daftar Armada</a></li>
            <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
            <li class="font-medium text-[#212529]">{{ $armada->nama_bus }}</li>
        </ol>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Galeri Dinamis --}}
        @php 
            $fotos = $armada->foto ?? [];
        @endphp

        <div class="swiper mySwiper rounded-2xl overflow-hidden shadow-lg mt-8">
            <div class="swiper-wrapper">

                @forelse($fotos as $foto)
                    @php
                        $extension = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
                        $isVideo = in_array($extension, ['mp4', 'webm', 'mov']);
                    @endphp

                    <div class="swiper-slide bg-black">
                        @if ($isVideo)
                            {{-- Tampilan Video --}}
                            <div class="relative w-full h-[250px] md:h-[600px] flex items-center justify-center">
                                <video 
                                    controls 
                                    playsinline 
                                    muted 
                                    class="w-full h-full object-contain md:object-cover"
                                >
                                    <source src="{{ asset('storage/' . $foto) }}" type="video/{{ $extension === 'mov' ? 'mp4' : $extension }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @else
                            {{-- Tampilan Gambar --}}
                            <img 
                                src="{{ asset('storage/' . $foto) }}" 
                                class="w-full h-[250px] md:h-[600px] object-cover"
                                alt="Foto Armada Albirru Trans"
                            >
                        @endif
                    </div>
                @empty
                    {{-- Default jika tidak ada foto/video --}}
                    <div class="swiper-slide">
                        <img 
                            src="{{ asset('images/default-bus.jpg') }}" 
                            class="w-full h-[250px] md:h-[600px] object-cover"
                        >
                    </div>
                @endforelse
            </div>

            {{-- Navigasi --}}
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next !text-white after:!text-2xl"></div>
            <div class="swiper-button-prev !text-white after:!text-2xl"></div>
        </div>

        <div class="grid lg:grid-cols-3 gap-12 items-start mt-12">
            <div class="lg:col-span-2 space-y-10">
                <div>
                    <h1 class="text-4xl font-semibold text-[#212529] tracking-tight mb-6">{{ $armada->nama_bus }}</h1>
                    <div class="flex w-fit items-center gap-3 px-5 py-2.5 bg-blue-50 text-albirru-blue rounded-full">
                        <i class="ph-fill ph-users-three text-3xl"></i>
                        <span class="text-base font-medium">{{ $armada->kapasitas }} Penumpang</span>
                    </div>
                </div>

                {{-- Informasi Penting --}}
                <div class="p-4 bg-blue-50 rounded-xl flex items-start gap-4">
                    <div class="p-2 bg-white rounded-lg shadow-sm text-albirru-blue flex items-center justify-center">
                        <i class="ph-fill ph-info text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-[#6C757D] text-sm">Informasi Penting</h4>
                        <p class="text-albirru-blue font-medium">Harga sesuai dengan tujuan anda yang akan diperhitungkan oleh pihak Albirru Trans</p>
                    </div>
                </div>

                {{-- Deskripsi Dinamis --}}
                <div>
                    <h3 class="text-lg font-semibold text-[#212529] mb-2">Deskripsi Armada</h3>
                    <p class="text-[#6C757D] leading-relaxed">
                        {{ $armada->deskripsi }}
                    </p>
                </div>

                {{-- Bagian Persyaratan & Fasilitas tetap sama karena biasanya statis per perusahaan --}}
                {{-- Namun jika fasilitas ingin dinamis, kamu bisa tambah kolom di DB --}}
                <div>
                    <h3 class="text-lg font-semibold text-[#212529] mb-3">Persyaratan Sewa Armada</h3>
                    <ul class="space-y-3 text-[#6C757D]">
                        <li class="flex items-center gap-3 text-[#6C757D]">
                            <i class="ph-fill ph-seal-check text-xl text-green-600"></i>
                            Mengisi data pemesanan dengan lengkap dan benar pada form yang tersedia.
                        </li>
                        <li class="flex items-center gap-3 text-[#6C757D]">
                            <i class="ph-fill ph-seal-check text-xl text-green-600"></i>
                            Menyertakan nomor kontak yang aktif untuk keperluan konfirmasi pemesanan.
                        </li>
                        <li class="flex items-center gap-3 text-[#6C757D]">
                            <i class="ph-fill ph-seal-check text-xl text-green-600"></i>
                            Menentukan tanggal perjalanan serta durasi penggunaan bus.
                        </li>
                        <li class="flex items-center gap-3 text-[#6C757D]">
                            <i class="ph-fill ph-seal-check text-xl text-green-600"></i>
                            Memberikan informasi lokasi penjemputan dan tujuan perjalanan.
                        </li>
                        <li class="flex items-center gap-3 text-[#6C757D]">
                            <i class="ph-fill ph-seal-check text-xl text-green-600"></i>
                            Menyetujui ketentuan layanan yang berlaku sebelum pemesanan diproses.
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-[#212529] mb-4">Fasilitas Armada</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-white rounded-2xl border border-[#E9ECEF]">
                            <div class="text-albirru-blue mb-2 flex justify-center">
                                <i class="ph-fill ph-lightning text-4xl"></i>
                            </div>
                            <span class="text-sm text-[#495057]">Full AC</span>
                        </div>
                        <div class="text-center p-4 bg-white rounded-2xl border border-[#E9ECEF]">
                            <div class="text-albirru-blue mb-2 flex justify-center">
                                <i class="ph-fill ph-microphone-stage text-4xl"></i>
                            </div>
                            <span class="text-sm text-[#495057]">Karaoke</span>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Aside Form --}}
            <aside class="lg:sticky lg:top-26">
                <div class="bg-white p-5 rounded-xl border border-[#DEE2E6]">
                    <h3 class="text-xl font-semibold text-[#212529] mb-4">Formulir Penyewaan</h3>
                    <form id="formBooking" action="{{ route('armada.sewa', $armada->id) }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- 1. INPUT TERSEMBUNYI (Data yang beneran dikirim ke Controller) --}}
                        <input type="hidden" name="tanggal_mulai" value="{{ $tgl_mulai }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $tgl_selesai }}">

                        {{-- 2. TAMPILAN VISUAL (Hanya untuk dilihat user, tidak dikirim ke server) --}}
                        <div class="space-y-3 p-4 border border-[#DEE2E6] rounded-lg bg-[#F8F9FA]">
                            <div>
                                <label class="block text-[14px] font-medium text-[#212529] mb-1 ml-1">Tanggal Awal Sewa</label>
                                <input type="text" 
                                    value="{{ \Carbon\Carbon::parse($tgl_mulai)->locale('id')->translatedFormat('d F Y') }}" 
                                    disabled 
                                    class="w-full px-4 py-3 bg-white border border-[#DEE2E6] rounded-lg text-sm font-medium text-[#ADB5BD] cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-[14px] font-medium text-[#212529] mb-1 ml-1">Tanggal Akhir Sewa</label>
                                <input type="text" 
                                    value="{{ \Carbon\Carbon::parse($tgl_selesai)->locale('id')->translatedFormat('d F Y') }}" 
                                    disabled 
                                    class="w-full px-4 py-3 bg-white border border-[#DEE2E6] rounded-lg text-sm font-medium text-[#ADB5BD] cursor-not-allowed">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[14px] font-medium text-[#212529] mb-1 ml-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" required placeholder="Masukkan nama lengkap anda" 
                                class="w-full px-4 py-3 bg-white border border-[#DEE2E6] rounded-lg text-sm focus:ring-1 focus:ring-[#6C757D] outline-none">
                                @error('nama_lengkap')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        <div>
                            <label class="block text-[14px] font-medium text-[#212529] mb-1 ml-1">Nomor WhatsApp</label>
                            <input type="tel" name="nomor_hp" required placeholder="Cth: 081234567890" 
                                class="w-full px-4 py-3 bg-white border border-[#DEE2E6] rounded-lg text-sm focus:ring-1 focus:ring-[#6C757D] outline-none">
                                @error('nomor_hp')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        <div>
                            <label class="block text-[14px] font-medium text-[#212529] mb-1 ml-1">Tujuan Destinasi</label>
                            <textarea name="tujuan" required placeholder="Cth: Jogja / Jakarta" 
                                    class="w-full px-4 py-3 bg-white border border-[#DEE2E6] rounded-lg text-sm focus:ring-1 focus:ring-[#6C757D] outline-none resize-none"></textarea>
                                @error('tujuan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        <div>
                            <label class="block text-[14px] font-medium text-[#212529] mb-1 ml-1">Alamat Penjemputan</label>
                            <textarea name="alamat_penjemputan" rows="3" required placeholder="Masukkan alamat lengkap penjemputan Anda" 
                                    class="w-full px-4 py-3 bg-white border border-[#DEE2E6] rounded-lg text-sm focus:ring-1 focus:ring-[#6C757D] outline-none resize-none"></textarea>
                                @error('alamat_penjemputan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                        </div>

                        <button type="button" onclick="confirmBooking()" id="submitBtn" class="w-full py-3 bg-albirru-blue text-white rounded-xl font-medium text-[14px] hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i class="ph-fill ph-whatsapp-logo text-2xl"></i>
                            Pesan via WhatsApp
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function sendWhatsApp() {
        const nama = document.getElementById('nama').value;
        const bus = "{{ $armada->nama_bus }}";
        const tglMulai = "{{ $tgl_mulai }}";
        const tglSelesai = "{{ $tgl_selesai }}";
        
        const message = `Halo Albirru Trans, saya ${nama}. Saya ingin memesan armada ${bus} untuk tanggal ${tglMulai} sampai ${tglSelesai}. Mohon informasi ketersediaannya.`;
        const waUrl = `https://wa.me/628123456789?text=${encodeURIComponent(message)}`;
        
        window.open(waUrl, '_blank');
    }

    function confirmBooking() {
        Swal.fire({
            title: 'Konfirmasi Penyewaan?',
            text: 'Pastikan data yang Anda masukkan sudah benar sebelum melanjutkan penyewaan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan Penyewaan',
            cancelButtonText: 'Cek Kembali',

            // 🔥 CUSTOM STYLE
            buttonsStyling: false,
            customClass: {
                confirmButton: 'bg-albirru-blue text-white px-4 py-2 rounded-lg mr-3',
                cancelButton: 'bg-[#DEE2E6] text-black px-4 py-2 rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                document.getElementById('formBooking').submit();
            }
        });
    }

    var swiper = new Swiper(".mySwiper", {
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        // Jika ingin autoplay, pastikan video tetap prioritas
        autoplay: false, 
    });
</script>
@endpush

@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '{{ session('success') }}',
// }).then(() => {
//     window.open("{{ $waUrl }}", "_blank");
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '{{ session('error') }}',
});
</script>
@endif
@endsection