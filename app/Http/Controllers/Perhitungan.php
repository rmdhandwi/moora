<?php

namespace App\Http\Controllers;

use App\Models\AngkatanModel;
use App\Models\KriteriaModel;
use App\Models\MahasiswaModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Perhitungan extends Controller
{
    public function perhitunganPage()
    {
        $title = 'Perhitungan';
        $Angkatan = AngkatanModel::all();
        $mahasiswa = MahasiswaModel::with('dosen', 'angkatan')->get();

        return Inertia::render('Perhitungan/PerhitunganPage', [
            'title' => $title,
            'angkatan' => $Angkatan,
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function store(Request $request, $id)
    {
        // Validasi permintaan yang masuk
        $request->validate([
            'angkatan_id' => 'required|exists:tbl_angkatan,angkatan_id',
        ]);

        // Ambil data mahasiswa yang terkait dengan angkatan_id yang dipilih
        $mahasiswa = MahasiswaModel::where('angkatan_id', $id)->get();

        // Cek apakah ada mahasiswa
        if ($mahasiswa->isEmpty()) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Tidak ada data mahasiswa untuk tahun angkatan yang dipilih.',
            ]);
        }

        // Ambil bobot dan tipe kriteria dari tbl_kriteria
        $kriteria = KriteriaModel::all();

        // Validasi jumlah kriteria
        if ($kriteria->count() < 6) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Jumlah kriteria harus minimal 6.',
            ]);
        }

        // Siapkan data bobot, tipe kriteria (benefit/cost), dan total bobot
        $weights = [];
        $criteriaTypes = [];
        $totalBobot = 0;

        // Tempat untuk menampung nilai kriteria yang didapat
        $kriteriaValues = [];

        foreach ($kriteria as $k) {
            $normalizedKriteriaName = strtolower(str_replace(' ', '', $k->nama_kriteria));
            $weights[$normalizedKriteriaName] = $k->bobot / 100; // Bagi dengan 100
            $criteriaTypes[$normalizedKriteriaName] = $k->type; // Simpan tipe (benefit/cost)
            $totalBobot += $k->bobot; // Hitung total bobot
        }

        // Validasi total bobot
        if ($totalBobot !== 100) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Total bobot kriteria harus sama dengan 100.',
            ]);
        }

        // Langkah 1: Menyimpan nilai kriteria untuk setiap mahasiswa berdasarkan nama_kriteria
        foreach ($mahasiswa as $student) {
            $studentKriteriaValues = [];

            foreach ($weights as $kriteriaName => $weight) {
                foreach ($student->getAttributes() as $column => $value) {
                    $normalizedStudentColumn = strtolower(str_replace('_', '', $column));
                    if ($normalizedStudentColumn === $kriteriaName) {
                        // Simpan nilai kriteria yang sesuai dengan kolom mahasiswa
                        $studentKriteriaValues[$kriteriaName] = $value;
                    }
                }
            }

            // Menampung nilai kriteria untuk mahasiswa ini
            $kriteriaValues[$student->mahasiswa_id] = $studentKriteriaValues;
        }


        // Sekarang kriteriaValues berisi nilai kriteria yang terhubung dengan mahasiswa

        // Langkah 2: Hitung kuadrat dari setiap kriteria untuk normalisasi
        $squaredSums = array_fill_keys(array_keys($weights), 0);

        foreach ($mahasiswa as $student) {
            foreach ($weights as $kriteriaName => $weight) {
                if (isset($kriteriaValues[$student->mahasiswa_id][$kriteriaName])) {
                    $value = $kriteriaValues[$student->mahasiswa_id][$kriteriaName];
                    $squaredSums[$kriteriaName] += pow($value, 2);
                }
            }
        }

        // Langkah 3: Normalisasi matriks keputusan (menggunakan rumus X_ij^' = x_ij / sqrt(Sum x_ij^2))
        $normalizationData = [];
        foreach ($mahasiswa as $student) {
            $studentNormalization = [
                'mahasiswa_id' => $student->mahasiswa_id,
                'nama_mahasiswa' => $student->nama_mahasiswa,
                'npm' => $student->npm,
                'nilai_normalisasi' => [],
            ];

            foreach ($weights as $kriteriaName => $weight) {
                if (isset($kriteriaValues[$student->mahasiswa_id][$kriteriaName])) {
                    $value = $kriteriaValues[$student->mahasiswa_id][$kriteriaName];
                    // Menggunakan rumus normalisasi
                    $normalizedValue = $value / sqrt($squaredSums[$kriteriaName]);
                    // Membatasi hasil normalisasi ke 3 angka di belakang koma
                    $studentNormalization['nilai_normalisasi'][$kriteriaName] = number_format($normalizedValue, 3);
                }
            }

            $normalizationData[] = $studentNormalization;
        }

        // Langkah 4: Hitung nilai optimasi bobot dan MOORA
        $optimizationData = [];
        foreach ($normalizationData as $studentData) {
            $studentOptimization = [
                'mahasiswa_id' => $studentData['mahasiswa_id'],
                'nama_mahasiswa' => $studentData['nama_mahasiswa'],
                'npm' => $studentData['npm'],
                'optimized_values' => [], // Nilai optimasi bobot
                'moora' => 0, // Nilai MOORA
            ];

            // Hitung optimasi bobot untuk setiap kriteria
            $optimizedSum = 0;
            foreach ($studentData['nilai_normalisasi'] as $kriteriaName => $normalizedValue) {
                $weight = $weights[$kriteriaName];
                $optimizedValue = $normalizedValue * $weight; // Optimasi bobot
                // Membatasi nilai optimasi ke 3 angka di belakang koma
                $studentOptimization['optimized_values'][$kriteriaName] = number_format($optimizedValue, 3);
                $optimizedSum += $optimizedValue;
            }

            // Simpan nilai optimasi bobot dan total MOORA
            // Membatasi nilai MOORA ke 3 angka di belakang koma
            $studentOptimization['moora'] = number_format($optimizedSum, 3); // Total MOORA
            $optimizationData[] = $studentOptimization;
        }

        // Urutkan hasil berdasarkan nilai MOORA
        usort($optimizationData, function ($a, $b) {
            return $b['moora'] <=> $a['moora']; // Urutkan dari nilai MOORA tertinggi ke terendah
        });


        // Langkah 5: Tambahkan rank berdasarkan urutan MOORA
        $rank = 1;
        foreach ($optimizationData as &$studentOptimization) {
            $studentOptimization['rank'] = $rank++;
        }

        // Langkah 6: Membagi ke dalam 3 golongan berdasarkan nilai MOORA
        $totalMahasiswa = count($optimizationData);

        // Tentukan batas untuk setiap golongan
        $golongan1Limit = ceil($totalMahasiswa * 0.1); // Top 10%
        $golongan2Limit = ceil($totalMahasiswa * 0.5); // Top 50%

        // Tambahkan golongan ke setiap mahasiswa
        foreach ($optimizationData as $index => &$studentOptimization) {
            if ($index < $golongan1Limit) {
                $studentOptimization['golongan'] = 'Aman';
            } elseif ($index < $golongan2Limit) {
                $studentOptimization['golongan'] = 'Hati-Hati';
            } else {
                $studentOptimization['golongan'] = 'DO/Pindah';
            }
        }


        // Kirim data ke frontend
        $title = 'Hasil Perhitungan';
        return Inertia::render('Perhitungan/DataPerhitungan', [
            'title' => $title,
            'normalizationData' => $normalizationData,
            'optimizationData' => $optimizationData, // Data optimasi bobot dan MOORA
        ]);
    }
}
