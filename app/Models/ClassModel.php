<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    // Tentukan nama tabel (karena nama model beda dengan tabel)
    protected $table = 'classes';
    
    // Kolom yang bisa diisi massal
    protected $fillable = [
        'class_name',
        'grade',
        'major',
        'academic_year'
    ];

    // Relasi ke members (satu kelas punya banyak anggota)
    public function members()
    {
        return $this->hasMany(Member::class, 'class_id');
    }
}