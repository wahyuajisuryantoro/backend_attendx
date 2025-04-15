<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WorkShiftModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WorkShiftsController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = WorkShiftModel::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $statusBadge = $row->is_active ? 
                        '<span class="badge bg-success">Active</span>' : 
                        '<span class="badge bg-danger">Inactive</span>';
                    return $statusBadge;
                })
                ->addColumn('action', function($row){
                    $actionBtn = '<div class="d-flex gap-2">
                        <button type="button" data-id="'.$row->id.'" class="btn btn-sm btn-warning edit-btn">
                           <i class="ti ti-edit"></i>
                        </button>
                        <button type="button" data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->addColumn('time_range', function($row){
                    return date('H:i', strtotime($row->start_time)) . ' - ' . date('H:i', strtotime($row->end_time));
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        
        return view('work_shifts.work_shift_index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_threshold_minutes' => 'required|integer|min:0|max:180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $workShift = WorkShiftModel::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'late_threshold_minutes' => $request->late_threshold_minutes,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Work shift has been created successfully!',
            'data' => $workShift
        ]);
    }

    public function get($id)
    {
        $workShift = WorkShiftModel::findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $workShift
        ]);
    }

    public function update(Request $request, $id)
    {
        $workShift = WorkShiftModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_threshold_minutes' => 'required|integer|min:0|max:180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $workShift->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'late_threshold_minutes' => $request->late_threshold_minutes,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Work shift has been updated successfully!',
            'data' => $workShift
        ]);
    }

    public function destroy($id)
    {
        $workShift = WorkShiftModel::findOrFail($id);

        if ($workShift->userShifts()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'This shift is still assigned to employees and cannot be deleted!'
            ], 422);
        }
        
        $workShift->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Work shift has been deleted successfully!'
        ]);
    }
}