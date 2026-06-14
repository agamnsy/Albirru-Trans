<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
        /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_hp',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSupir(): bool
    {
        return $this->role === 'supir';
    }

    public function penugasanSupirs()
    {
        return $this->hasMany(PenugasanSupir::class, 'supir_id');
    }

    public function penugasanAktif()
    {
        return $this->hasOne(PenugasanSupir::class, 'supir_id')
            ->whereIn('status', [
                'ditugaskan',
                'diterima',
        ])
        ->whereHas('penyewaan', function ($query) {
            $query->whereNotIn('status', [
                'selesai',
                'dibatalkan',
            ]);
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }

        if ($panel->getId() === 'supir') {
            return $this->role === 'supir'
                && in_array($this->status, ['aktif', 'bertugas']);
        }

        return false;
    }

    public function aktivitasPerjalanans()
    {
        return $this->hasMany(AktivitasPerjalanan::class, 'supir_id');
    }
}
