<?php
namespace App\Http\Controllers;

use App\Models\LayerGroup;
use App\Models\Lahan;
use App\Models\KarakteristikLahan;
use App\Models\Basemap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil statistik yang dibutuhkan
        $totalLayerGroups = LayerGroup::count();
        $totalLahan = Lahan::count();
        $totalKarakteristikLahan = KarakteristikLahan::count();
        $totalBasemaps = Basemap::count();

        // Mengambil 5 pemilik lahan terbaru dengan kolom 'foto' dan 'telepon' dari pengguna
        $layerGroupsTerbaru = LayerGroup::with(['pengguna' => function($query) {
            $query->select('username', 'foto', 'telepon');  // Ambil foto dan telepon pengguna
        }])
        ->orderByDesc('id_layer_groups')
        ->take(5)
        ->get(['id_layer_groups', 'layer_groups', 'username']);  // Hanya ambil kolom yang dibutuhkan

        // Mengambil 5 lahan terbaru
        $lahanTerbaru = Lahan::with('layerGroup')  // Ambil informasi layer group (pemilik lahan)
            ->orderByDesc('id_lahan')
            ->take(5)
            ->get(['id_lahan', 'lahan', 'id_layer_groups']);  // Hanya ambil kolom yang dibutuhkan

        return response()->json([
            'total_layer_groups' => $totalLayerGroups,
            'total_lahan' => $totalLahan,
            'total_karakteristik_lahan' => $totalKarakteristikLahan,
            'total_basemaps' => $totalBasemaps,
            'layer_groups_terbaru' => $layerGroupsTerbaru,
            'lahan_terbaru' => $lahanTerbaru,
        ]);
    }

    public function owner(Request $request)
    {
        // Mengambil data user yang sedang login
        $user = Auth::user();
        $isOwner = optional($user->rolePengguna->role)->role === 'Owner';

        // Mengambil statistik yang dibutuhkan
        $totalLahan = 0;
        $totalKarakteristikLahan = KarakteristikLahan::count();
        $totalBasemaps = Basemap::count();

        if ($isOwner) {
            $layerGroup = $user->layerGroups->first(); // Ambil layer group pertama pemilik

            if ($layerGroup) {
                // Mengambil total lahan milik owner berdasarkan layer group mereka
                $totalLahan = Lahan::where('id_layer_groups', $layerGroup->id_layer_groups)->count();

                // Mengambil 5 lahan terbaru milik owner berdasarkan layer group
                $lahanTerbaru = Lahan::with('layerGroup')
                    ->where('id_layer_groups', $layerGroup->id_layer_groups)
                    ->orderByDesc('id_lahan')
                    ->take(5)
                    ->get(['id_lahan', 'lahan', 'id_layer_groups']);  // Mengambil kolom yang dibutuhkan saja
            } else {
                // Jika Owner tidak memiliki layer group, kembalikan data kosong
                return response()->json([
                    'message' => 'Owner tidak memiliki layer group.',
                    'data' => [],
                    'meta' => [
                        'total' => 0,
                    ],
                ]);
            }
        } else {
            // Jika bukan Owner, kembalikan data kosong atau error lainnya
            return response()->json([
                'message' => 'Akses ditolak, hanya Owner yang dapat mengakses data ini.',
            ], 403);
        }

        // Mengembalikan response
        return response()->json([
            'total_lahan' => $totalLahan,
            'total_karakteristik_lahan' => $totalKarakteristikLahan,
            'total_basemaps' => $totalBasemaps,
            'lahan_terbaru' => $lahanTerbaru,
        ]);
    }
}
