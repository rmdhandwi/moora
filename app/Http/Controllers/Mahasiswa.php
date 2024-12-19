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
        $currentUser = auth()->user(); // Mendapatkan user yang sedang login

        // Inisialisasi variabel mahasiswa
        $mahasiswa = [];

        if ($currentUser->role == 3) {
            // Ambil data dosen berdasarkan user_id dari pengguna yang sedang login
            $dosen = DosenModel::where('user_id', $currentUser->user_id)->first();
            // Cek apakah data dosen ditemukan
            if ($dosen) {
                $dosenId = $dosen->dosen_id; // Ambil ID dosen
                // Filter mahasiswa berdasarkan dosen_id
                $mahasiswa = MahasiswaModel::with('dosen', 'angkatan')
                    ->where('dosen_id', $dosenId)
                    ->get();
            } else {
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'message' => 'Data dosen tidak ditemukan untuk pengguna ini.'
                ]);
            }
        } else {
            // Jika role bukan 3, ambil semua data mahasiswa
            $mahasiswa = MahasiswaModel::with('dosen', 'angkatan')->get();
        }

        // Ambil data angkatan dan dosen untuk dikirim ke view
        $angkatan = AngkatanModel::all();
        $dosen = DosenModel::all();

        return Inertia::render('Mahasiswa/MahasiswaPage', [
            'title' => $title,
            'angkatan' => $angkatan,
            'dosen'    => $dosen,
            'mahasiswa' => $mahasiswa
        ]);
    }


    public function store(Request $request)
    {
        $currentUser = auth()->user(); // Mendapatkan user yang sedang login

        // Validasi khusus untuk role 
        if ($currentUser->role == 1) {
            $request->validate([
                'dosen_id'          => 'required',
                'angkatan_id'       => 'required',
                'nama_mahasiswa'    => 'required',
                'npm'               => 'required|numeric|unique:tbl_mahasiswa,npm|digits:8',
                'sks_total'         => 'required|numeric',
                'sks_tempuh'        => 'required|numeric|min:0|max:144',
                'studi_tempuh'      => 'required|numeric|min:0|max:14',
                'sks_sisa'          => 'required|numeric',
                'studi_total'       => 'required|numeric',
                'studi_sisa'        => 'required|numeric',
            ], [
                '*.required'        => 'Kolom wajib diisi',
                'npm.unique'        => 'NPM telah digunakan',
                'npm.digits'        => 'Maksimal 8 digit angka',
                '*.numeric'         => 'Kolom harus berupa angka',
                'sks_tempuh.min'    => 'SKS tempuh minimal 0',
                'sks_tempuh.max'    => 'SKS tempuh maksimal 144',
                'studi_tempuh.min'  => 'Studi tempuh minimal 0',
                'studi_tempuh.max'  => 'Studi tempuh maksimal 14',
            ]);

            // Data untuk role 1 dan 3
            $data = [
                'dosen_id'      => $request->dosen_id,
                'angkatan_id'   => $request->angkatan_id,
                'nama_mahasiswa' => $request->nama_mahasiswa,
                'npm'           => $request->npm,
                'sks_total'     => $request->sks_total,
                'sks_tempuh'    => $request->sks_tempuh,
                'sks_sisa'      => $request->sks_sisa,
                'studi_total'   => $request->studi_total,
                'studi_tempuh'  => $request->studi_tempuh,
                'studi_sisa'    => $request->studi_sisa,
            ];
        } else if ($currentUser->role == 3) {
            $request->validate([
                'sks_total'         => 'required|numeric',
                'sks_tempuh'        => 'required|numeric|min:0|max:144',
                'studi_tempuh'      => 'required|numeric|min:0|max:14',
                'sks_sisa'          => 'required|numeric',
                'studi_total'       => 'required|numeric',
                'studi_sisa'        => 'required|numeric',
            ], [
                '*.numeric'         => 'Kolom hanya berupa angka',
                'sks_tempuh.min'    => 'SKS tempuh minimal 0',
                'sks_tempuh.max'    => 'SKS tempuh maksimal 144',
                'studi_tempuh.min'  => 'Studi tempuh minimal 0',
                'studi_tempuh.max'  => 'Studi tempuh maksimal 14',
            ]);

            // Data untuk role 1 dan 3
            $data = [
                'sks_total'     => $request->sks_total,
                'sks_tempuh'    => $request->sks_tempuh,
                'sks_sisa'      => $request->sks_sisa,
                'studi_total'   => $request->studi_total,
                'studi_tempuh'  => $request->studi_tempuh,
                'studi_sisa'    => $request->studi_sisa,
            ];
        }
        // Jika role adalah 2
        else if ($currentUser->role == 2) {
            $request->validate([
                'dosen_id'       => 'required',
                'angkatan_id'    => 'required',
                'nama_mahasiswa' => 'required',
                'npm'               => 'required|numeric|unique:tbl_mahasiswa,npm|digits:8',
            ], [
                '*.required'     => 'Kolom wajib diisi',
                'npm.digits'        => 'Maksimal 8 digit angka',
                '*.numeric'      => 'Kolom hanya berupa angka',
                'npm.unique'     => 'NPM telah digunakan',
            ]);

            // Data untuk role 2, nilai SKS dan studi diset ke 0
            $data = [
                'dosen_id'      => $request->dosen_id,
                'angkatan_id'   => $request->angkatan_id,
                'nama_mahasiswa' => $request->nama_mahasiswa,
                'npm'           => $request->npm,
                'sks_total'     => 0,
                'sks_tempuh'    => 0,
                'sks_sisa'      => 0,
                'studi_total'   => 0,
                'studi_tempuh'  => 0,
                'studi_sisa'    => 0,
            ];
        } else {
            // Jika role tidak diizinkan
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Anda tidak memiliki akses untuk menambahkan mahasiswa.',
            ]);
        }

        // Simpan data mahasiswa
        $insert = MahasiswaModel::create($data);

        // Cek hasil insert dan kembalikan notifikasi
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
        $currentUser = auth()->user(); // Mendapatkan user yang sedang login

        // Cari data mahasiswa berdasarkan ID
        $mahasiswa = MahasiswaModel::findOrFail($id);

        // Validasi khusus berdasarkan role
        if ($currentUser->role == 1) {
            $request->validate([
                'dosen_id'          => 'required',
                'angkatan_id'       => 'required',
                'nama_mahasiswa'    => 'required',
                'npm'               => 'required|unique:tbl_mahasiswa,npm,' . $id . ',mahasiswa_id',
                'sks_total'         => 'required|numeric',
                'sks_tempuh'        => 'required|numeric|min:0|max:144',
                'studi_tempuh'      => 'required|numeric|min:0|max:14',
                'sks_sisa'          => 'required|numeric',
                'studi_total'       => 'required|numeric',
                'studi_sisa'        => 'required|numeric',
            ], [
                '*.required'        => 'Kolom wajib diisi',
                'npm.unique'        => 'NPM telah digunakan',
                '*.numeric'         => 'Kolom harus berupa angka',
                'sks_tempuh.min'    => 'SKS tempuh minimal 0',
                'sks_tempuh.max'    => 'SKS tempuh maksimal 144',
                'studi_tempuh.min'  => 'Studi tempuh minimal 0',
                'studi_tempuh.max'  => 'Studi tempuh maksimal 14',
            ]);

            // Data untuk role 1
            $data = $request->only([
                'dosen_id',
                'angkatan_id',
                'nama_mahasiswa',
                'npm',
                'sks_total',
                'sks_tempuh',
                'sks_sisa',
                'studi_total',
                'studi_tempuh',
                'studi_sisa',
            ]);
        } else if ($currentUser->role == 3) {
            $request->validate([
                'sks_total'         => 'required|numeric',
                'sks_tempuh'        => 'required|numeric|min:0|max:144',
                'studi_tempuh'      => 'required|numeric|min:0|max:14',
                'sks_sisa'          => 'required|numeric',
                'studi_total'       => 'required|numeric',
                'studi_sisa'        => 'required|numeric',
            ], [
                'sks_tempuh.min'    => 'SKS tempuh minimal 0',
                'sks_tempuh.max'    => 'SKS tempuh maksimal 144',
                'studi_tempuh.min'  => 'Studi tempuh minimal 0',
                'studi_tempuh.max'  => 'Studi tempuh maksimal 14',
            ]);

            // Data untuk role 3
            $data = $request->only([
                'sks_total',
                'sks_tempuh',
                'sks_sisa',
                'studi_total',
                'studi_tempuh',
                'studi_sisa',
            ]);
        } else if ($currentUser->role == 2) {
            $request->validate([
                'dosen_id'       => 'required',
                'angkatan_id'    => 'required',
                'nama_mahasiswa' => 'required',
                'npm'            => 'required|unique:tbl_mahasiswa,npm,' . $id . ',mahasiswa_id',
            ], [
                '*.required'     => 'Kolom wajib diisi',
                'npm.unique'     => 'NPM telah digunakan',
            ]);

            // Data untuk role 2, nilai SKS dan studi diset ke 0
            $data = [
                'dosen_id'      => $request->dosen_id,
                'angkatan_id'   => $request->angkatan_id,
                'nama_mahasiswa' => $request->nama_mahasiswa,
                'npm'           => $request->npm,
            ];
        } else {
            // Jika role tidak diizinkan
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Anda tidak memiliki akses untuk memperbarui data mahasiswa.',
            ]);
        }

        // Update data mahasiswa
        $update = $mahasiswa->update($data);

        // Cek hasil update dan kembalikan notifikasi
        if ($update) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil memperbarui data mahasiswa.',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal memperbarui data mahasiswa.',
            ]);
        }
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
