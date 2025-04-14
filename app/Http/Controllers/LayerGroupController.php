<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LayerGroup;
use App\Models\Pengguna;
use App\Models\RolePengguna;
use App\Models\Role;

class LayerGroupController extends Controller
{


    public function index(Request $request)
{
    // Menggunakan query builder untuk mengambil data LayerGroup
    $query = LayerGroup::query();

    if ($request->has('search') && $request->search !== '') {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('layer_groups', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%")
              ->orWhere('aktif', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        });
    }

    // Menambahkan pengaturan sorting jika ada dalam request
    if ($request->has('sort_column') && $request->has('sort_direction')) {
        $sortColumn = $request->input('sort_column');
        $sortDirection = $request->input('sort_direction');
        $query->orderBy($sortColumn, $sortDirection);
    }

    // Menambahkan pagination
    $perPage = $request->input('per_page', 10);
    $currentPage = $request->input('current_page', 1);

    $layerGroups = $query->withCount('lahans')->paginate($perPage, ['*'], 'page', $currentPage);

    $layerGroupsData = $layerGroups->items();

    // Menambahkan total_lahan ke setiap item dalam data
    foreach ($layerGroupsData as &$layerGroup) {
        $layerGroup['total_lahan'] = $layerGroup['lahans_count'];
        unset($layerGroup['lahans_count']);
    }

    // Menyusun hasil response
    return response()->json([
        'message' => 'success',
        'data' => $layerGroupsData,
        'meta' => [
            'total' => $layerGroups->total(),
            'current_page' => $layerGroups->currentPage(),
            'per_page' => $layerGroups->perPage(),
        ],
    ]);
}

    
    

    // 2. Menampilkan layer group berdasarkan ID
    public function show($id)
    {
        $layer = LayerGroup::find($id);

        if (!$layer) {
            return response()->json(['message' => 'error', 'errors' => ['Layer group tidak ditemukan']], 404);
        }

        return response()->json([
            'message' => 'Sukses Menampikan Data Layer Group',
            'data' => $layer
        ]);
    }

    // 3. Menampilkan layer group berdasarkan username
    public function byUsername($username)
    {
        $layer = LayerGroup::where('username', $username)->first();

        if (!$layer) {
            return response()->json(['message' => 'error', 'errors' => ['Layer group tidak ditemukan']], 404);
        }

        return response()->json([
            'message' => 'Sukses Menampilkan Layer Group Berdasarkan Username',
            'data' => $layer
        ]);
    }

    // 4. Update layer group (Administrator atau pemilik)
    public function update(Request $request, $id)
    {
        $layer = LayerGroup::find($id);

        if (!$layer) {
            return response()->json(['message' => 'error', 'errors' => ['Data tidak ditemukan']], 404);
        }

        $user = Auth::user();
        $roleName = $this->getRoleName($user->username);

        $isAdmin = $roleName === 'administrator';

        if (!$isAdmin && $layer->username !== $user->username) {
            return response()->json(['message' => 'error', 'errors' => ['Unauthorized']], 403);
        }

        $validated = $request->validate([
            'layer_groups' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'aktif' => 'required|string|max:5',
        ]);

        $layer->update($validated);

        return response()->json([
            'message' => 'Sukses Update Layer Group',
            'data' => $layer
        ]);
    }

    // 5. Hapus layer group & akun owner
    public function destroy($id)
    {
        $layer = LayerGroup::find($id);

        if (!$layer) {
            return response()->json(['message' => 'error', 'errors' => ['Data tidak ditemukan']], 404);
        }

        $user = Auth::user();
        $roleName = $this->getRoleName($user->username);

        $isAdmin = $roleName === 'administrator';

        if (!$isAdmin && $layer->username !== $user->username) {
            return response()->json(['message' => 'error', 'errors' => ['Unauthorized']], 403);
        }

        $username = $layer->username;

        $layer->delete();
        Pengguna::where('username', $username)->delete();

        return response()->json([
            'message' => 'Sukses Delete Layer Group',
            'data' => "Layer group dan akun '$username' berhasil dihapus"
        ]);
    }

    // 🧠 Helper untuk ambil role secara manual (karena MyISAM tidak support relasi)
    private function getRoleName($username)
    {
        $rolePengguna = RolePengguna::where('username', $username)->first();
        if (!$rolePengguna) return null;

        $role = Role::find($rolePengguna->id_role);
        return $role ? strtolower($role->role) : null;
    }
}
