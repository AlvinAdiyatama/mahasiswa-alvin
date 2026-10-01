<?php

namespace App\Http\Controllers;

use App\Http\Requests\MahasiswaRequest;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Daftar mahasiswa (dengan pencarian, filter angkatan, dan pagination).
     * Form tambah / edit / detail / hapus semuanya ada di halaman ini (modal).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $angkatan = $request->query('angkatan');

        $mahasiswa = Mahasiswa::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', "%{$q}%")
                        ->orWhere('nim', 'like', "%{$q}%")
                        ->orWhere('program_studi', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($angkatan, fn ($query) => $query->where('angkatan', $angkatan))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $daftarAngkatan = Mahasiswa::query()
            ->select('angkatan')
            ->distinct()
            ->orderByDesc('angkatan')
            ->pluck('angkatan');

        // Dipakai saat halaman dibuka lewat link lama (?tambah=1 / ?edit=ID / ?lihat=ID)
        $awal = null;
        if ($request->filled('edit') || $request->filled('lihat')) {
            $target = Mahasiswa::find($request->query('edit') ?? $request->query('lihat'));
            if ($target) {
                $awal = [
                    'mode' => $request->filled('edit') ? 'edit' : 'lihat',
                    'data' => $target->only(['id', 'nim', 'nama', 'program_studi', 'email', 'angkatan']),
                ];
            }
        } elseif ($request->boolean('tambah')) {
            $awal = ['mode' => 'tambah', 'data' => null];
        }

        return view('mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'total' => Mahasiswa::count(),
            'daftarAngkatan' => $daftarAngkatan,
            'q' => $q,
            'angkatan' => $angkatan,
            'awal' => $awal,
        ]);
    }

    // Link lama tetap jalan: dialihkan ke halaman data mahasiswa dan modalnya dibuka otomatis
    public function create()
    {
        return redirect()->route('mahasiswa.index', ['tambah' => 1]);
    }

    public function show(Mahasiswa $mahasiswa)
    {
        return redirect()->route('mahasiswa.index', ['lihat' => $mahasiswa->id]);
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        return redirect()->route('mahasiswa.index', ['edit' => $mahasiswa->id]);
    }

    // MENYIMPAN data baru ke database
    public function store(MahasiswaRequest $request)
    {
        $mahasiswa = Mahasiswa::create($request->validated());

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', "Mahasiswa {$mahasiswa->nama} berhasil ditambahkan.");
    }

    // MENGUBAH data yang sudah ada
    public function update(MahasiswaRequest $request, Mahasiswa $mahasiswa)
    {
        $mahasiswa->update($request->validated());

        return redirect()
            ->back(fallback: route('mahasiswa.index'))
            ->with('success', "Data {$mahasiswa->nama} berhasil diperbarui.");
    }

    // MENGHAPUS data
    public function destroy(Mahasiswa $mahasiswa)
    {
        $nama = $mahasiswa->nama;
        $mahasiswa->delete();

        return redirect()
            ->back(fallback: route('mahasiswa.index'))
            ->with('success', "Data {$nama} berhasil dihapus.");
    }
}
