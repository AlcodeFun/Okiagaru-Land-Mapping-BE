<?php

namespace App\Http\Controllers;

use App\Models\KarakteristikAngka;
use App\Models\Lahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KarakteristikAngkaController extends Controller
{
    public function index()
    {
        $data = KarakteristikAngka::with('lahan', 'karakteristik')->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function getByLahan($id)
    {
        $data = KarakteristikAngka::where('id_lahan', $id)
            ->with('karakteristik')
            ->get();

        return response()->json([
            'message' => 'success',
            'data' => $data
        ],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lahan' => 'required|exists:t_lahan,id_lahan',
            'id_karakteristik_lahan' => 'required|exists:karakteristik_lahan,id_karakteristik_lahan',
            'nilai_angka' => 'required|numeric'
        ]);

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $lahan = Lahan::find($request->id_lahan);

        if (!$lahan) {
            return response()->json(['message' => 'Lahan tidak ditemukan'], 404);
        }

        $isOwner = $user->username === optional($lahan->layerGroup)->username;

        if (!$isAdmin && !$isOwner) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = KarakteristikAngka::create($request->all());

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = KarakteristikAngka::find($id);
        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $isOwner = $user->username === optional($data->lahan->layerGroup)->username;

        if (!$isAdmin && !$isOwner) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'nilai_angka' => 'required|numeric'
        ]);

        $data->update(['nilai_angka' => $request->nilai_angka]);

        return response()->json([
            'message' => 'success',
            'data' => [
                'id_karakteristik_angka' => $data->id_karakteristik_angka,
                'id_lahan' => $data->id_lahan,
                'id_karakteristik_lahan' => $data->id_karakteristik_lahan,
                'nilai_angka' => $data->nilai_angka,
            ]
        ]);
        
    }

    public function destroy($id)
    {
        $data = KarakteristikAngka::find($id);
        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $isOwner = $user->username === optional($data->lahan->layerGroup)->username;

        if (!$isAdmin && !$isOwner) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
