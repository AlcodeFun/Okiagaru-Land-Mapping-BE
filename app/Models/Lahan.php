<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Lahan extends Model
{
    protected $table = 't_lahan';
    protected $primaryKey = 'id_lahan';
    public $timestamps = false;

    protected $fillable = [
        'id_layer_groups',
        'lahan',
        'id_kelas',
        'id_desa',
        'lat',
        'lon',
        'geom',
        'luas',
        'deskripsi',
        'aktif',
    ];

    // ✅ Accessor untuk konversi kolom geom ke GeoJSON
    public function getPolygonAttribute()
    {
        $result = DB::selectOne("SELECT ST_AsGeoJSON(geom) as geojson FROM t_lahan WHERE id_lahan = ?", [$this->id_lahan]);
        return $result ? json_decode($result->geojson, true) : null;
    }

    // ✅ Relasi ke Layer Group (pemilik lahan)
    public function layerGroup()
    {
        return $this->belongsTo(LayerGroup::class, 'id_layer_groups', 'id_layer_groups');
    }

    // ✅ Relasi ke Desa
    public function desa()
    {
        return $this->belongsTo(Desa::class, 'id_desa', 'id_desa');
    }

    // ✅ Shortcut akses ke Kecamatan
    public function kecamatan()
    {
        return $this->desa?->kecamatan;
    }

    // ✅ Shortcut akses ke Kabupaten
    public function kabupaten()
    {
        return $this->desa?->kecamatan?->kabupaten;
    }

    // ✅ Shortcut akses ke Provinsi
    public function provinsi()
    {
        return $this->desa?->kecamatan?->kabupaten?->provinsi;
    }

    public function karakteristikAngka()
{
    return $this->hasMany(KarakteristikAngka::class, 'id_lahan');
}

public function karakteristikPilihan()
{
    return $this->hasMany(KarakteristikPilihan::class, 'id_lahan');
}

}
