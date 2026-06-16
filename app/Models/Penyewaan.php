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
        'jam_mulai',
        'tanggal_selesai',
        'jam_selesai',
        'alamat_penjemputan',
        'tujuan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        // 'jam_mulai' => 'datetime:H:i',
        // 'jam_selesai' => 'datetime:H:i',
    ];

    protected static function booted()
    {
        // Saat status penyewaan berubah
        static::updated(function ($penyewaan) {

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
                }
            }
        });

        // Penyewaan aktif tetap tidak boleh dihapus
        static::deleting(function ($penyewaan) {
            if (
                ! $penyewaan->isForceDeleting()
                && in_array($penyewaan->status, ['pending', 'dikonfirmasi', 'berjalan'])
            ) {
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