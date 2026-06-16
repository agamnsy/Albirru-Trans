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

        // Jika salah satu tanggal kosong, armada belum ditampilkan.
        if (! $tgl_mulai || ! $tgl_selesai) {
            $armadas = collect();

            return view('armada', compact('armadas', 'tgl_mulai', 'tgl_selesai'));
        }

        // Menampilkan armada yang status unitnya tersedia
        // dan tidak memiliki penyewaan aktif yang bentrok pada tanggal tersebut.
        $armadas = Armada::where('status', 'tersedia')
            ->whereDoesntHave('penyewaans', function ($query) use ($tgl_mulai, $tgl_selesai) {
                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereDate('tanggal_mulai', '<=', $tgl_selesai)
                    ->whereDate('tanggal_selesai', '>=', $tgl_mulai);
            })
            ->get();

        return view('armada', compact('armadas', 'tgl_mulai', 'tgl_selesai'));
    }

    public function show(string $id, Request $request)
    {
        $armada = Armada::findOrFail($id);

        // Validasi tanggal dari query URL.
        // Detail armada tidak boleh dibuka tanpa tanggal sewa.
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

        // Cek apakah armada sudah memiliki penyewaan aktif
        // yang bentrok dengan tanggal yang dipilih.
        $isBooked = $armada->penyewaans()
            ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
            ->whereDate('tanggal_mulai', '<=', $tgl_selesai)
            ->whereDate('tanggal_selesai', '>=', $tgl_mulai)
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

        // Di konsep baru, status armada hanya menandakan kondisi unit.
        // Jadi yang ditolak di awal hanya armada yang bukan tersedia,
        // misalnya maintenance.
        if ($armada->status !== 'tersedia') {
            return redirect()->route('armada.index')
                ->with('error', 'Tidak ada armada yang tersedia untuk dipesan.');
        }

        // Validasi input form pemesanan.
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'tujuan' => 'required|string',
            'alamat_penjemputan' => 'required|string',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nomor_hp.required' => 'Nomor WhatsApp jangan sampai kosong',
            'tujuan.required' => 'Tujuan destinasi harus diisi',
            'alamat_penjemputan.required' => 'Alamat penjemputan mohon dilengkapi',
            'tanggal_mulai.required' => 'Tanggal mulai sewa wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai sewa tidak boleh sebelum hari ini.',
            'tanggal_selesai.required' => 'Tanggal selesai sewa wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai sewa tidak boleh mendahului tanggal mulai sewa.',
        ]);

        try {
            DB::beginTransaction();

            // Cek ulang apakah armada sudah memiliki penyewaan aktif yang bentrok dengan tanggal pemesanan.
            // Ini tetap wajib walaupun sebelumnya armada sudah muncul di daftar, karena bisa saja ada data baru masuk sebelum form disubmit.
            $exists = Penyewaan::where('armada_id', $id)
                ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                ->whereDate('tanggal_mulai', '<=', $request->tanggal_selesai)
                ->whereDate('tanggal_selesai', '>=', $request->tanggal_mulai)
                ->exists();

            if ($exists) {
                DB::rollBack();

                return redirect()->route('armada.index')
                    ->with('error', 'Armada tidak tersedia pada tanggal tersebut.');
            }

            // Cari atau buat pelanggan berdasarkan nomor HP.
            // Catatan: nanti kalau pelanggan pakai soft delete dan nomor HP lama muncul lagi,
            // bisa kita sesuaikan agar data pelanggan lama otomatis restore.
            $pelanggan = Pelanggan::firstOrCreate(
                ['no_hp' => $request->nomor_hp],
                ['nama' => $request->nama_lengkap]
            );

            // Simpan penyewaan.
            // Tidak ada update status armada menjadi disewa di sini.
            Penyewaan::create([
                'pelanggan_id' => $pelanggan->id,
                'armada_id' => $id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'tujuan' => $request->tujuan,
                'alamat_penjemputan' => $request->alamat_penjemputan,
                'status' => 'pending',
            ]);

            DB::commit();

            return $this->redirectToWhatsApp($request, $id);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan pesanan.');
        }
    }

    protected function redirectToWhatsApp($request, $id)
    {
        $armada = Armada::find($id);

        $mulai = \Carbon\Carbon::parse($request->tanggal_mulai)
            ->locale('id')
            ->translatedFormat('d F Y');

        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai)
            ->locale('id')
            ->translatedFormat('d F Y');

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

        $nomor_admin = "6289635697054";

        $url = "https://wa.me/{$nomor_admin}?text=" . urlencode($pesan);

        return redirect()->away($url);
    }
}