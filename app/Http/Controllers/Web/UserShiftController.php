<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserShiftModel;
use App\Models\WorkShiftModel;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserShiftController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = UserShiftModel::with(['user.profile', 'shift'])
                ->latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->user->profile->name ?? 'Unknown';
                })
                ->addColumn('employee_id', function ($row) {
                    return $row->user->profile->employee_id ?? 'N/A';
                })
                ->addColumn('department', function ($row) {
                    return $row->user->profile->department ?? 'N/A';
                })
                ->addColumn('shift_name', function ($row) {
                    return $row->shift->name ?? 'Unknown Shift';
                })
                ->addColumn('shift_time', function ($row) {
                    if ($row->shift) {
                        return date('H:i', strtotime($row->shift->start_time)) . ' - ' .
                            date('H:i', strtotime($row->shift->end_time));
                    }
                    return 'N/A';
                })
                ->addColumn('period', function ($row) {
                    $start = Carbon::parse($row->start_date)->format('d M Y');
                    $end = $row->end_date
                        ? Carbon::parse($row->end_date)->format('d M Y')
                        : 'Indefinite';
                    return $start . ' - ' . $end;
                })
                ->addColumn('status', function ($row) {
                    $today = Carbon::today();
                    $startDate = Carbon::parse($row->start_date);
                    $endDate = $row->end_date ? Carbon::parse($row->end_date) : null;

                    if (!$row->is_active) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else if ($today->lt($startDate)) {
                        return '<span class="badge bg-info">Upcoming</span>';
                    } else if ($endDate && $today->gt($endDate)) {
                        return '<span class="badge bg-secondary">Expired</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex gap-2">
                        <button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-warning edit-btn">
                           <i class="ti ti-edit"></i>
                        </button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-btn">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $employees = User::whereHas('profile')
            ->with('profile')
            ->where('is_active', 1)
            ->where('is_admin', 0)
            ->get();

        $shifts = WorkShiftModel::where('is_active', 1)->get();

        return view('user_shifts.user_shifts_index', compact('employees', 'shifts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:work_shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for overlapping assignments
        $overlapping = UserShiftModel::where('user_id', $request->user_id)
            ->where('is_active', 1)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    // If end_date is provided in request
                    if ($request->filled('end_date')) {
                        $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                            ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                            ->orWhere(function ($inner) use ($request) {
                                $inner->where('start_date', '<=', $request->start_date)
                                    ->where('end_date', '>=', $request->end_date);
                            });
                    } else {
                        // If end_date is not provided (indefinite)
                        $q->where('start_date', '>=', $request->start_date)
                            ->orWhere(function ($inner) use ($request) {
                            $inner->where('start_date', '<=', $request->start_date)
                                ->where(function ($i) {
                                    $i->whereNull('end_date')
                                        ->orWhere('end_date', '>=', now());
                                });
                        });
                    }
                });
            })
            ->first();

        if ($overlapping) {
            return response()->json([
                'status' => false,
                'message' => 'Employee already has an active shift assignment during this period.'
            ], 422);
        }

        $assignment = UserShiftModel::create([
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Shift has been assigned successfully!',
            'data' => $assignment
        ]);
    }


    public function show($id)
    {
        $assignment = UserShiftModel::with(['user.profile', 'shift'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $assignment
        ]);
    }

    public function update(Request $request, $id)
    {
        $assignment = UserShiftModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:work_shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $overlapping = UserShiftModel::where('user_id', $assignment->user_id)
            ->where('id', '!=', $id)
            ->where('is_active', 1)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    if ($request->filled('end_date')) {
                        $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                            ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                            ->orWhere(function ($inner) use ($request) {
                                $inner->where('start_date', '<=', $request->start_date)
                                    ->where('end_date', '>=', $request->end_date);
                            });
                    } else {
                        $q->where('start_date', '>=', $request->start_date)
                            ->orWhere(function ($inner) use ($request) {
                                $inner->where('start_date', '<=', $request->start_date)
                                    ->where(function ($i) {
                                        $i->whereNull('end_date')
                                            ->orWhere('end_date', '>=', now());
                                    });
                            });
                    }
                });
            })
            ->first();

        if ($overlapping) {
            return response()->json([
                'status' => false,
                'message' => 'Employee already has an active shift assignment during this period.'
            ], 422);
        }

        $assignment->update([
            'shift_id' => $request->shift_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? $request->is_active : $assignment->is_active,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Shift assignment has been updated successfully!',
            'data' => $assignment
        ]);
    }

    public function destroy($id)
    {
        $assignment = UserShiftModel::findOrFail($id);
        $assignment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Shift assignment has been deleted successfully!'
        ]);
    }
}
