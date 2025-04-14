<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Basemap;

class BasemapController extends Controller
{
    // ✅ GET: Semua data basemap (public)
    public function index()
    {
        $basemaps = Basemap::where('aktif', 'Ya')->get();


        return response()->json([
            'message' => 'success',
            'data' => $basemaps
        ]);
    }
}
