<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nama_perusahaan',
        'kode',
        'email',
        'telepon',
        'alamat',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_tenant');
    }
}