<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 't_kabupaten';
    protected $primaryKey = 'id_kabupaten';
    public $timestamps = false;

    protected $fillable = ['id_provinsi', 'kode', 'kabupaten', 'aktif'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'id_kabupaten', 'id_kabupaten');
    }
}
