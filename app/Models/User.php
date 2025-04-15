<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'username',
        'password',
        'is_admin',
        'is_active',
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
            'is_admin' => 'boolean', 
            'is_active' => 'boolean', 
        ];
    }

    public function profile()
    {
        return $this->hasOne(user_profile::class, 'user_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequestModel::class, 'user_id');
    }

    public function attendance()
    {
        return $this->hasMany(AttendanceModel::class,'user_id');
    }
}
