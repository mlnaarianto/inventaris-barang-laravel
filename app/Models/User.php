<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'status',
        'google_id',
        'avatar',
        'otp',
        'otp_expires_at',
        'id_tenant',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant');
    }

    public function getAvatarUrlAttribute()
    {
        if (!empty($this->avatar)) {
            return Storage::url($this->avatar);
        }

        return asset('images/1.jpg');
    }
}