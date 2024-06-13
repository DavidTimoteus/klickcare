<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'tbl_users'; // Mengarah ke tabel 'tbl_users'

    protected $fillable = [
        'name', 'noId', 'email', 'password', 'gender', 'birthDate', 'address', 'phoneNumber', 'role', 'create_id', 'update_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
