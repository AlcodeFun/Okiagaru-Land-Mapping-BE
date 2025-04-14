<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KualitasLahan;

class KarakteristikLahan extends Model
{
    protected $table = 'karakteristik_lahan';
    protected $primaryKey = 'id_karakteristik_lahan';
    public $timestamps = false;

    protected $fillable = [
        'id_kualitas_lahan',
        'karakteristik_lahan',
        'jenis_nilai',
        'deskripsi',
        'aktif',
    ];

    public function kualitasLahan()
    {
        return $this->belongsTo(KualitasLahan::class, 'id_kualitas_lahan', 'id_kualitas_lahan');
    }

    public function nilaiPilihan()
{
    return $this->hasMany(NilaiPilihan::class, 'id_karakteristik_lahan');
}

public function karakteristikAngka()
{
    return $this->hasMany(KarakteristikAngka::class, 'id_karakteristik_lahan');
}

public function karakteristikPilihan()
{
    return $this->hasMany(KarakteristikPilihan::class, 'id_karakteristik_lahan');
}
}
