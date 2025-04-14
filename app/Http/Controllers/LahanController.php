<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Models\LayerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\LahanResource;

class LahanController extends Controller
{
    // ✅ GET: Semua data lahan (public)
    public function index(Request $request)
{
    $query = Lahan::with('desa.kecamatan.kabupaten.provinsi');

    // ✅ Jika user login & role-nya "Owner", hanya ambil data lahan milik mereka
    $user = Auth::user();
    $isOwner = optional($user->rolePengguna->role)->role === 'Owner';

    
    if ($isOwner) {
        $layerGroup = $user->layerGroups->first(); 
        if ($layerGroup) {
            $query->where('id_layer_groups', $layerGroup->id_layer_groups);
        } else {
            // Tidak punya layer group, langsung return kosong
            return response()->json([
                'message' => 'success',
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'current_page' => 1,
                    'per_page' => $request->input('per_page', 10),
                ],
            ]);
        }
    }

    // 🔍 Global Search
    if ($request->has('search') && $request->search !== '') {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('lahan', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%")
              ->orWhere('aktif', 'like', "%{$search}%")
              ->orWhereHas('desa', function ($q) use ($search) {
                  $q->where('desa', 'like', "%{$search}%")
                    ->orWhereHas('kecamatan', function ($q) use ($search) {
                        $q->where('kecamatan', 'like', "%{$search}%");
                    })->orWhereHas('kecamatan.kabupaten', function ($q) use ($search) {
                        $q->where('kabupaten', 'like', "%{$search}%");
                    })->orWhereHas('kecamatan.kabupaten.provinsi', function ($q) use ($search) {
                        $q->where('provinsi', 'like', "%{$search}%");
                    });
              });
        });
    }

    // ⬇️ Sorting
    $sortColumn = $request->input('sort_column', 'id_lahan');
    $sortDirection = $request->input('sort_direction', 'asc');
    $allowedSortColumns = ['id_lahan', 'lahan', 'deskripsi', 'aktif', 'luas'];

    if (in_array($sortColumn, $allowedSortColumns)) {
        $query->orderBy($sortColumn, $sortDirection);
    }

    // 📄 Pagination
    $perPage = $request->input('per_page', 10);
    $data = $query->paginate($perPage);

    return response()->json([
        'message' => 'success',
        'data' => LahanResource::collection($data),
        'meta' => [
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ],
    ]);
}

    


    // ✅ GET: Detail lahan by ID (public)
    public function show($id)
    {
        $lahan = Lahan::with('desa.kecamatan.kabupaten.provinsi')->find($id);
        if (!$lahan) {
            return response()->json(['message' => 'Lahan tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => new LahanResource($lahan)
        ]);
    }

    // ✅ GET: Semua lahan by Layer Group ID (public)
    public function getByLayerGroup($id)
    {
        $lahan = Lahan::where('id_layer_groups', $id)
            ->with('desa.kecamatan.kabupaten.provinsi')
            ->get();
    
        if ($lahan->isEmpty()) {
            return response()->json([
                'message' => 'Data lahan tidak ditemukan untuk Layer Group tersebut.'
            ], 404);
        }
    
        return response()->json([
            'message' => 'success',
            'data' => LahanResource::collection($lahan)
        ]);
    }
    
    // ✅ POST: Tambah lahan (admin atau owner)
    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
    
        if ($isAdmin) {
            $request->validate([
                'id_layer_groups' => 'required|exists:t_layer_groups,id_layer_groups',
            ]);
            $idLayerGroup = $request->id_layer_groups;
        } else {
            $layerGroup = $user->layerGroups->first();
            if (!$layerGroup) {
                return response()->json(['message' => 'Owner belum memiliki layer group'], 403);
            }
            $idLayerGroup = $layerGroup->id_layer_groups;
        }
    
        $request->validate([
            'lahan'=>'required|string',
            'id_desa' => 'required|exists:t_desa,id_desa',
            'luas' => 'required|numeric',
            'deskripsi' => 'required|string',
            'aktif' => 'required|string|max:5',
            'polygon' => 'required|array|min:3'
        ]);
    
        $polygonArray = $request->polygon;
        if ($polygonArray[0] !== end($polygonArray)) {
            $polygonArray[] = $polygonArray[0];
        }
    
        $geoJson = json_encode([
            'type' => 'Polygon',
            'coordinates' => [$polygonArray]
        ], JSON_NUMERIC_CHECK);
    
        $polygon = DB::raw("ST_GeomFromGeoJSON('$geoJson')");
        $lon = DB::select("SELECT ST_X(ST_Centroid(ST_GeomFromGeoJSON(?))) as lon", [$geoJson])[0]->lon;
        $lat = DB::select("SELECT ST_Y(ST_Centroid(ST_GeomFromGeoJSON(?))) as lat", [$geoJson])[0]->lat;
    
        $lahan = Lahan::create([
            'id_layer_groups' => $idLayerGroup,
            'id_kelas'=>1,
            'lahan' => $request->lahan,
            'id_desa' => $request->id_desa,
            'lat' => $lat,
            'lon' => $lon,
            'geom' => $polygon,
            'luas' => $request->luas,
            'deskripsi' => $request->deskripsi,
            'aktif' => $request->aktif
        ]);
    
        return response()->json([
            'message' => 'success',
            'data' => new LahanResource($lahan)
        ]);
    }
    

    // ✅ PUT: Update lahan (admin atau owner)
    public function update(Request $request, $id)
    {
        $lahan = Lahan::find($id);
        if (!$lahan) {
            return response()->json(['message' => 'Lahan tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $isOwner = $user->username === optional($lahan->layerGroup)->username;

        if (!$isAdmin && !$isOwner) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id_desa' => 'sometimes|exists:t_desa,id_desa',
            'lahan' => 'sometimes|string',
            'luas' => 'sometimes|numeric',
            'deskripsi' => 'sometimes|string',
            'aktif' => 'sometimes|string|max:5',
            'polygon' => 'sometimes|array|min:3'
        ]);

        $updateData = $request->only(['id_desa','lahan', 'luas', 'deskripsi', 'aktif']);

        if ($request->has('polygon')) {
            $polygonArray = $request->polygon;
            if ($polygonArray[0] !== end($polygonArray)) {
                $polygonArray[] = $polygonArray[0];
            }

            $geoJson = json_encode([
                'type' => 'Polygon',
                'coordinates' => [$polygonArray]
            ], JSON_NUMERIC_CHECK);

            $updateData['geom'] = DB::raw("ST_GeomFromGeoJSON('$geoJson')");
            $updateData['lon'] = DB::select("SELECT ST_X(ST_Centroid(ST_GeomFromGeoJSON(?))) as lon", [$geoJson])[0]->lon;
            $updateData['lat'] = DB::select("SELECT ST_Y(ST_Centroid(ST_GeomFromGeoJSON(?))) as lat", [$geoJson])[0]->lat;
        }

        $lahan->update($updateData);

        return response()->json([
            'message' => 'success',
            'data' => new LahanResource($lahan)
        ]);
    }

    // ✅ DELETE: Hapus lahan (admin atau owner)
    public function destroy($id)
    {
        $lahan = Lahan::find($id);
        if (!$lahan) {
            return response()->json(['message' => 'Lahan tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $isOwner = $user->username === optional($lahan->layerGroup)->username;

        if (!$isAdmin && !$isOwner) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lahan->delete();

        return response()->json(['message' => 'Lahan berhasil dihapus']);
    }
}
