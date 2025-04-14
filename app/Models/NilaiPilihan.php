<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiPilihan extends Model
{
    protected $table = 'nilai_pilihan';
    protected $primaryKey = 'id_nilai_pilihan';
    public $timestamps = false;

    protected $fillable = [
        'id_karakteristik_lahan',
        'pilihan'
    ];

    public function karakteristik()
    {
        return $this->belongsTo(KarakteristikLahan::class, 'id_karakteristik_lahan');
    }

    public function karakteristikPilihan()
    {
        return $this->hasMany(KarakteristikPilihan::class, 'id_nilai_pilihan');
    }
}
