<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class user_profile extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "users_profile";
    protected $fillable = [
        "user_id",
        "name",
        "employee_id",
        "department",
        "position",
        "email",
        "phone",
        "address",
        "profile_photo",
        "join_date"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
