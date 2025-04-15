<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequestModel extends Model
{
    use HasFactory;
    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'approved_by',
        'rejection_reason',
        'attachment',
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
}
