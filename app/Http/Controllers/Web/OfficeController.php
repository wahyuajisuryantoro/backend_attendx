<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Models\OfficeLocationModel;
use App\Http\Controllers\Controller;

class OfficeController extends Controller
{
    public function index()
    {
        $officeLocation = OfficeLocationModel::first();
        return view("office_locations.office_locations_index", compact('officeLocation'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'coordinates' => 'required|string|max:255',
            'radius' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);
        
        $officeLocation = OfficeLocationModel::first();
        $officeLocation->update($request->all());
        
        return response()->json(['success' => true, 'message' => 'Data lokasi kantor berhasil diperbarui']);
    }
}
