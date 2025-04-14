<?php

namespace App\Http\Controllers;

use App\Models\KualitasLahan;
use Illuminate\Http\Request;

class KualitasLahanController extends Controller
{
    // ✅ Menampilkan semua kualitas lahan beserta karakteristik
    public function index()
{
    $kualitas = KualitasLahan::with(['karakteristik' => function($q) {
        $q->select(
            'id_karakteristik_lahan',
            'id_kualitas_lahan',
            'karakteristik_lahan',
            'jenis_nilai',
            'deskripsi',
            'aktif'
        );
    }])->get();

    return response()->json([
        'message' => 'success',
        'data' => $kualitas
    ]);
}


    // ✅ Menampilkan kualitas lahan berdasarkan ID beserta karakteristik
    public function show($id)
    {
        $data = KualitasLahan::with('karakteristikLahan')->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Kualitas lahan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }
}
