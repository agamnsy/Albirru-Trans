<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

class AktivitasPerjalanan extends Model
{
    use HasUuids;

    protected $fillable = [
        'penyewaan_id',
        'supir_id',
        'status',
        'catatan',
        'foto',
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class)->withTrashed();
    }

    public function supir()
    {
        return $this->belongsTo(User::class, 'supir_id');
    }
    public static function urutanStatus(): array
    {
        return [
            'sampai_penjemputan',
            'mulai_perjalanan',
            'sampai_tujuan',
            'perjalanan_pulang',
            'sampai_garasi',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function ($aktivitas) {
            $original = $aktivitas->getOriginal('foto');

            if (is_string($original)) {
                $decoded = json_decode($original, true);
                $original = is_array($decoded) ? $decoded : [$original];
            }

            $current = $aktivitas->foto ?? [];

            if (is_string($current)) {
                $decoded = json_decode($current, true);
                $current = is_array($decoded) ? $decoded : [$current];
            }

            if (is_array($original)) {
                foreach ($original as $foto) {
                    if (!in_array($foto, $current)) {
                        Storage::disk('public')->delete($foto);
                    }
                }
            }
        });

        static::deleting(function ($aktivitas) {
            $fotos = $aktivitas->foto ?? [];

            if (is_string($fotos)) {
                $decoded = json_decode($fotos, true);
                $fotos = is_array($decoded) ? $decoded : [$fotos];
            }

            if (is_array($fotos)) {
                foreach ($fotos as $foto) {
                    Storage::disk('public')->delete($foto);
                }
            }
        });
    }
}
