@php
    $isAdmin = auth()->user()->role === 'admin';

    // ---- Tentukan modal mana yang harus terbuka saat halaman dimuat ----
    // 1) Validasi gagal  -> buka lagi modal tambah/edit beserta isian & pesan error
    // 2) Link lama ?tambah=1 / ?edit=ID / ?lihat=ID
    $oldModal = old('_modal');
    $errorModal = null;
    $state = ['modal' => null, 'item' => null];

    if ($oldModal === 'tambah') {
        $errorModal = 'tambah';
        $state['modal'] = 'tambah';
    } elseif ($oldModal && \Illuminate\Support\Str::startsWith($oldModal, 'edit:')) {
        $errorModal = 'edit';
        $state['modal'] = 'edit';
        $state['item'] = [
            'id' => (int) \Illuminate\Support\Str::after($oldModal, 'edit:'),
            'nim' => old('nim'),
            'nama' => old('nama'),
            'program_studi' => old('program_studi'),
            'email' => old('email'),
            'angkatan' => old('angkatan'),
        ];
    } elseif ($awal) {
        $state['modal'] = $awal['mode'];
        $state['item'] = $awal['data'];
    }

    $warna = ['bg-indigo-100 text-indigo-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700', 'bg-violet-100 text-violet-700'];
@endphp

