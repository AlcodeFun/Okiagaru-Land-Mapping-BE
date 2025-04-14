<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KarakteristikPilihan extends Model
{
    protected $table = 'karakteristik_pilihan';
    protected $primaryKey = 'id_karakteristik_pilihan';
    public $timestamps = false;

    protected $fillable = [
        'id_lahan',
        'id_karakteristik_lahan',
        'id_nilai_pilihan'
    ];

    public function lahan()
    {
        return $this->belongsTo(Lahan::class, 'id_lahan');
    }

    public function karakteristik()
    {
        return $this->belongsTo(KarakteristikLahan::class, 'id_karakteristik_lahan');
    }

    public function nilaiPilihan()
    {
        return $this->belongsTo(NilaiPilihan::class, 'id_nilai_pilihan');
    }
}
