<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'total' => Mahasiswa::count(),
            'jumlahProdi' => Mahasiswa::distinct('program_studi')->count('program_studi'),
            'angkatanTerbaru' => Mahasiswa::max('angkatan'),
            'perProdi' => Mahasiswa::selectRaw('program_studi, count(*) as jumlah')
                ->groupBy('program_studi')
                ->orderByDesc('jumlah')
                ->limit(5)
                ->get(),
            'terbaru' => Mahasiswa::latest()->limit(5)->get(),
        ]);
    }
}
