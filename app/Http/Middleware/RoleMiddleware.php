<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RolePengguna;
use App\Models\Role;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        $roleName = null;

        if ($user) {
            $rolePengguna = RolePengguna::where('username', $user->username)->first();
            if ($rolePengguna) {
                $role = Role::find($rolePengguna->id_role);
                $roleName = $role ? strtolower($role->role) : null;
            }
        }

        if (!$user || !$roleName || !in_array($roleName, array_map('strtolower', $roles))) {
            return response()->json([
                'message' => 'error',
                'errors' => ['auth' => ['Akses ditolak']]
            ], 403);
        }

        return $next($request);
    }
}
