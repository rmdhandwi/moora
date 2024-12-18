<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class Users extends Controller
{
    public function usersPage()
    {
        $title = 'Users';
        $users = User::all();

        return Inertia::render('Admin/UserPage', [
            'title' => $title,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        // Validasi data dari form
        $request->validate([
            'username'      => 'required|unique:tbl_users,username|max:20',
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
            'username'   => strtolower($request->username),
            'password'   => Hash::make($request->password), // Enkripsi kata sandi
            'role'       => $request->role,
        ]);

        // Periksa apakah penyimpanan berhasil
        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil membuat User.',
            ]);
        } else {
            // Jika gagal mendaftarkan akun
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal Membuat User.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        // Validasi data dari form
        $request->validate([
            'username' => 'required|max:20|unique:tbl_users,username,' . $id . ',user_id',
            'password' => 'nullable|min:8|max:16', // Password is optional for update
            'role'     => 'required|numeric',
        ], [
            'username.required' => 'Nama pengguna harus diisi',
            'username.unique'   => 'Nama pengguna telah digunakan!',
            'username.max'      => 'Nama pengguna maksimal 20 karakter',
            'password.min'      => 'Kata sandi minimal 8 karakter',
            'password.max'      => 'Kata sandi maksimal 16 karakter',
            'role.required'     => 'Role harus diisi',
        ]);

        // Temukan pengguna berdasarkan ID
        $user = User::findOrFail($id);

        // Simpan data yang diupdate
        $user->username = strtolower($request->username);

        // Cek apakah password diisi
        if ($request->filled('password')) {
            // Jika password diisi, enkripsi dan simpan
            $user->password = Hash::make($request->password);
        }
        // Jika password tidak diisi, biarkan password lama tetap

        $user->role = $request->role;

        // Periksa apakah penyimpanan berhasil
        if ($user->update()) {
            // Jika pengguna yang diupdate adalah pengguna yang sedang aktif
            if ($user->user_id === auth()->id()) {
                // Logout pengguna
                Auth::logout();

                $request->session()->invalidate();  // Menghancurkan session
                $request->session()->regenerateToken();  // Mengganti token CSRF untuk keamanan

                // Redirect dengan notifikasi
                return redirect()->route('login')->with([
                    'notif_status' => 'info',
                    'message'      => 'Data Anda telah diperbarui. Silakan login kembali.',
                ]);
            }

            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil memperbarui User.',
            ]);
        } else {
            // Jika gagal mendaftarkan akun
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal memperbarui User.',
            ]);
        }
    }

    public function destroy($id)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::findOrFail($id);

        // Periksa apakah pengguna yang akan dihapus adalah pengguna yang sedang aktif
        if ($user->user_id === auth()->id()) {
            // Logout pengguna
            Auth::logout();

            // Hancurkan session
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // Hapus pengguna
            $user->delete();

            // Redirect dengan notifikasi
            return redirect()->route('login')->with([
                'notif_status' => 'info',
                'message'      => 'Akun Anda telah dihapus. Silakan login kembali.',
            ]);
        }

        // Hapus pengguna
        $user->delete();

        // Redirect dengan notifikasi
        return redirect()->back()->with([
            'notif_status' => 'success',
            'message'      => 'Berhasil menghapus User.',
        ]);
    }
}
