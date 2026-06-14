<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\Penyewaan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArmadaController extends Controller
{
    public function index(Request $request)
    {
        $tgl_mulai = $request->query('tgl_mulai');
        $tgl_selesai = $request->query('tgl_selesai');

        // Jika salah satu tanggal kosong, kirim koleksi kosong
        if (!$tgl_mulai || !$tgl_selesai) {
            $armadas = collect();
            return view('armada', compact('armadas', 'tgl_mulai', 'tgl_selesai'));
        }

        // Jika ada tanggal, baru jalankan query ketersediaan
        $armadas = Armada::where('status', 'tersedia')
            ->whereDoesntHave('penyewaans', function ($q) use ($tgl_mulai, $tgl_selesai) {
                $q->where(function ($inner) use ($tgl_mulai, $tgl_selesai) {
                    $inner->whereBetween('tanggal_mulai', [$tgl_mulai, $tgl_selesai])
                        ->orWhereBetween('tanggal_selesai', [$tgl_mulai, $tgl_selesai])
                        ->orWhere(function ($deep) use ($tgl_mulai, $tgl_selesai) {
                            $deep->where('tanggal_mulai', '<=', $tgl_mulai)
                                ->where('tanggal_selesai', '>=', $tgl_selesai);
                        });
                })->whereIn('status', ['pending', 'dikonfirmasi']);
            })->get();

        return view('armada', compact('armadas', 'tgl_mulai', 'tgl_selesai'));
    }

    // public function show(string $id, Request $request)
    // {
    //     $armada = Armada::findOrFail($id);

    //     if ($armada->status !== 'tersedia' || $armada->penyewaans()->whereIn('status', ['pending', 'dikonfirmasi'])->exists()) {
    //         return redirect()->route('armada.index')->with('error', 'Armada tidak tersedia untuk disewa.');
    //     }

    //     $tgl_mulai = $request->query('tgl_mulai');
    //     $tgl_selesai = $request->query('tgl_selesai');

    //     return view('detail', compact('armada', 'tgl_mulai', 'tgl_selesai'));
    // }
    public function show(string $id, Request $request)
    {
        $armada = Armada::findOrFail($id);

        // 🔥 VALIDASI QUERY PARAM (WAJIB)
        $validator = Validator::make($request->all(), [
            'tgl_mulai' => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        if ($validator->fails()) {
            return redirect()->route('armada.index')
                ->with('error', 'Silakan pilih tanggal sewa terlebih dahulu.');
        }

        $tgl_mulai = $request->query('tgl_mulai');
        $tgl_selesai = $request->query('tgl_selesai');

        // 🔥 CEK KETERSEDIAAN BERDASARKAN TANGGAL (INI YANG BENAR)
        $isBooked = $armada->penyewaans()
            ->whereIn('status', ['pending', 'dikonfirmasi'])
            ->where(function ($q) use ($tgl_mulai, $tgl_selesai) {
                $q->whereBetween('tanggal_mulai', [$tgl_mulai, $tgl_selesai])
                ->orWhereBetween('tanggal_selesai', [$tgl_mulai, $tgl_selesai])
                ->orWhere(function ($q2) use ($tgl_mulai, $tgl_selesai) {
                    $q2->where('tanggal_mulai', '<=', $tgl_mulai)
                        ->where('tanggal_selesai', '>=', $tgl_selesai);
                });
            })
            ->exists();

        if ($armada->status !== 'tersedia' || $isBooked) {
            return redirect()->route('armada.index')
                ->with('error', 'Armada tidak tersedia pada tanggal tersebut.');
        }

        return view('detail', compact('armada', 'tgl_mulai', 'tgl_selesai'));
    }

    public function store(Request $request, $id)
    {
        $armada = Armada::findOrFail($id);

        if ($armada->status !== 'tersedia' || $armada->penyewaans()->whereIn('status', ['pending', 'dikonfirmasi'])->exists()) {
            return redirect()->route('armada.index')->with('error', 'Armada tidak tersedia untuk disewa.');
        }

        // 1. Validasi
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'tujuan' => 'required|string',
            'alamat_penjemputan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            // Daftar pesan kustom dalam bahasa Indonesia
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nomor_hp.required' => 'Nomor WhatsApp jangan sampai kosong',
            'tujuan.required' => 'Tujuan destinasi harus diisi',
            'alamat_penjemputan.required' => 'Alamat penjemputan mohon dilengkapi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai sewa tidak boleh mendahului tanggal mulai sewa.',
        ]);

        try {
            DB::beginTransaction();

            // 2. Simpan/Cari Pelanggan (Sesuaikan dengan fillable model: nama, no_hp)
            $pelanggan = Pelanggan::firstOrCreate(
                ['no_hp' => $request->nomor_hp],
                ['nama' => $request->nama_lengkap]
            );

            $exists = Penyewaan::where('armada_id', $id)
                ->whereIn('status', ['pending', 'dikonfirmasi'])
                ->where(function ($q) use ($request) {
                    $q->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                        ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai])
                        ->orWhere(function ($q2) use ($request) {
                            $q2->where('tanggal_mulai', '<=', $request->tanggal_mulai)
                                ->where('tanggal_selesai', '>=', $request->tanggal_selesai);
                        });
                })
                ->exists();

            if ($exists) {
                return redirect()->route('armada.index')->with('error', 'Armada tidak tersedia untuk disewa.');
            }

            // 3. Simpan Penyewaan
            Penyewaan::create([
                'pelanggan_id' => $pelanggan->id,
                'armada_id' => $id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'tujuan' => $request->tujuan,
                'alamat_penjemputan' => $request->alamat_penjemputan,
                'status' => 'pending', // Sesuai default enum di migration kamu
            ]);

            DB::commit();

            // 4. Redirect ke WhatsApp
            return $this->redirectToWhatsApp($request, $id);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error untuk debug jika perlu: \Log::error($e->getMessage());
            return back()->with('error', 'Gagal menyimpan pesanan.');
        }
    }

    // TAMBAHKAN INI DI BAWAH METHOD STORE
    protected function redirectToWhatsApp($request, $id)
    {
        $armada = Armada::find($id);

        // Format tanggal agar lebih enak dibaca di pesan WA
        $mulai = \Carbon\Carbon::parse($request->tanggal_mulai)->locale('id')->translatedFormat('d F Y');
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai)->locale('id')->translatedFormat('d F Y');

        $pesan = "Halo Albirru Trans,\n\n"
            . "Saya ingin menyewa armada *{$armada->nama_bus}*.\n\n"
            . "*Detail Penyewa:*\n"
            . "• Nama: {$request->nama_lengkap}\n"
            . "• No. HP: {$request->nomor_hp}\n\n"
            . "*Detail Perjalanan:*\n"
            . "• Tanggal: {$mulai} s/d {$selesai}\n"
            . "• Tujuan: {$request->tujuan}\n"
            . "• Penjemputan: {$request->alamat_penjemputan}\n\n"
            . "Mohon informasi selanjutnya, terima kasih.";

        // Ganti nomor ini dengan nomor WA Admin Albirru Trans yang aktif
        $nomor_admin = "6289635697054";

        $url = "https://wa.me/{$nomor_admin}?text=" . urlencode($pesan);

        // window . open(waUrl, '_blank');

        return redirect()->away($url);
    }
    // Kurung kurawal penutup class ArmadaController
}
