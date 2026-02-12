<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'nip',
        'class_id',
        'enrollment_year',
        'phone',
        'gender',
        'type',
        'status'
    ];

    // relasi ke user (satu anggota punya satu user account)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke class (satu anggota punya satu kelas)
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    // relasi ke borrows (satu anggota punya banyak peminjaman)
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    // format nama lengkap
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }
}