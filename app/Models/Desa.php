<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 't_desa';
    protected $primaryKey = 'id_desa';
    public $timestamps = false;

    protected $fillable = ['id_kecamatan', 'kode', 'desa', 'aktif'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }

    public function lahan()
    {
        return $this->hasMany(Lahan::class, 'id_desa', 'id_desa');
    }
}
