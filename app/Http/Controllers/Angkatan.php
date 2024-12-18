<?php

namespace App\Http\Controllers;

use App\Models\AngkatanModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Angkatan extends Controller
{
    public function angkatanPage()
    {
        $title = 'Angkatan';
        $angkatan = AngkatanModel::all();

        return Inertia::render('Admin/AngkatanPage', [
            'title' => $title,
            'angkatan' => $angkatan
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request
        $request->validate([
            'tahun_angkatan' => 'required|unique:tbl_angkatan,tahun_angkatan',
            'jurusan'    => 'required'
        ], [
            '*.required' => 'Kolom wajib diisi',
            'tahun_angkatan.unique' => 'Tahun ini sudah terdaftar'
        ]);

        // Create a new AngkatanModel instance
        $insert = AngkatanModel::create([
            'tahun_angkatan' => $request->tahun_angkatan,
            'jurusan'    => $request->jurusan
        ]);

        // Check if the insert was successful
        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil membuat tahun angkatan.',
            ]);
        } else {
            // If the insert failed
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal membuat tahun angkatan.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'tahun_angkatan' => 'required|unique:tbl_angkatan,tahun_angkatan,' . $id . ',angkatan_id', // Exclude current record
            'jurusan'        => 'required'
        ], [
            '*.required' => 'Kolom wajib diisi',
            'tahun_angkatan.unique' => 'Tahun ini sudah terdaftar'
        ]);

        // Find the AngkatanModel instance by ID
        $angkatan = AngkatanModel::findOrFail($id);

        // Update the record
        $angkatan->tahun_angkatan = $request->tahun_angkatan;
        $angkatan->jurusan = $request->jurusan;

        // Save the changes
        if ($angkatan->save()) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil memperbarui tahun angkatan.',
            ]);
        } else {
            // If the update failed
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal memperbarui tahun angkatan.',
            ]);
        }
    }

    public function destroy($id)
    {
        // Find the AngkatanModel instance by ID
        $angkatan = AngkatanModel::findOrFail($id);

        // Delete the record
        if ($angkatan->delete()) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil menghapus tahun angkatan.',
            ]);
        } else {
            // If the delete failed
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal menghapus tahun angkatan.',
            ]);
        }
    }
}
