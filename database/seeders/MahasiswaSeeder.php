<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['231001', 'Budi Santoso', 'Manajemen Informatika', 'budi@gmail.com', 2023],
            ['231002', 'Siti Aminah', 'Teknologi Rekayasa Internet', 'siti@gmail.com', 2023],
            ['221003', 'Andi Pratama', 'Teknologi Rekayasa Perangkat Lunak', 'andi@gmail.com', 2022],
            ['241004', 'Dewi Lestari', 'Teknologi Rekayasa Elektro', 'dewi@gmail.com', 2024],
            ['241005', 'Rizky Ramadhan', 'Teknologi Rekayasa Internet', 'rizky@gmail.com', 2024],
        ];

        foreach ($data as [$nim, $nama, $prodi, $email, $angkatan]) {
            Mahasiswa::updateOrCreate(
                ['nim' => $nim],
                ['nama' => $nama, 'program_studi' => $prodi, 'email' => $email, 'angkatan' => $angkatan]
            );
        }
    }
}
