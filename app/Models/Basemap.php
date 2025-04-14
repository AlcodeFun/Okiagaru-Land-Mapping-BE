<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basemap extends Model
{
    protected $table = 't_basemap';
    protected $primaryKey = 'id_basemap';
    public $timestamps = false;

    protected $fillable = [
        'judul', 'basemap', 'deskripsi', 'aktif'
    ];
}
