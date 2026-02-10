<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // TAMBAHKAN INI ↓
    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function isAdmin()
    {
        return $this->email === 'admin@perpustakaan.test' || $this->email === 'admin@perpustakaan.sch.id';
    }

    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }
}