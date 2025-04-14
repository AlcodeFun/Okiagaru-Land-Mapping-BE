<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NilaiPilihan;
use App\Models\KarakteristikLahan;
use Illuminate\Support\Facades\Auth;

class NilaiPilihanController extends Controller
{
    // ✅ GET semua data (public)
    public function index()
    {
        return response()->json([
            'message' => 'success',
            'data' => NilaiPilihan::with('karakteristik')->get()
        ]);
    }

    // ✅ GET berdasarkan karakteristik (public)
    public function getByKarakteristik($id)
    {
        $data = NilaiPilihan::where('id_karakteristik_lahan', $id)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }

    // ✅ POST tambah data (Administrator only)
    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'id_karakteristik_lahan' => 'required|exists:karakteristik_lahan,id_karakteristik_lahan',
            'pilihan' => 'required|string|max:100'
        ]);

        $nilai = NilaiPilihan::create($validated);

        return response()->json([
            'message' => 'Berhasil menambah data',
            'data' => $nilai
        ]);
    }

    // ✅ PUT update data (Administrator only)
    public function update(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $nilai = NilaiPilihan::find($id);
        if (!$nilai) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'pilihan' => 'required|string|max:100'
        ]);

        $nilai->update($validated);

        return response()->json([
            'message' => 'Berhasil update data',
            'data' => $nilai
        ]);
    }

    // ✅ DELETE hapus data (Administrator only)
    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $nilai = NilaiPilihan::find($id);
        if (!$nilai) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $nilai->delete();

        return response()->json(['message' => 'Berhasil menghapus data']);
    }

    // ✅ Helper cek Administrator
    private function isAdmin()
    {
        $user = Auth::user();
        return strtolower(optional(optional($user->rolePengguna)->role)->role) === 'Administrator';
    }
}
