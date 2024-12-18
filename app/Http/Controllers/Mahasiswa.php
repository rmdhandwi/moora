<?php

namespace App\Http\Controllers;

use App\Models\AngkatanModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Mahasiswa extends Controller
{
    public function mahasiswaPage()
    {
        $title = 'Mahasiswa';
        $Angkatan = AngkatanModel::all();
        $dosen = DosenModel::all();
        $mahasiswa = MahasiswaModel::with('dosen', 'angkatan')->get();

        return Inertia::render('Mahasiswa/MahasiswaPage', [
            'title' => $title,
            'angkatan' => $Angkatan,
            'dosen'    => $dosen,
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'dosen_id'          => 'required',
            'angkatan_id'       => 'required',
            'nama_mahasiswa'    => 'required',
            'npm'               => 'required|unique:tbl_mahasiswa,npm',
            'sks_total'         => 'required|numeric',
            'sks_tempuh'        => 'required|numeric|min:0|max:144',
            'studi_tempuh'      => 'required|numeric|min:0|max:14',
            'sks_sisa'          => 'required|numeric',
            'studi_total'       => 'required|numeric',
            'studi_sisa'        => 'required|numeric',
        ], [
            '*.required'        => 'Kolom wajib diisi',
            'npm.unique'        => 'Npm telah Digunakan',
            '*.numeric'         => 'Kolom harus berupa angka',
            'sks_tempuh.min'    => 'SKS tempuh minimal 0',
            'sks_tempuh.max'    => 'SKS tempuh maksimal 144',
            'studi_tempuh.min'  => 'Studi tempuh minimal 0',
            'studi_tempuh.max'  => 'Studi tempuh minimal 14'
        ]);

        // Create a new AngkatanModel instance
        $insert = MahasiswaModel::create([
            'dosen_id' => $request->dosen_id,
            'angkatan_id' => $request->angkatan_id,
            'nama_mahasiswa' => $request->nama_mahasiswa,
            'npm' => $request->npm,
            'sks_total' => $request->sks_total,
            'sks_tempuh' => $request->sks_tempuh,
            'sks_sisa' => $request->sks_sisa,
            'studi_total' => $request->studi_total,
            'studi_tempuh' => $request->studi_tempuh,
            'studi_sisa' => $request->studi_sisa,
        ]);

        // Check if the insert was successful
        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil menambahkan mahasiswa.',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal menambahkan mahasiswa.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'dosen_id'          => 'required',
            'angkatan_id'       => 'required',
            'nama_mahasiswa'    => 'required',
            'npm'               => 'required|unique:tbl_mahasiswa,npm,' . $id . ',mahasiswa_id', // Exclude current record from unique check
            'sks_total'         => 'required|numeric',
            'sks_tempuh'        => 'required|numeric|min:0|max:144',
            'studi_tempuh'      => 'required|numeric|min:0|max:14',
            'sks_sisa'          => 'required|numeric',
            'studi_total'       => 'required|numeric',
            'studi_sisa'        => 'required|numeric',
        ], [
            '*.required'        => 'Kolom wajib diisi',
            'npm.unique'        => 'Npm telah Digunakan',
            '*.numeric'         => 'Kolom harus berupa angka',
            'sks_tempuh.min'    => 'SKS tempuh minimal 0',
            'sks_tempuh.max'    => 'SKS tempuh maksimal 144',
            'studi_tempuh.min'  => 'Studi tempuh minimal 0',
            'studi_tempuh.max'  => 'Studi tempuh maksimal 14'
        ]);

        // Find the existing record
        $mahasiswa = MahasiswaModel::findOrFail($id);

        // Update the record
        $mahasiswa->update([
            'dosen_id' => $request->dosen_id,
            'angkatan_id' => $request->angkatan_id,
            'nama_mahasiswa' => $request->nama_mahasiswa,
            'npm' => $request->npm,
            'sks_total' => $request->sks_total,
            'sks_tempuh' => $request->sks_tempuh,
            'sks_sisa' => $request->sks_sisa,
            'studi_total' => $request->studi_total,
            'studi_tempuh' => $request->studi_tempuh,
            'studi_sisa' => $request->studi_sisa,
        ]);

        return redirect()->back()->with([
            'notif_status' => 'success',
            'message'      => 'Berhasil memperbarui mahasiswa.',
        ]);
    }

    public function destroy($id)
    {
        // Find the existing record
        $mahasiswa = MahasiswaModel::findOrFail($id);

        // Delete the record
        $mahasiswa->delete();

        return redirect()->back()->with([
            'notif_status' => 'success',
            'message'      => 'Berhasil menghapus mahasiswa.',
        ]);
    }
}
