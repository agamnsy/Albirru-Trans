<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Armada extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nama_bus',
        'kapasitas',
        'deskripsi',
        'foto',
        'status',
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function penyewaans()
    {
        return $this->hasMany(Penyewaan::class);
    }

    protected static function booted()
    {
        static::updating(function ($armada) {

            $original = $armada->getOriginal('foto');

            if ($original && is_string($original)) {
                $decoded = json_decode($original, true);
                $original = is_array($decoded) ? $decoded : [$original];
            }

            $current = $armada->foto ?? [];

            if ($current && is_string($current)) {
                $decoded = json_decode($current, true);
                $current = is_array($decoded) ? $decoded : [$current];
            }

            if ($original && is_array($original)) {
                foreach ($original as $foto) {
                    if (! in_array($foto, $current ?? [])) {
                        Storage::disk('public')->delete($foto);
                    }
                }
            }
        });

        static::deleting(function ($armada) {

            // Kalau hanya soft delete, jangan hapus foto dari storage.
            if (! $armada->isForceDeleting()) {
                return;
            }

            if (is_array($armada->foto)) {
                foreach ($armada->foto as $foto) {
                    Storage::disk('public')->delete($foto);
                }
            }

            if (is_string($armada->foto)) {
                Storage::disk('public')->delete($armada->foto);
            }
        });
    }
}