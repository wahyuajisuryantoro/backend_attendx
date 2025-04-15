<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserShiftModel extends Model
{
    use HasFactory;

    protected $table = 'user_shifts';
    
    protected $fillable = [
        'user_id',
        'shift_id',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(WorkShiftModel::class, 'shift_id');
    }
}
