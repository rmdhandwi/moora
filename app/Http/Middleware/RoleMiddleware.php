<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect ke login jika belum login
        }

        $user = Auth::user();

        // Cek apakah role pengguna ada dalam daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            return redirect()->route('login')->with([
                'notif_status' => 'info',
                'message' => 'Role tidak ditemukan',
            ]);
        }

        return $next($request);
    }
}
