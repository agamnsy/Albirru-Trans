<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\Penyewaan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ArmadaController extends Controller
{

    private function normalizeDate($date): ?string
    {
        if (! $date) {
            return null;
        }

        return Carbon::parse($date)->format('Y-m-d');
    }

    private function normalizeTime($time): ?string
    {
        if (! $time) {
            return null;
        }

        return Carbon::parse($time)->format('H:i:s');
    }

    private function combineDateTime($date, $time): ?string
    {
        $date = $this->normalizeDate($date);
        $time = $this->normalizeTime($time);

        if (! $date || ! $time) {
            return null;
        }

        return Carbon::parse("{$date} {$time}")->format('Y-m-d H:i:s');
    }

    private function hasBentrokJadwal(
        $armadaId,
        $tanggalMulai,
        $jamMulai,
        $tanggalSelesai,
        $jamSelesai
    ): bool {
        $waktuMulai = $this->combineDateTime($tanggalMulai, $jamMulai);
        $waktuSelesai = $this->combineDateTime($tanggalSelesai, $jamSelesai);

        if (! $armadaId || ! $waktuMulai || ! $waktuSelesai) {
            return false;
        }

        return Penyewaan::where('armada_id', $armadaId)
            ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
            ->whereRaw(
                "TIMESTAMP(tanggal_mulai, COALESCE(jam_mulai, '00:00:00')) < ?",
                [$waktuSelesai]
            )
            ->whereRaw(
                "TIMESTAMP(tanggal_selesai, COALESCE(jam_selesai, '23:59:59')) > ?",
                [$waktuMulai]
            )
            ->exists();
    }

    public function index(Request $request)
    {
        $tgl_mulai = $request->query('tgl_mulai');
        $tgl_selesai = $request->query('tgl_selesai');

        // Jika salah satu tanggal kosong, armada belum ditampilkan.
        if (! $tgl_mulai || ! $tgl_selesai) {
            $armadas = collect();

            return view('armada', compact('armadas', 'tgl_mulai', 'tgl_selesai'));
        }

        // Menampilkan armada yang status unitnya tersedia.
        // Validasi bentrok jadwal berdasarkan tanggal dan jam dilakukan saat pelanggan mengirim form pemesanan.
        $armadas = Armada::where('status', 'tersedia')
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
        if ($armada->status !== 'tersedia') {
            return back()
                ->withInput()
                ->with('error', 'Armada tidak tersedia untuk dipesan.');
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
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'tujuan' => 'required|string',
            'alamat_penjemputan' => 'required|string',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_selesai' => 'required|date_format:H:i',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nomor_hp.required' => 'Nomor WhatsApp jangan sampai kosong',
            'tujuan.required' => 'Tujuan destinasi harus diisi',
            'alamat_penjemputan.required' => 'Alamat penjemputan mohon dilengkapi',
            'tanggal_mulai.required' => 'Tanggal mulai sewa wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai sewa tidak boleh sebelum hari ini.',
            'jam_mulai.required' => 'Jam mulai sewa wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai sewa wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai sewa tidak boleh mendahului tanggal mulai sewa.',
            'jam_selesai.required' => 'Jam selesai sewa wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid.',
        ]);
        
        $validator->after(function ($validator) use ($request) {
            $waktuMulai = $this->combineDateTime($request->tanggal_mulai, $request->jam_mulai);
            $waktuSelesai = $this->combineDateTime($request->tanggal_selesai, $request->jam_selesai);
        
            if (! $waktuMulai || ! $waktuSelesai) {
                return;
            }
        
            if (Carbon::parse($waktuSelesai)->lessThanOrEqualTo(Carbon::parse($waktuMulai))) {
                $validator->errors()->add('jam_selesai', 'Waktu selesai sewa harus lebih besar dari waktu mulai sewa.');
            }
        });
        
        $validator->validate();

        try {
            DB::beginTransaction();

            // Cek ulang apakah armada sudah memiliki penyewaan aktif yang bentrok dengan tanggal pemesanan.
            // Ini tetap wajib walaupun sebelumnya armada sudah muncul di daftar, karena bisa saja ada data baru masuk sebelum form disubmit.
            $exists = $this->hasBentrokJadwal(
                $id,
                $request->tanggal_mulai,
                $request->jam_mulai,
                $request->tanggal_selesai,
                $request->jam_selesai
            );

            if ($exists) {
                DB::rollBack();
            
                return back()
                    ->withInput()
                    ->withErrors([
                        'jam_mulai' => 'Armada tidak tersedia pada rentang waktu tersebut.',
                        'jam_selesai' => 'Silakan pilih jam sewa lain.',
                    ])
                    ->with('error', 'Armada tidak tersedia pada rentang waktu tersebut. Silakan pilih jam sewa lain.');
            }

            // Cari atau buat pelanggan berdasarkan nomor HP.
            // Catatan: nanti kalau pelanggan pakai soft delete dan nomor HP lama muncul lagi,
            // bisa kita sesuaikan agar data pelanggan lama otomatis restore.
            $pelanggan = Pelanggan::withTrashed()
                ->where('no_hp', $request->nomor_hp)
                ->first();

            if ($pelanggan) {
                if ($pelanggan->trashed()) {
                    $pelanggan->restore();
                }
            } else {
                $pelanggan = Pelanggan::create([
                    'nama' => $request->nama_lengkap,
                    'no_hp' => $request->nomor_hp,
                ]);
            }

            // Simpan penyewaan.
            // Tidak ada update status armada menjadi disewa di sini.
            Penyewaan::create([
                'pelanggan_id' => $pelanggan->id,
                'armada_id' => $id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'jam_mulai' => $this->normalizeTime($request->jam_mulai),
                'tanggal_selesai' => $request->tanggal_selesai,
                'jam_selesai' => $this->normalizeTime($request->jam_selesai),
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

        $mulai = Carbon::parse($request->tanggal_mulai)
            ->locale('id')
            ->translatedFormat('d F Y');

        $selesai = Carbon::parse($request->tanggal_selesai)
            ->locale('id')
            ->translatedFormat('d F Y');

        $jamMulai = Carbon::parse($request->jam_mulai)->format('H:i') . ' WIB';

        $jamSelesai = Carbon::parse($request->jam_selesai)->format('H:i') . ' WIB';

        $pesan = "Halo Albirru Trans,\n\n"
            . "Saya ingin menyewa armada *{$armada->nama_bus}*.\n\n"
            . "*Detail Penyewa:*\n"
            . "• Nama: {$request->nama_lengkap}\n"
            . "• No. HP: {$request->nomor_hp}\n\n"
            . "*Detail Perjalanan:*\n"
            . "• Mulai Sewa: {$mulai}, {$jamMulai}\n"
            . "• Selesai Sewa: {$selesai}, {$jamSelesai}\n"
            . "• Tujuan: {$request->tujuan}\n"
            . "• Penjemputan: {$request->alamat_penjemputan}\n\n"
            . "Mohon informasi selanjutnya, terima kasih.";

        $nomor_admin = "6289635697054";

        $url = "https://wa.me/{$nomor_admin}?text=" . urlencode($pesan);

        return redirect()->away($url);
    }
}