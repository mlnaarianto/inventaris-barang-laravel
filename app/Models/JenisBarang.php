<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class JenisBarang extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'jenisbarangs';

    protected $fillable = [
        'jenis_barang',
        'id_tenant',
    ];

    public function databarangs()
    {
        return $this->hasMany(DataBarang::class, 'id_jenisbarang');
    }
}
