<?php

namespace App\Http\Controllers;

use App\Models\KarakteristikLahan;
use Illuminate\Http\Request;

class KarakteristikLahanController extends Controller
{
    // ✅ Menampilkan semua karakteristik lahan
    public function index()
    {
        $karakteristik = KarakteristikLahan::with('kualitasLahan')->get();

        return response()->json([
            'message' => 'success',
            'data' => $karakteristik
        ]);
    }

    // ✅ Menampilkan detail karakteristik lahan berdasarkan ID
    public function show($id)
    {
        $karakteristik = KarakteristikLahan::with('kualitasLahan')->find($id);

        if (!$karakteristik) {
            return response()->json([
                'message' => 'Karakteristik lahan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => $karakteristik
        ]);
    }
}
