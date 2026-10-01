{{--
    Field form mahasiswa, dipakai oleh modal Tambah dan modal Edit.
    $mode = 'tambah' | 'edit'
    - tambah : nilai lama (old) diisi dari server bila validasi gagal
    - edit   : nilai diikat ke objek Alpine "item"
--}}
@php
    $isEdit = $mode === 'edit';
    $showErrors = ($errorModal ?? null) === $mode;
    $inputClass = 'block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-slate-400';

    $fields = [
        ['name' => 'nim', 'label' => 'NIM', 'type' => 'text', 'placeholder' => 'Contoh: 231001', 'extra' => 'maxlength=20'],
        ['name' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'placeholder' => 'Contoh: Budi Santoso', 'extra' => 'maxlength=100'],
        ['name' => 'program_studi', 'label' => 'Program Studi', 'type' => 'select', 'placeholder' => '', 'extra' => ''],
        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'placeholder' => 'Contoh: mahasiswa@gmail.com', 'extra' => 'maxlength=100'],
        ['name' => 'angkatan', 'label' => 'Angkatan', 'type' => 'number', 'placeholder' => 'Contoh: ' . date('Y'), 'extra' => 'min=2000 max=' . (date('Y') + 1)],
    ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    @foreach ($fields as $f)
        <div class="{{ in_array($f['name'], ['nama', 'email']) ? 'sm:col-span-2' : '' }}">
            <label for="{{ $mode }}-{{ $f['name'] }}" class="block text-sm font-semibold text-slate-700 mb-1.5">
                {{ $f['label'] }} <span class="text-rose-500">*</span>
            </label>

            @if ($f['type'] === 'select')
                <select
                    id="{{ $mode }}-{{ $f['name'] }}"
                    name="{{ $f['name'] }}"
                    required
                    @if ($isEdit) x-model="item.{{ $f['name'] }}" @endif
                    class="{{ $inputClass }} {{ $showErrors && $errors->has($f['name']) ? '!border-rose-400 !ring-rose-200' : '' }}"
                >
                    <option value="" disabled @selected(! $isEdit && ! ($showErrors && old($f['name'])))>-- Pilih program studi --</option>
                    @foreach (\App\Models\Mahasiswa::PROGRAM_STUDI as $prodi)
                        <option value="{{ $prodi }}" @selected(! $isEdit && $showErrors && old($f['name']) === $prodi)>{{ $prodi }}</option>
                    @endforeach
                </select>
            @else
                <input
                    type="{{ $f['type'] }}"
                    id="{{ $mode }}-{{ $f['name'] }}"
                    name="{{ $f['name'] }}"
                    placeholder="{{ $f['placeholder'] }}"
                    {!! $f['extra'] !!}
                    required
                    @if ($isEdit)
                        x-model="item.{{ $f['name'] }}"
                    @else
                        value="{{ $showErrors ? old($f['name']) : '' }}"
                    @endif
                    class="{{ $inputClass }} {{ $showErrors && $errors->has($f['name']) ? '!border-rose-400 !ring-rose-200' : '' }}"
                >
            @endif

            @if ($showErrors && $errors->has($f['name']))
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $errors->first($f['name']) }}</p>
            @endif
        </div>
    @endforeach
</div>
