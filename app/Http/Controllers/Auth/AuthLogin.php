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
        $request->validate([
            'nama_pengguna' => 'required',
            'kata_sandi'    => 'required',
        ], [
            'nama_pengguna.required' => 'Nama pengguna harus diisi',
            'kata_sandi.required'    => 'Kata sandi harus diisi',
        ]);

        // Cek apakah pengguna ada
        $user = User::where('nama_pengguna', $request->nama_pengguna)->first();

        // Verifikasi kata sandi
        if ($user && Hash::check($request->kata_sandi, $user->kata_sandi)) {
            // Update status pengguna menjadi 1 (login)
            $user->status = 1;
            $user->save();

            // Login pengguna dan regenerasi session
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with([
                'notif_status' => 'success',
                'message'      => 'Selamat Datang.',
                'notif_show'   => true,
            ]);
        }

        // Jika login gagal
        return redirect()->back()->with([
            'notif_status' => 'error',
            'message'      => 'Kode pengguna atau kata sandi salah.',
            'notif_show'   => true,
        ]);
    }


    public function register(Request $request)
    {
        // Validasi data dari form
        $request->validate([
            'nama_pengguna' => 'required|unique:users,nama_pengguna|max:20',
            'kata_sandi'    => 'required|min:8|max:16',
            'role'          => 'required|numeric',
        ], [
            'nama_pengguna.required' => 'Nama pengguna harus diisi',
            'nama_pengguna.unique'   => 'Nama pengguna telah digunakan!',
            'nama_pengguna.max'      => 'Nama pengguna maksimal 20 karakter',
            'kata_sandi.required'    => 'Kata sandi harus diisi',
            'kata_sandi.min'         => 'Kata sandi minimal 8 karakter',
            'kata_sandi.max'         => 'Kata sandi minimal 16 karakter',
            'role.required'          => 'Role harus diisi',
        ]);

        // Buat pengguna baru
        $insert = User::create([
            'nama_pengguna'   => $request->nama_pengguna,
            'kata_sandi'      => Hash::make($request->kata_sandi), // Enkripsi kata sandi
            'role'            => $request->role,
            'status'          => 0,
        ]);

        // Periksa apakah penyimpanan berhasil
        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil mendaftarkan Akun.',
                'notif_show'   => true,
            ]);
        } else {
             // Jika gagal mendaftarkan akun
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal mendaftarkan Akun.',
                'notif_show'   => true,
            ]);
        }
    }

}
