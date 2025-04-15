<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLocationModel extends Model
{
    use HasFactory;
    protected $table = "office_locations";
    protected $fillable = [
        'name',
        'address',
        'coordinates',
        'radius',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}