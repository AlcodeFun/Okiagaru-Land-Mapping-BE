<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Pengguna;
use App\Models\Role;
use App\Models\RolePengguna;
use App\Models\LayerGroup;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerAdmin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:t_pengguna,username',
            'email' => 'nullable|email|unique:t_pengguna,email',
            'password' => 'required|string|min:6|confirmed',
            'nama' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan pengguna
        $pengguna = Pengguna::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama' => $request->nama,
            'status'=>'Aktif'
        ]);

        // Ambil ID role Administrator
        $roleAdmin = Role::where('role', 'Administrator')->first();
        if (!$roleAdmin) {
            return response()->json([
                'message' => 'error',
                'errors' => ['role' => ['Role Administrator tidak ditemukan']]
            ], 500);
        }

        // Simpan role pengguna
        RolePengguna::create([
            'username' => $pengguna->username,
            'id_role' => $roleAdmin->id_role,
        ]);

        return response()->json([
            'message' => 'success',
            'data' => $pengguna
        ], 201);
    }

    public function registerOwner(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|unique:t_pengguna,username',
        'email' => 'required|email|unique:t_pengguna,email',
       'password' => 'required|string|min:6|confirmed',
        'nama' => 'required|string',
        'telepon' => 'required|string',
        'foto' => 'required|file|mimes:jpg,jpeg,png|max:2048',



        // Layer Group
        'deskripsi' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('images', 'public');
    } else {
        $path = null;
    }
    

    // Simpan pengguna
    $pengguna = Pengguna::create([
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'nama' => $request->nama,
        'telepon' => $request->telepon,
        'foto' => $path,
        'status'=>'Aktif'
    ]);

    // Ambil ID role 'owner'
    $roleOwner = Role::where('role', 'owner')->first();
    if (!$roleOwner) {
        return response()->json([
            'message' => 'error',
            'errors' => ['role' => ['Role owner tidak ditemukan']]
        ], 500);
    }

    // Simpan ke t_role_pengguna
    RolePengguna::create([
        'username' => $pengguna->username,
        'id_role' => $roleOwner->id_role,
    ]);
    
    // Simpan ke t_layer_groups
      $default_aktif='Ya';
    LayerGroup::create([
      
        'username' => $pengguna->username,
        'layer_groups' => $request->nama,
        'deskripsi' => $request->deskripsi,
        'aktif' => $default_aktif
    ]);

    return response()->json([
        'message' => 'Berhasil Daftar Sebagai Owner',
    ], 201);
}

public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Cari pengguna berdasarkan username
    $pengguna = Pengguna::where('username', $request->username)->first();

    // Periksa password
    if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
        return response()->json([
            'message' => 'error',
            'errors' => ['login' => ['Username atau password salah']]
        ], 401);
    }

    // Ambil role
    $rolePengguna = $pengguna->rolePengguna;
    $role = $rolePengguna ? $rolePengguna->role->role : null;

    // Buat token Sanctum
    $token = $pengguna->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'success',
        'token' => $token,
        'data' => [
            'username' => $pengguna->username,
            'email' => $pengguna->email,
            'nama' => $pengguna->nama,
            'telepon' => $pengguna->telepon,
            'foto' => $pengguna->foto,
            'status' => $pengguna->status,
            'role' => $role,
        ]
    ]);
}

public function me(Request $request)
{
    $pengguna = Auth::user();

    if (!$pengguna) {
        return response()->json([
            'message' => 'error',
            'errors' => ['auth' => ['Pengguna tidak ditemukan']]
        ], 401);
    }

    // Ambil role
    $rolePengguna = $pengguna->rolePengguna;
    $role = $rolePengguna ? $rolePengguna->role->role : null;

    return response()->json([
        'message' => 'success',
        'data' => [
            'username' => $pengguna->username,
            'email' => $pengguna->email,
            'nama' => $pengguna->nama,
            'telepon' => $pengguna->telepon,
            'foto' => $pengguna->foto, 
            'status' => $pengguna->status,
            'role' => $role,
        ]
    ]);
}

public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string',
        'new_password' => 'required|string|min:6|confirmed',
        'confirm_password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    $Administrator = Auth::user();

    // Cek role Administrator
    $role = $Administrator->rolePengguna->role->role ?? null;
    if ($role !== 'Administrator') {
        return response()->json([
            'message' => 'error',
            'errors' => ['auth' => ['Hanya Administrator yang dapat mereset password']]
        ], 403);
    }

    // Cek password Administrator (konfirmasi)
    if (!Hash::check($request->confirm_password, $Administrator->password)) {
        return response()->json([
            'message' => 'error',
            'errors' => ['confirm_password' => ['Password konfirmasi salah']]
        ], 401);
    }

    // Reset password user target
    $target = Pengguna::where('username', $request->username)->first();
    if (!$target) {
        return response()->json([
            'message' => 'error',
            'errors' => ['username' => ['User tidak ditemukan']]
        ], 404);
    }

    $target->password = Hash::make($request->new_password);
    $target->save();

    return response()->json([
        'message' => 'success',
        'data' => [
            'username' => $target->username,
            'status' => 'Password berhasil direset'
        ]
    ]);
}

public function logout(Request $request)
{
    $user = $request->user(); // atau bisa pakai Auth::user();

    // Revoke token yang sedang digunakan
    $user->currentAccessToken()->delete();

    return response()->json([
        'message' => 'success',
        'status' => 'Berhasil logout'
    ]);
}



}
