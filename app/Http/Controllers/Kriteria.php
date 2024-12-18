<?php

namespace App\Http\Controllers;

use App\Models\KriteriaModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Kriteria extends Controller
{
    public function kriteriaPage()
    {
        $title = 'Kriteria';
        $kriteria = KriteriaModel::all();

        return Inertia::render('Admin/KriteriaPage', [
            'kriteria' => $kriteria,
            'title' => $title
        ]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'nama_kriteria' => 'required',
            'bobot' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|max:255',
        ], [
            '*.required' => 'Kolom wajib diisi',
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
            'nama_kriteria' => 'required',
            'bobot' => 'required|numeric|min:0|max:100',
            'type' => 'required|string|max:255',
        ], [
            '*.required' => 'Kolom wajib diisi',
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
