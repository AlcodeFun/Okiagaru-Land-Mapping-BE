<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 't_kecamatan';
    protected $primaryKey = 'id_kecamatan';
    public $timestamps = false;

    protected $fillable = ['id_kabupaten', 'kode', 'kecamatan', 'aktif'];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten', 'id_kabupaten');
    }

    public function desa()
    {
        return $this->hasMany(Desa::class, 'id_kecamatan', 'id_kecamatan');
    }
}
