<?php

namespace App\Http\Requests;

use App\Models\Mahasiswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MahasiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang boleh menambah / mengubah data mahasiswa
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        // Saat update, abaikan baris mahasiswa yang sedang diedit agar NIM/email miliknya sendiri tidak dianggap duplikat
        $mahasiswa = $this->route('mahasiswa');
        $id = $mahasiswa?->id;

        return [
            'nim' => [
                'required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/',
                Rule::unique('mahasiswas', 'nim')->ignore($id),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'program_studi' => ['required', 'string', Rule::in(Mahasiswa::PROGRAM_STUDI)],
            'email' => [
                'required', 'email', 'max:100',
                Rule::unique('mahasiswas', 'email')->ignore($id),
            ],
            'angkatan' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'email' => 'Format email tidak valid.',
            'integer' => ':attribute harus berupa angka.',
            'angkatan.min' => 'Angkatan minimal :min.',
            'angkatan.max' => 'Angkatan maksimal :max.',
            'program_studi.in' => 'Pilih salah satu program studi yang tersedia.',
            'nim.regex' => 'NIM hanya boleh berisi huruf dan angka.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah dipakai mahasiswa lain.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nim' => 'NIM',
            'nama' => 'Nama',
            'program_studi' => 'Program studi',
            'email' => 'Email',
            'angkatan' => 'Angkatan',
        ];
    }
}
