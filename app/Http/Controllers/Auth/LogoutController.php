<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
         // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Pastikan pengguna ada dan status dapat diubah
        if ($user) {
            $user->status = 0;  // Perbarui kolom 'status' menjadi 0
            $user->save();  // Simpan perubahan ke database
        }
        // Menghapus informasi pengguna dari session
        session()->forget(['user_id', 'user_name', 'user_role']);

        // Logout pengguna dan hapus session
        Auth::logout();

        $request->session()->invalidate();  // Menghancurkan session
        $request->session()->regenerateToken();  // Mengganti token CSRF untuk keamanan

        // Redirect ke halaman login dengan notifikasi
        return redirect()->route('login')->with([
            'notif_status' => 'success',
            'message'      => 'Anda telah logout.',
            'notif_show'   => true,
        ]);
    }
}
