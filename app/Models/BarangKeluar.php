<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant; // Import trait

class BarangKeluar extends Model
{
    use HasFactory, HasTenant; // Tambahkan HasTenant

    protected $table = 'barangkeluars';

    protected $fillable = [
        'no_barangkeluar',
        'tanggal',
        'id_databarang',
        'jumlah_keluar',
        'id_tenant',
    ];

    public function dataBarang()
    {
        return $this->belongsTo(DataBarang::class, 'id_databarang');
    }
}
