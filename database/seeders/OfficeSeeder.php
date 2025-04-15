<?php

namespace Database\Seeders;

use App\Models\OfficeLocationModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        OfficeLocationModel::create([
            'name' => 'Bengkel Las Suryo',
            'address' => 'Temanggung, Jawa Tengah, Indonesia',
            'coordinates' => '7.272229,110.2321858',
            'radius' => 100,
            'is_active' => true
        ]);
    }
}
