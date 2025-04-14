<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LahanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_lahan' => $this->id_lahan,
            'lahan' => $this->lahan,
            'id_layer_groups' => $this->id_layer_groups,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'luas' => $this->luas,
            'deskripsi' => $this->deskripsi,
            'aktif' => $this->aktif,
            'polygon' => $this->polygon,
    
        
            'desa' => $this->desa?->desa,
            'kecamatan' => $this->kecamatan()?->kecamatan,
            'kabupaten' => $this->kabupaten()?->kabupaten,
            'provinsi' => $this->provinsi()?->provinsi,
    
            'id_desa' => $this->id_desa,
            'id_kecamatan' => $this->desa?->id_kecamatan,
            'id_kabupaten' => $this->kecamatan()?->id_kabupaten,
            'id_provinsi' => $this->kabupaten()?->id_provinsi,
        ];
    }
    
}
