<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class Satuan extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'satuans';

    protected $fillable = [
        'nama',
        'id_tenant',
    ];

    public function databarangs()
    {
        return $this->hasMany(DataBarang::class, 'id_satuan');
    }
}
