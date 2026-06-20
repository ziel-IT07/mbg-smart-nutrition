<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::all();

        return view('siswa.index', compact('siswas'));
    }

    public function store(Request $request)
    {
        $bmi = $request->berat / pow(($request->tinggi / 100), 2);

        if ($bmi < 18.5) {
            $status = 'Kurus';
        } elseif ($bmi < 25) {
            $status = 'Normal';
        } elseif ($bmi < 30) {
            $status = 'Gemuk';
        } else {
            $status = 'Obesitas';
        }

        Siswa::create([
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'umur' => $request->umur,
            'tinggi' => $request->tinggi,
            'berat' => $request->berat,
            'bmi' => round($bmi, 2),
            'status_gizi' => $status,
        ]);

        return redirect('/siswa')->with('success', 'Data berhasil disimpan');
    }
}