<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    // tentukan nama tabel (karena nama model beda dengan tabel)
    protected $table = 'classes';
    
    // kolom yang bisa diisi massal
    protected $fillable = [
        'class_name',
        'grade',
        'major',
        'academic_year'
    ];

    // relasi ke members (satu kelas punya banyak anggota)
    public function members()
    {
        return $this->hasMany(Member::class, 'class_id');
    }
}