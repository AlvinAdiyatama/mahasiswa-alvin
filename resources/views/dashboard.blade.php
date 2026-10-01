<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
        <p class="text-sm text-slate-500 mt-1">Halo, {{ auth()->user()->name }}! Berikut ringkasan data mahasiswa.</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Kartu statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl p-6 text-white bg-gradient-to-br from-indigo-600 to-violet-600 shadow-lg shadow-indigo-200">
                    <p class="text-indigo-100 text-sm">Total Mahasiswa</p>
                    <p class="text-4xl font-bold mt-2">{{ $total }}</p>
                </div>
                <div class="rounded-2xl p-6 bg-white border border-slate-200">
                    <p class="text-slate-500 text-sm">Program Studi</p>
                    <p class="text-4xl font-bold text-slate-800 mt-2">{{ $jumlahProdi }}</p>
                </div>
                <div class="rounded-2xl p-6 bg-white border border-slate-200">
                    <p class="text-slate-500 text-sm">Angkatan Terbaru</p>
                    <p class="text-4xl font-bold text-slate-800 mt-2">{{ $angkatanTerbaru ?? '-' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Mahasiswa per prodi --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-800 mb-4">Mahasiswa per Program Studi</h3>

                    @forelse ($perProdi as $row)
                        <div class="mb-4 last:mb-0">
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="font-medium text-slate-700">{{ $row->program_studi }}</span>
                                <span class="text-slate-500">{{ $row->jumlah }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ $total > 0 ? round($row->jumlah / $total * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>

                {{-- Terbaru ditambahkan --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-800">Terakhir Ditambahkan</h3>
                        <a href="{{ route('mahasiswa.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Lihat semua &rarr;</a>
                    </div>

                    <ul class="divide-y divide-slate-100">
                        @forelse ($terbaru as $mhs)
                            <li class="py-3 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($mhs->nama, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $mhs->nama }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $mhs->nim }} · {{ $mhs->program_studi }}</p>
                                </div>
                                <span class="text-xs text-slate-400">{{ $mhs->angkatan }}</span>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-slate-400">Belum ada data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
