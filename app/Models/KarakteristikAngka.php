<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KarakteristikAngka extends Model
{
    protected $table = 'karakteristik_angka';
    protected $primaryKey = 'id_karakteristik_angka';
    public $timestamps = false;

    protected $fillable = [
        'id_lahan',
        'id_karakteristik_lahan',
        'nilai_angka'
    ];

    public function lahan()
    {
        return $this->belongsTo(Lahan::class, 'id_lahan');
    }

    public function karakteristik()
    {
        return $this->belongsTo(KarakteristikLahan::class, 'id_karakteristik_lahan');
    }
}
