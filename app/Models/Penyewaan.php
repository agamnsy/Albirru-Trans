<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyewaan extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';
    
    protected $fillable = [
        'pelanggan_id',
        'armada_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'alamat_penjemputan',
        'tujuan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected static function booted()
    {
        // 1. SAAT DATA BARU DIBUAT
        static::created(function ($penyewaan) {
            if (in_array($penyewaan->status, ['selesai', 'dibatalkan'])) {
                $penyewaan->armada()->update([
                    'status' => 'tersedia',
                ]);
            } else {
                $penyewaan->armada()->update([
                    'status' => 'disewa',
                ]);
            }
        });

        // 2. SAAT DATA SEDANG DIUBAH (Penting untuk menangani tukar bus)
        static::updating(function ($penyewaan) {
            // Jika admin mengganti unit armada (tukar bus)
            if ($penyewaan->isDirty('armada_id')) {
                $idLama = $penyewaan->getOriginal('armada_id');
                // Kembalikan bus lama ke status tersedia
                \App\Models\Armada::where('id', $idLama)->update(['status' => 'tersedia']);
                // Bus baru akan dihandle di event updated atau manual di sini
                \App\Models\Armada::where('id', $penyewaan->armada_id)->update(['status' => 'disewa']);
            }
        });

        // 3. SAAT DATA SELESAI DIUBAH (Update status)
        static::updated(function ($penyewaan) {

            // Jika status penyewaan berubah
            if ($penyewaan->wasChanged('status')) {
        
                // Jika penyewaan menjadi selesai
                if ($penyewaan->status === 'selesai') {
                    $penugasan = $penyewaan->penugasanSupirs()
                        ->whereIn('status', ['ditugaskan', 'diterima'])
                        ->latest()
                        ->first();
                
                    if ($penugasan) {
                        if ($penugasan->status === 'ditugaskan') {
                            $penugasan->update([
                                'status' => 'diterima',
                                'responded_at' => $penugasan->responded_at ?? now(),
                            ]);
                        }
                
                        $penugasan->supir?->update([
                            'status' => 'aktif',
                        ]);
                    }
                
                    $penyewaan->armada()->update([
                        'status' => 'tersedia',
                    ]);
                }
        
                // Jika penyewaan dibatalkan
                elseif ($penyewaan->status === 'dibatalkan') {
                    $penugasans = $penyewaan->penugasanSupirs()
                        ->whereIn('status', ['ditugaskan', 'diterima'])
                        ->get();
                
                    foreach ($penugasans as $penugasan) {
                        $penugasan->update([
                            'status' => 'dibatalkan',
                            'responded_at' => $penugasan->responded_at ?? now(),
                        ]);
                
                        $penugasan->supir?->update([
                            'status' => 'aktif',
                        ]);
                    }
                
                    $penyewaan->armada()->update([
                        'status' => 'tersedia',
                    ]);
                }
        
                // Jika penyewaan masih aktif
                elseif (in_array($penyewaan->status, ['pending', 'dikonfirmasi', 'berjalan'])) {
        
                    // Armada dianggap sedang digunakan/dipesan
                    $penyewaan->armada()->update([
                        'status' => 'disewa',
                    ]);
                }

            }
        });

        // 4. Saat data penyewaan dihapus
        static::deleted(function ($penyewaan) {
            if ($penyewaan->armada) {
                $penyewaan->armada()->update([
                    'status' => 'tersedia',
                ]);
            }
        });

        static::deleting(function ($penyewaan) {
            if (in_array($penyewaan->status, ['pending', 'dikonfirmasi', 'berjalan'])) {
                throw new \Exception('Penyewaan aktif tidak dapat dihapus.');
            }
        });
    }

    public function armada()
    {
        return $this->belongsTo(Armada::class)->withTrashed();
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class)->withTrashed();
    }

    public function penugasanSupirs()
    {
        return $this->hasMany(PenugasanSupir::class);
    }

    public function penugasanAktif()
    {
        return $this->hasOne(PenugasanSupir::class)
            ->whereIn('status', [
                'ditugaskan',
                'diterima',
            ]);
    }

    public function aktivitasPerjalanans()
    {
        return $this->hasMany(AktivitasPerjalanan::class);
    }
}