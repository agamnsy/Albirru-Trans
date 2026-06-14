@extends('layouts.app')

@section('content')
    <section class="py-12 mt-16 lg:mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- HEADER --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-semibold text-[#212529] leading-tight">
                    Galeri Albirru Trans
                </h1>
                <p class="mt-2 text-base text-[#6C757D] max-w-xl mx-auto">
                    Lihat berbagai momen perjalanan bersama Albirru Trans melalui dokumentasi foto dan video dari pelanggan kami.
                </p>
            </div>

            {{-- GRID GALERI --}}
            @if ($galeris->count())
            <div x-data="{ visibleItems: 6 }">
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($galeris as $galeri)
                        @php
                            $media = is_array($galeri->media)
                                ? ($galeri->media[0] ?? null)
                                : $galeri->media;
                            $urlMedia = asset('storage/' . $media);
                            $extension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                            $isVideo = in_array($extension, ['mp4', 'webm', 'mov', 'quicktime']);
                        @endphp
                        <div
                            x-data="{ open: false }"
                            x-show="{{ $loop->index }} < visibleItems"
                            class="bg-white border border-[#E9ECEF] rounded-xl overflow-hidden hover:shadow-lg transition duration-300 cursor-pointer"
                        >
                            {{-- CARD --}}
                            <div @click="open = true">
                                {{-- MEDIA --}}
                                <div class="aspect-[4/3] bg-[#F8F9FA] overflow-hidden relative">
                                    {{-- BADGE KATEGORI --}}
                                    <span class="absolute top-5 left-5 z-10 px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-albirru-blue shadow-md">
                                        {{ $galeri->kategori?->nama ?? 'Tidak Berkategori' }}
                                    </span>
                                    @if ($isVideo)
                                        <div class="relative w-full h-full">
                                            {{-- VIDEO THUMBNAIL --}}
                                            <video
                                                class="w-full h-full object-cover bg-black"
                                                muted
                                                playsinline
                                                webkit-playsinline
                                                preload="auto"
                                            >
                                                <source src="{{ $urlMedia }}">
                                            </video>
                                            {{-- OVERLAY --}}
                                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                                {{-- PLAY BUTTON --}}
                                                <div class="w-14 h-14 rounded-full bg-white/80 flex items-center justify-center shadow-lg">
                                                    <i class="ph-fill ph-play text-2xl text-albirru-blue"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- IMAGE --}}
                                        <img src="{{ $urlMedia }}" class="w-full h-full object-cover" alt="{{ $galeri->judul }}">
                                    @endif
                                </div>
                                {{-- CONTENT --}}
                                <div class="p-5">
                                    <h2 class="text-lg font-semibold text-[#212529]">
                                        {{ $galeri->judul }}
                                    </h2>
                                    <div class="mt-2 space-y-2">
                                        {{-- NAMA PENYEWA --}}
                                        <div class="flex items-center gap-2 text-sm text-[#6C757D]">
                                            <i class="ph-fill ph-user text-lg text-albirru-blue"></i>
                                            <span>{{ $galeri->nama_pelanggan }}</span>
                                        </div>
                                        {{-- TANGGAL --}}
                                        <div class="flex items-center gap-2 text-sm text-[#6C757D]">
                                            <i class="ph-fill ph-calendar-dots text-lg text-albirru-blue"></i>
                                            <span>
                                                {{ \Carbon\Carbon::parse($galeri->tanggal_penyewaan)->translatedFormat('d F Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL PREVIEW --}}
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-[999] bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
                                @click="open = false"
                                style="display: none;"
                            >

                                {{-- FRAME MEDIA --}}
                                <div class="w-full max-w-4xl flex items-center justify-center" @click.stop>
                                    @if ($isVideo)
                                        {{-- VIDEO --}}
                                        <video
                                            x-ref="modalVideo"
                                            x-init="$watch('open', value => value ? $refs.modalVideo.play() : $refs.modalVideo.pause())"
                                            controls
                                            playsinline
                                            class="max-w-full max-h-[85vh] rounded-lg shadow-2xl">
                                            <source src="{{ $urlMedia }}" type="video/{{ $extension === 'mov' ? 'mp4' : $extension }}">
                                            <source src="{{ $urlMedia }}" type="video/quicktime">
                                        </video>
                                    @else
                                        {{-- IMAGE ZOOM --}}
                                        <div
                                            x-data="{ scale: 1 }"
                                            @wheel.prevent="scale += $event.deltaY * -0.001"
                                            @dblclick="scale = 1"
                                            class="overflow-hidden rounded-lg"
                                        >
                                            <img
                                                src="{{ $urlMedia }}"
                                                class="max-w-full max-h-[85vh] object-contain transition duration-200"
                                                :style="'transform: scale(' + Math.min(Math.max(scale, 1), 4) + ')'">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- LOAD MORE BUTTON --}}
                @if ($galeris->count() > 6)
                    <div class="mt-14 flex justify-center">
                        <button
                            x-show="visibleItems < {{ $galeris->count() }}"
                            @click="visibleItems += 6"
                            class="px-6 py-3 bg-albirru-blue text-white rounded-xl font-medium hover:bg-blue-700 transition duration-300 shadow-md">
                            Muat Lebih Banyak
                        </button>
                    </div>
                @endif
            </div>
            @else

            {{-- EMPTY STATE --}}
            <div class="flex flex-col items-center justify-center py-14 text-center">
                {{-- ILLUSTRATION --}}
                <div class="items-center justify-center mb-2">
                    <div class="w-56 h-56">
                        {!! file_get_contents(public_path('illustration/image-folder.svg')) !!}
                    </div>
                </div>
                {{-- TITLE --}}
                <h2 class="text-lg font-semibold text-[#212529]">
                    Belum Ada Galeri yang Ditambahkan
                </h2>
                {{-- DESCRIPTION --}}
                <p class="mt-1 text-[#6C757D] max-w-md leading-relaxed">
                    Albirru Trans belum menampilkan galerinya untuk saat ini.
                </p>
            </div>
            @endif

            <!-- {{-- LOAD MORE BUTTON --}}
            @if ($galeris->count() > 6)
                <div class="mt-14 flex justify-center">
                    <button
                        x-show="visibleItems < {{ $galeris->count() }}"
                        @click="visibleItems += 6"
                        class="px-6 py-3 bg-albirru-blue text-white rounded-xl font-medium hover:bg-blue-700 transition duration-300 shadow-md">
                        Muat Lebih Banyak
                    </button>
                </div>
            @endif -->
            </div>
        </div>
    </section>
    @endsection