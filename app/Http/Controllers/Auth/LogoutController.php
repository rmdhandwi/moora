<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Logout pengguna dan hapus session
        Auth::logout();

        $request->session()->invalidate();  // Menghancurkan session
        $request->session()->regenerateToken();  // Mengganti token CSRF untuk keamanan

        // Redirect ke halaman login dengan notifikasi
        return redirect()->route('login')->with([
            'notif_status' => 'success',
            'message'      => 'Anda telah logout.',
        ]);
    }
}