<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Data Mahasiswa</h2>
                <p class="text-sm text-slate-500 mt-1">Kelola data mahasiswa: tambah, ubah, hapus, dan lihat detail.</p>
            </div>

            @if ($isAdmin)
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('buka-tambah'))"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm shadow-indigo-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                    Tambah Mahasiswa
                </button>
            @endif
        </div>
    </x-slot>

    <div
        class="py-8"
        x-data="mahasiswaPage(@js($state), @js(url('/mahasiswa')))"
        @buka-tambah.window="openTambah()"
        @keydown.escape.window="close()"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- NOTIFIKASI --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                     class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <p class="text-sm font-medium flex-1">{{ session('success') }}</p>
                    <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none">&times;</button>
                </div>
            @endif

            {{-- STATISTIK --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Total Mahasiswa</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $total }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Hasil Pencarian</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $mahasiswa->total() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Akses Anda</p>
                        <p class="text-lg font-bold text-slate-800">{{ $isAdmin ? 'Admin (penuh)' : 'User (lihat saja)' }}</p>
                    </div>
                </div>
            </div>

            {{-- TABEL --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

                {{-- Pencarian & filter --}}
                <form method="GET" action="{{ route('mahasiswa.index') }}"
                      class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama, NIM, program studi, atau email..."
                               class="w-full pl-10 rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <select name="angkatan" onchange="this.form.submit()"
                            class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-44">
                        <option value="">Semua angkatan</option>
                        @foreach ($daftarAngkatan as $th)
                            <option value="{{ $th }}" @selected((string) $angkatan === (string) $th)>Angkatan {{ $th }}</option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold transition">Cari</button>
                        @if ($q !== '' || $angkatan)
                            <a href="{{ route('mahasiswa.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                <th class="px-5 py-3">No</th>
                                <th class="px-5 py-3">NIM</th>
                                <th class="px-5 py-3">Mahasiswa</th>
                                <th class="px-5 py-3">Program Studi</th>
                                <th class="px-5 py-3">Angkatan</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($mahasiswa as $mhs)
                                @php
                                    $data = $mhs->only(['id', 'nim', 'nama', 'program_studi', 'email', 'angkatan']);
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-4 text-sm text-slate-400">{{ $mahasiswa->firstItem() + $loop->index }}</td>
                                    <td class="px-5 py-4 text-sm font-mono font-semibold text-slate-700">{{ $mhs->nim }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold {{ $warna[$mhs->id % count($warna)] }}">
                                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($mhs->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800">{{ $mhs->nama }}</p>
                                                <p class="text-xs text-slate-500">{{ $mhs->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-block px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium">{{ $mhs->program_studi }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-slate-600">{{ $mhs->angkatan }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openLihat(@js($data))" title="Lihat detail"
                                                class="p-2 rounded-lg text-sky-600 bg-sky-50 hover:bg-sky-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                                            </button>

                                            @if ($isAdmin)
                                                <button type="button" @click="openEdit(@js($data))" title="Edit"
                                                    class="p-2 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.41-9.41a2 2 0 112.83 2.83L11.83 15H9v-2.83l8.59-8.58z"/></svg>
                                                </button>

                                                <button type="button" @click="openHapus(@js($data))" title="Hapus"
                                                    class="p-2 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="mx-auto w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-3xl mb-4">🎓</div>
                                        @if ($q !== '' || $angkatan)
                                            <h3 class="font-semibold text-slate-700">Data tidak ditemukan</h3>
                                            <p class="text-sm text-slate-400 mt-1">Coba ubah kata kunci atau filter angkatan.</p>
                                        @else
                                            <h3 class="font-semibold text-slate-700">Belum ada data mahasiswa</h3>
                                            <p class="text-sm text-slate-400 mt-1">
                                                {{ $isAdmin ? 'Klik tombol "Tambah Mahasiswa" untuk mulai menambahkan data.' : 'Data akan tampil di sini setelah admin menambahkannya.' }}
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($mahasiswa->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 bg-slate-50">
                        {{ $mahasiswa->links() }}
                    </div>
                @else
                    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 text-xs text-slate-500">
                        Menampilkan {{ $mahasiswa->count() }} data
                    </div>
                @endif
            </div>
        </div>

        {{-- Saran program studi untuk input (diambil dari data yang sudah ada) --}}
        <datalist id="daftar-prodi">
            @foreach (\App\Models\Mahasiswa::query()->distinct()->orderBy('program_studi')->pluck('program_studi') as $prodi)
                <option value="{{ $prodi }}"></option>
            @endforeach
            <option value="Teknik Informatika"></option>
            <option value="Sistem Informasi"></option>
            <option value="Manajemen"></option>
            <option value="Akuntansi"></option>
        </datalist>

        @if ($isAdmin)
            {{-- ================= MODAL TAMBAH ================= --}}
            <div x-cloak x-show="modal === 'tambah'" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="close()"></div>

                    <div x-show="modal === 'tambah'" x-transition
                         class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Tambah Mahasiswa</h3>
                                <p class="text-sm text-slate-500 mt-0.5">Isi data mahasiswa baru, lalu simpan.</p>
                            </div>
                            <button type="button" @click="close()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                        </div>

                        <form method="POST" action="{{ route('mahasiswa.store') }}">
                            @csrf
                            <input type="hidden" name="_modal" value="tambah">

                            <div class="p-6">
                                @include('mahasiswa._fields', ['mode' => 'tambah', 'errorModal' => $errorModal])
                            </div>

                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                                <button type="button" @click="close()" class="px-5 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-sm font-semibold transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ================= MODAL EDIT ================= --}}
            <div x-cloak x-show="modal === 'edit'" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="close()"></div>

                    <div x-show="modal === 'edit'" x-transition
                         class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Edit Mahasiswa</h3>
                                <p class="text-sm text-slate-500 mt-0.5">Perbarui data, lalu simpan perubahan.</p>
                            </div>
                            <button type="button" @click="close()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                        </div>

                        <form method="POST" :action="baseUrl + '/' + (item ? item.id : '')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_modal" :value="'edit:' + (item ? item.id : '')">

                            <div class="p-6">
                                @include('mahasiswa._fields', ['mode' => 'edit', 'errorModal' => $errorModal])
                            </div>

                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                                <button type="button" @click="close()" class="px-5 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-sm font-semibold transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ================= MODAL HAPUS ================= --}}
            <div x-cloak x-show="modal === 'hapus'" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="close()"></div>

                    <div x-show="modal === 'hapus'" x-transition class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 text-center">
                        <div class="mx-auto w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Hapus data mahasiswa?</h3>
                        <p class="text-sm text-slate-500 mt-2">
                            Data <span class="font-semibold text-slate-700" x-text="item ? item.nama : ''"></span>
                            (<span x-text="item ? item.nim : ''"></span>) akan dihapus permanen dan tidak bisa dikembalikan.
                        </p>

                        <form method="POST" :action="baseUrl + '/' + (item ? item.id : '')" class="mt-6 flex justify-center gap-3">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="close()" class="px-5 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-sm font-semibold transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-sm transition">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= MODAL DETAIL ================= --}}
        <div x-cloak x-show="modal === 'lihat'" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="close()"></div>

                <div x-show="modal === 'lihat'" x-transition class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-br from-indigo-600 to-violet-600 px-6 py-8 text-center text-white">
                        <div class="mx-auto w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold"
                             x-text="item && item.nama ? item.nama.charAt(0).toUpperCase() : ''"></div>
                        <h3 class="mt-3 text-lg font-bold" x-text="item ? item.nama : ''"></h3>
                        <p class="text-indigo-100 text-sm font-mono" x-text="item ? item.nim : ''"></p>
                    </div>

                    <dl class="p-6 space-y-4 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Program Studi</dt><dd class="font-semibold text-slate-800 text-right" x-text="item ? item.program_studi : ''"></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Email</dt><dd class="font-semibold text-slate-800 text-right break-all" x-text="item ? item.email : ''"></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Angkatan</dt><dd class="font-semibold text-slate-800" x-text="item ? item.angkatan : ''"></dd></div>
                    </dl>

                    <div class="px-6 pb-6">
                        <button type="button" @click="close()" class="w-full px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mahasiswaPage(awal, baseUrl) {
            return {
                modal: awal.modal,
                item: awal.item,
                baseUrl: baseUrl,

                init() {
                    // Bersihkan ?tambah / ?edit / ?lihat dari URL agar tidak terbuka lagi setelah simpan/hapus
                    const url = new URL(window.location.href);
                    if (['tambah', 'edit', 'lihat'].some(k => url.searchParams.has(k))) {
                        ['tambah', 'edit', 'lihat'].forEach(k => url.searchParams.delete(k));
                        window.history.replaceState({}, '', url);
                    }
                },
                openTambah() { this.item = null; this.modal = 'tambah'; },
                openEdit(m)  { this.item = { ...m }; this.modal = 'edit'; },
                openLihat(m) { this.item = { ...m }; this.modal = 'lihat'; },
                openHapus(m) { this.item = { ...m }; this.modal = 'hapus'; },
                close()      { this.modal = null; },
            };
        }
    </script>
</x-app-layout>
