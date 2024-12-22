<?php

namespace App\Http\Controllers;

use App\Models\DosenModel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Dosen extends Controller
{
    public function dosenPage()
    {
        $title = 'Dosen';
        $dosen = DosenModel::with('users')->get();
        $users = User::where('role', 3)->get();

        return Inertia::render('Dosen/DosenPage', [
            'title' => $title,
            'dosen' => $dosen,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'nama_dosen' => 'required',
            'user_id'    => 'required|unique:tbl_dosen,user_id'
        ], [
            '*.required' => 'Kolom wajib diisi',
            'user_id.unique' => 'User ini sudah terdaftar'
        ]);

        // Create a new DosenModel instance
        $insert = DosenModel::create([
            'nama_dosen' => ucwords($request->nama_dosen),
            'user_id'    => $request->user_id
        ]);

        // Check if the insert was successful
        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil membuat Dosen.',
            ]);
        } else {
            // If the insert failed
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal membuat Dosen.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // Validasi data dari form
        $request->validate([
            'nama_dosen' => 'required',
            'user_id'    => 'required|unique:tbl_dosen,user_id,' . $id . ',dosen_id'
        ], [
            '*.required' => 'Kolom wajib diisi',
            'user_id.unique' => 'User  ini sudah terdaftar'
        ]);

        // Temukan dosen berdasarkan ID
        $dosen = DosenModel::findOrFail($id);

        // Update data dosen
        $dosen->nama_dosen = ucwords($request->nama_dosen);
        $dosen->user_id = $request->user_id;

        // Simpan perubahan
        if ($dosen->save()) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil memperbarui Dosen.',
            ]);
        } else {
            // Jika gagal memperbarui data
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal memperbarui Dosen.',
            ]);
        }
    }

    public function destroy($id)
    {
        // Temukan dosen berdasarkan ID
        $dosen = DosenModel::findOrFail($id);

        // Hapus dosen
        if ($dosen->delete()) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil menghapus Dosen.',
            ]);
        } else {
            // Jika gagal menghapus data
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal menghapus Dosen.',
            ]);
        }
    }
}
