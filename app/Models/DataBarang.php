<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class DataBarang extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'databarangs';

    protected $fillable = [
        'id_barang',
        'nama_barang',
        'stok',
        'id_satuan',
        'id_jenisbarang',
        'id_tenant',
        'lokasi', // Tambahkan lokasi di sini
    ];

    /**
     * Relasi ke model Satuan
     */
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    /**
     * Relasi ke model Jenisbarang
     */
    public function jenisbarang()
    {
        return $this->belongsTo(Jenisbarang::class, 'id_jenisbarang');
    }

    /**
     * Relasi ke model BarangMasuk
     */
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'id_databarang');
    }

    /**
     * Relasi ke model BarangKeluar
     */
    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class, 'id_databarang');
    }
}
