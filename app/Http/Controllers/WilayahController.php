<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahController extends Controller
{
    public function getProvinsi()
    {
        return response()->json([
            'data' => Provinsi::where('aktif', 'Ya')->get()
        ]);
    }

    public function getKabupaten($id_provinsi)
    {
        return response()->json([
            'data' => Kabupaten::where('id_provinsi', $id_provinsi)
                ->where('aktif', 'Ya')
                ->get()
        ]);
    }

    public function getKecamatan($id_kabupaten)
    {
        return response()->json([
            'data' => Kecamatan::where('id_kabupaten', $id_kabupaten)
                ->where('aktif', 'Ya')
                ->get()
        ]);
    }

    public function getDesa($id_kecamatan)
    {
        return response()->json([
            'data' => Desa::where('id_kecamatan', $id_kecamatan)
                ->where('aktif', 'Ya')
                ->get()
        ]);
    }
}
