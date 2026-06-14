<?php

namespace App\Models;

use App\Models\AktivitasPerjalanan;
use App\Models\Penyewaan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PenugasanSupir extends Model
{
    use HasUuids;

    protected $fillable = [
        'penyewaan_id',
        'supir_id',
        'status',
        'alasan_penolakan',
        'assigned_at',
        'responded_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class)->withTrashed();
    }

    public function supir()
    {
        return $this->belongsTo(User::class, 'supir_id')->withTrashed();
    }

    public function aktivitasPerjalanans()
    {
        return $this->hasMany(AktivitasPerjalanan::class, 'penyewaan_id', 'penyewaan_id');
    }

    public function isDitugaskan(): bool
    {
        return $this->status === 'ditugaskan';
    }

    public function isDiterima(): bool
    {
        return $this->status === 'diterima';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }
}
