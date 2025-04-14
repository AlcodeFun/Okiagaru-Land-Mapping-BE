<?php

namespace App\Http\Controllers;

use App\Models\KarakteristikPilihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KarakteristikPilihanController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'success',
            'data' => KarakteristikPilihan::with(['karakteristik', 'nilaiPilihan'])->get()
        ]);
    }

    public function getByLahan($id)
    {
        $data = KarakteristikPilihan::where('id_lahan', $id)
            ->with(['karakteristik', 'nilaiPilihan'])
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
            'id_nilai_pilihan' => 'required|exists:nilai_pilihan,id_nilai_pilihan',
        ]);

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $username = $user->username;

        if (!$isAdmin && !$this->isOwner($request->id_lahan, $username)) {
            return response()->json([
                'message' => 'error',
                'errors' => ['auth' => ['Akses ditolak']]
            ], 403);
        }

        $data = KarakteristikPilihan::create($request->all());

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = KarakteristikPilihan::find($id);
        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $username = $user->username;

        if (!$isAdmin && !$this->isOwner($data->id_lahan, $username)) {
            return response()->json([
                'message' => 'error',
                'errors' => ['auth' => ['Akses ditolak']]
            ], 403);
        }

        $request->validate([
            'id_karakteristik_lahan' => 'sometimes|exists:karakteristik_lahan,id_karakteristik_lahan',
            'id_nilai_pilihan' => 'sometimes|exists:nilai_pilihan,id_nilai_pilihan',
        ]);

        $data->update($request->all());

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        $data = KarakteristikPilihan::find($id);
        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $isAdmin = strtolower(optional($user->rolePengguna->role)->role) === 'administrator';
        $username = $user->username;

        if (!$isAdmin && !$this->isOwner($data->id_lahan, $username)) {
            return response()->json([
                'message' => 'error',
                'errors' => ['auth' => ['Akses ditolak']]
            ], 403);
        }

        $data->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    private function isOwner($idLahan, $username)
    {
        return \App\Models\Lahan::where('id_lahan', $idLahan)
            ->whereHas('layerGroup', fn ($q) => $q->where('username', $username))
            ->exists();
    }
}
