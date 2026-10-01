<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    // Daftar program studi yang tersedia (dipakai di form dan validasi)
    public const PROGRAM_STUDI = [
        'Manajemen Informatika',
        'Teknologi Rekayasa Internet',
        'Teknologi Rekayasa Elektro',
        'Teknologi Rekayasa Perangkat Lunak',
    ];

    protected $fillable = [
        'nim',
        'nama',
        'program_studi',
        'email',
        'angkatan',
    ];
}