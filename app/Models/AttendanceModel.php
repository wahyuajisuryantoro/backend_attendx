<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceModel extends Model
{
    use HasFactory;
    protected $table = "attendances";
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'clock_in_method',
        'clock_out_method',
        'clock_in_photo',
        'clock_out_photo',
        'clock_in_location',
        'clock_out_location',
        'notes'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
