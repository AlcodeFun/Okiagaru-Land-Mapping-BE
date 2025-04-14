<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KarakteristikLahan;

class KualitasLahan extends Model
{
    protected $table = 'kualitas_lahan';
    protected $primaryKey = 'id_kualitas_lahan';
    public $timestamps = false;

    protected $fillable = [
        'kualitas_lahan',
        'deskripsi',
        'aktif',
    ];

    public function karakteristik()
    {
        return $this->hasMany(KarakteristikLahan::class, 'id_kualitas_lahan', 'id_kualitas_lahan');
    }
}
