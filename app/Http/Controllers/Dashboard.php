<?php

namespace App\Http\Controllers;

use App\Models\AngkatanModel;
use App\Models\DosenModel;
use App\Models\KriteriaModel;
use App\Models\MahasiswaModel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Dashboard extends Controller
{
  public function DashboardPage()
  {
    $title = 'Dashboard';
    $dosen = DosenModel::count();
    $kriteria = KriteriaModel::count();
    $user = User::count();
    $mahasiswa = MahasiswaModel::count();
    $angkatan = AngkatanModel::count();

    // Data untuk chart berdasarkan angkatan
    $angkatanData = MahasiswaModel::selectRaw('angkatan_id, COUNT(*) as total')
      ->groupBy('angkatan_id')
      ->with('angkatan') // Pastikan ada relasi angkatan
      ->get()
      ->map(function ($item) {
        return [
          'tahun' => $item->angkatan->tahun_angkatan,
          'total' => $item->total,
        ];
      });

    return Inertia::render('Admin/DashboardPage', [
      'title' => $title,
      'dosen' => $dosen,
      'kriteria' => $kriteria,
      'user' => $user,
      'mahasiswa' => $mahasiswa,
      'angkatan' => $angkatan,
      'angkatanData' => $angkatanData,
    ]);
  }
}
