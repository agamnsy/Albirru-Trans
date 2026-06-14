<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galeri extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'kategori_id',
        'media',
        'judul',
        'tanggal_penyewaan',
        'nama_pelanggan',
        'deskripsi',
    ];

    protected $casts = [
        'media' => 'array',
        'tanggal_penyewaan' => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class)->withTrashed();
    }

    protected static function booted()
    {
        static::updating(function ($galeri) {
            $original = $galeri->getOriginal('media');

            if (is_string($original)) {
                $decoded = json_decode($original, true);
                $original = is_array($decoded) ? $decoded : [$original];
            }

            $current = $galeri->media ?? [];

            if (is_string($current)) {
                $decoded = json_decode($current, true);
                $current = is_array($decoded) ? $decoded : [$current];
            }

            if ($original && is_array($original)) {
                foreach ($original as $file) {
                    if (! in_array($file, $current ?? [])) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        });

        static::deleting(function ($galeri) {
            // Kalau hanya soft delete, media jangan dihapus dari storage.
            if (! $galeri->isForceDeleting()) {
                return;
            }

            if (is_array($galeri->media)) {
                foreach ($galeri->media as $file) {
                    Storage::disk('public')->delete($file);
                }
            }

            if (is_string($galeri->media)) {
                Storage::disk('public')->delete($galeri->media);
            }
        });
    }
}