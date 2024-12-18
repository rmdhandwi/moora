<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthLogin extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Nama pengguna harus diisi',
            'password.required' => 'Kata sandi harus diisi',
        ]);

        // Cek apakah pengguna ada dengan mengubah username ke lowercase
        $user = User::whereRaw('LOWER(username) = ?', [strtolower($request->username)])->first();

        // Jika pengguna tidak ditemukan
        if (!$user) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Username Tidak terdaftar',
            ]);
        }

        // Coba untuk melakukan otentikasi
        if (Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Redirect ke dashboard jika login berhasil
            return redirect()->route('dashboard')->with([
                'notif_status' => 'success',
                'message' => 'Selamat Datang, ' . $user->username . '.',
            ]);
        }

        // Jika login gagal
        return redirect()->back()->with([
            'notif_status' => 'error',
            'message' => 'Username atau Password salah.',
        ]);
    }


    public function register(Request $request)
    {
        // Validasi data dari form
        $request->validate([
            'username'      => 'required|unique:users,username|max:20',
            'password'      => 'required|min:8|max:16',
            'role'          => 'required|numeric',
        ], [
            'username.required'     => 'Nama pengguna harus diisi',
            'username.unique'       => 'Nama pengguna telah digunakan!',
            'username.max'          => 'Nama pengguna maksimal 20 karakter',
            'password.required'     => 'Kata sandi harus diisi',
            'password.min'          => 'Kata sandi minimal 8 karakter',
            'password.max'          => 'Kata sandi minimal 16 karakter',
            'role.required'         => 'Role harus diisi',
        ]);

        // Buat pengguna baru
        $insert = User::create([
            'username'   => $request->username,
            'password'   => Hash::make($request->password), // Enkripsi kata sandi
            'role'       => $request->role,
        ]);

        // Periksa apakah penyimpanan berhasil
        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil mendaftarkan Akun.',
            ]);
        } else {
            // Jika gagal mendaftarkan akun
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal mendaftarkan Akun.',
            ]);
        }
    }
}
