<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShiftModel extends Model
{
    use HasFactory;

    protected $table = 'work_shifts';
    
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_threshold_minutes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'late_threshold_minutes' => 'integer'
    ];

    public function userShifts()
    {
        return $this->hasMany(UserShiftModel::class, 'shift_id');
    }
}