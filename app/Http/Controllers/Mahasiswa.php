<?php

namespace App\Http\Controllers;

use App\Models\AngkatanModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Mahasiswa extends Controller
{
    public function mahasiswaPage()
    {
        $title = 'Mahasiswa';
        $currentUser = Auth::user(); // Mendapatkan user yang sedang login

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
        $currentUser = Auth::user();
        $angkatan = AngkatanModel::all();
        $dosen = DosenModel::all();
        // Ambil nama dosen dan tahun angkatan yang terkait
        $usernameWithAngkatan = DosenModel::where('user_id', $currentUser->user_id)
        ->with(['angkatan' => function ($query) {
            $query->select('dosen_id', 'tahun_angkatan'); // Kolom yang ingin diambil dari tbl_angkatan
        }])
            ->select('dosen_id', 'nama_dosen') // Kolom yang ingin diambil dari DosenModel
            ->first();

        if ($usernameWithAngkatan) {
            $namaDosen = $usernameWithAngkatan->nama_dosen;
            $tahunAngkatan = $usernameWithAngkatan->angkatan->tahun_angkatan ?? 'Tidak ada data angkatan';
        } else {
            $namaDosen = 'Tidak ditemukan';
            $tahunAngkatan = 'Tidak ditemukan';
        }


        return Inertia::render('Mahasiswa/MahasiswaPage', [
            'title' => $title,
            'angkatan' => $angkatan,
            'dosen'    => $dosen,
            'mahasiswa' => $mahasiswa,
            'username' => $namaDosen,
            'tahun' => $tahunAngkatan
        ]);
    }


    public function store(Request $request)
    {
        $currentUser = Auth::user(); // Mendapatkan user yang sedang login

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
        $currentUser = Auth::user(); // Mendapatkan user yang sedang login

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

    public function uploadCSV(Request $request)
    {
        // Validasi file yang diunggah
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ], [
            'file.required' => 'File harus diunggah.',
            'file.mimes' => 'Hanya file CSV yang diperbolehkan.'
        ]);

        try {
            $file = $request->file('file');
            $csvData = array_map('str_getcsv', file($file));

            // Lewati baris pertama jika itu adalah header
            $header = array_shift($csvData);

            // Ambil kode terakhir dari tabel, atau mulai dari 'MHS0001'
            $lastMahasiswa = MahasiswaModel::orderBy('mahasiswa_id', 'desc')->first();
            $lastNumber = $lastMahasiswa ? intval(substr($lastMahasiswa->mahasiswa_id, 3)) : 0;


            // Array untuk menyimpan npm yang sudah diproses
            $processedNPM = [];

            foreach ($csvData as $row) {
                $npm = $row[0]; // Pastikan NPM ada di kolom pertama
                $tahunId = $row[8]; // Tahun dari CSV (row[8])

                $lastNumber++; // Increment kode mahasiswa berdasarkan jumlah data di CSV
                $newMahasiswaId = 'MHS' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

                // Validasi apakah nilai pada row[2] hingga row[7] adalah integer
                for ($i = 2; $i <= 7; $i++) {
                    if (!ctype_digit($row[$i])) { // ctype_digit memastikan bahwa nilai adalah string numerik tanpa tanda negatif
                        return redirect()->back()->with([
                            'notif_status' => 'error',
                            'message' => "Kolom pada baris dengan NPM $npm dan data ke $i harus berupa angka!"
                        ]);
                    }
                }

                // Cek apakah npm sudah ada dalam array (untuk cek duplikasi dalam file)
                if (in_array($npm, $processedNPM)) {
                    return redirect()->back()->with([
                        'notif_status' => 'error',
                        'message' => "NPM $npm duplikat dalam file CSV!"
                    ]);
                }

                // Cek apakah npm sudah ada di database
                $existingData = MahasiswaModel::where('npm', $npm)->first();
                if ($existingData) {
                    return redirect()->back()->with([
                        'notif_status' => 'error',
                        'message' => "NPM $npm sudah terdaftar! dengan Nama: {$existingData->nama_mahasiswa}"
                    ]);
                }

                // Cek apakah tahun_angkatan ada di tabel angkatan
                $angkatan = AngkatanModel::where('tahun_angkatan', $tahunId)->first();
                if (!$angkatan) {
                    return redirect()->back()->with([
                        'notif_status' => 'error',
                        'message' => "Angkatan $tahunId tidak ditemukan di tabel angkatan!"
                    ]);
                }

                // Cek apakah dosen ada di tabel dosen
                $dosen = DosenModel::where('dosen_id', $angkatan->dosen_id)->first();
                if (!$dosen) {
                    return redirect()->back()->with([
                        'notif_status' => 'error',
                        'message' => "Dosen dengan kode $angkatan->dosen_id tidak ditemukan di tabel dosen!"
                    ]);
                }

                // Gunakan id  yang ditemukan
                $angkatanId = $angkatan->angkatan_id;
                $dosenId = $angkatan->dosen_id;

                // Pemetaan kolom CSV ke kolom database
                MahasiswaModel::create([
                    'mahasiswa_id' => $newMahasiswaId,
                    'npm' => $npm,
                    'nama_mahasiswa' => $row[1],
                    'sks_tempuh' => $row[2],
                    'sks_sisa' => $row[3],
                    'studi_tempuh' => $row[4],
                    'studi_sisa' => $row[5],
                    'sks_total' => $row[6],
                    'studi_total' => $row[7],
                    'dosen_id' => $dosenId,
                    'angkatan_id' => $angkatanId,
                ]);

                // Tambahkan npm yang telah diproses ke dalam array
                $processedNPM[] = $npm;
            }

            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'File CSV berhasil diunggah dan data berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ]);
        }
    }
}
