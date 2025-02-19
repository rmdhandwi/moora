<?php

namespace App\Http\Controllers;

use App\Models\DosenModel;
use App\Models\KriteriaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Kriteria extends Controller
{
    public function kriteriaPage()
    {
        $title = 'Kriteria';
        $kriteria = KriteriaModel::all();
        $currentUser = Auth::user();

        $username = DosenModel::where('user_id', $currentUser->user_id)
            ->value('nama_dosen');


        return Inertia::render('Admin/KriteriaPage', [
            'kriteria' => $kriteria,
            'title' => $title,
            'username' => $username,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'nama_kriteria' => 'required|unique:tbl_kriteria,nama_kriteria',
            'bobot' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|max:255',
        ], [
            '*.required' => 'Kolom wajib diisi',
            'nama_kriteria.unique' => 'Nama kriteria telah digunakan',
            'bobot.max' => 'Bobot tidak boleh lebih dari 100',
            'bobot.min' => 'Bobot tidak boleh kurang dari 0',
        ]);

        // Calculate the total bobot from existing records
        $totalBobot = KriteriaModel::sum('bobot');
        $sisabobot = 100 - $totalBobot;        // Check if adding the new bobot exceeds 100
        if (($totalBobot + $request->bobot) > 100) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Total bobot tidak boleh lebih dari 100. Sisa bobot : ' . $sisabobot,
            ]);
        }

        // Create a new KriteriaModel instance
        $insert = KriteriaModel::create([
            'nama_kriteria' => ucwords($request->nama_kriteria),
            'bobot' => $request->bobot,
            'type' => $request->type
        ]);

        // Check if the insert was successful
        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Berhasil membuat kriteria.',
            ]);
        } else {
            // If the insert failed
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Gagal membuat kriteria.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'nama_kriteria' => 'required|unique:tbl_kriteria,nama_kriteria,' . $id . ',kriteria_id',
            'bobot' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|max:255',
        ], [
            '*.required' => 'Kolom wajib diisi',
            'nama_kriteria.unique' => 'Nama kriteria telah digunakan',
            'bobot.max' => 'Bobot tidak boleh lebih dari 100',
            'bobot.min' => 'Bobot tidak boleh kurang dari 0',
        ]);

        $kriteria = KriteriaModel::findOrFail($id);

        $totalBobot = KriteriaModel::where('kriteria_id', '!=', $id)->sum('bobot');
        $sisabobot = 100 - $totalBobot;

        if (($totalBobot + $request->bobot) > 100) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Total bobot tidak boleh lebih dari 100. Sisa bobot: ' . $sisabobot,
            ]);
        }

        // Update the KriteriaModel instance
        $kriteria->update([
            'nama_kriteria' => ucwords($request->nama_kriteria),
            'bobot' => $request->bobot,
            'type' => $request->type
        ]);

        // Check if the update was successful
        return redirect()->back()->with([
            'notif_status' => 'success',
            'message' => 'Berhasil mengupdate kriteria.',
        ]);
    }
}
