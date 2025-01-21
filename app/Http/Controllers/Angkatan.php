<?php

namespace App\Http\Controllers;

use App\Models\AngkatanModel;
use App\Models\DosenModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Angkatan extends Controller
{
    public function angkatanPage()
    {
        $title = 'Angkatan';
        $angkatan = AngkatanModel::with('dosen')->get();
        $dosen = DosenModel::all();
        $currentUser = Auth::user();
        $username = DosenModel::where('user_id', $currentUser->user_id)
            ->value('nama_dosen');

        return Inertia::render('Admin/AngkatanPage', [
            'title' => $title,
            'angkatan' => $angkatan,
            'dosen' => $dosen,
            'username' => $username
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request
        $request->validate([
            'tahun_angkatan' => 'required|unique:tbl_angkatan,tahun_angkatan|digits:4',
            'dosen_id'      => 'required|unique:tbl_angkatan,dosen_id',
            'jurusan'    => 'required'
        ], [
            '*.required' => 'Kolom wajib diisi',
            'tahun_angkatan.digits' => 'Tahun hanya dapat diisi 4 digit angka',
            'tahun_angkatan.unique' => 'Tahun ini sudah terdaftar',
            'dosen_id.unique' => 'Dosen sudah terdaftar'
        ]);

        // Create a new AngkatanModel instance
        $insert = AngkatanModel::create([
            'tahun_angkatan' => $request->tahun_angkatan,
            'dosen_id' => $request->dosen_id,
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
        // Cari data Angkatan berdasarkan ID
        $angkatan = AngkatanModel::findOrFail($id);

        // Periksa apakah dosen_id berubah
        $isDosenIdChanged = $request->dosen_id !== $angkatan->dosen_id;

        // Validasi data
        $request->validate([
            'tahun_angkatan' => 'required|unique:tbl_angkatan,tahun_angkatan,' . $id . ',angkatan_id|digits:4', // Exclude current record
            'dosen_id' => $isDosenIdChanged
                ? 'required|unique:tbl_angkatan,dosen_id'
                : 'required', // Hanya validasi unik jika dosen_id berubah
            'jurusan' => 'required'
        ], [
            '*.required' => 'Kolom wajib diisi',
            'tahun_angkatan.unique' => 'Tahun ini sudah terdaftar',
            'tahun_angkatan.digits' => 'Tahun hanya dapat diisi 4 digit angka',
            'dosen_id.unique' => 'Dosen sudah terdaftar'
        ]);

        // Perbarui data Angkatan
        $angkatan->tahun_angkatan = $request->tahun_angkatan;
        $angkatan->dosen_id = $request->dosen_id;
        $angkatan->jurusan = $request->jurusan;

        // Simpan perubahan
        if ($angkatan->save()) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Berhasil memperbarui tahun angkatan.',
            ]);
        } else {
            // Jika penyimpanan gagal
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Gagal memperbarui tahun angkatan.',
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
