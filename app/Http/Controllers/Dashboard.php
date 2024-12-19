<?php

namespace App\Http\Controllers;

use App\Models\AngkatanModel;
use App\Models\DosenModel;
use App\Models\KriteriaModel;
use App\Models\MahasiswaModel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function Laravel\Prompts\select;

class Dashboard extends Controller
{
  public function DashboardPage()
  {
    $currentUser = auth()->user();
    $Dosen = DosenModel::where('user_id', $currentUser->user_id)->first();

    $title = 'Dashboard';
    $dosen = DosenModel::count();
    $kriteria = KriteriaModel::count();
    $user = User::count();
    $mahasiswa = MahasiswaModel::count();
    $angkatan = AngkatanModel::count();

    // Hitung mahasiswa berdasarkan dosen jika role == 3
    $bydosen = $currentUser->role == 3 && $Dosen
      ? MahasiswaModel::where('dosen_id', $Dosen->dosen_id)->count()
      : null;

    // Data untuk chart berdasarkan angkatan
    $angkatanData = MahasiswaModel::selectRaw('angkatan_id, COUNT(*) as total')
      ->groupBy('angkatan_id')
      ->with('angkatan') // Pastikan relasi angkatan terdefinisi di MahasiswaModel
      ->get()
      ->map(function ($item) {
        return [
          'tahun' => $item->angkatan->tahun_angkatan ?? 'Unknown', // Handle null angkatan
          'total' => $item->total,
        ];
      });

    // Kirim data ke view
    return Inertia::render('Admin/DashboardPage', [
      'title' => $title,
      'dosen' => $dosen,
      'kriteria' => $kriteria,
      'user' => $user,
      'mahasiswa' => $mahasiswa,
      'angkatan' => $angkatan,
      'angkatanData' => $angkatanData,
      'bydosen' => $bydosen, // Tambahkan nilai ini hanya jika ada
    ]);
  }

}
