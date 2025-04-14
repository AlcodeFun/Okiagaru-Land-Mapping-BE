<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 't_provinsi';
    protected $primaryKey = 'id_provinsi';
    public $timestamps = false;

    protected $fillable = ['kode', 'provinsi', 'aktif', 'geom'];

    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'id_provinsi', 'id_provinsi');
    }
}
