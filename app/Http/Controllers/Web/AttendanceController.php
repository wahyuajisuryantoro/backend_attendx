<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AttendanceModel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class AttendanceController extends Controller
{
    public function index()
    {
        $totalAttendances = AttendanceModel::count();
        $totalEmployees = User::where('is_admin', 0)->count();
        $onTimeCount = AttendanceModel::whereNotNull('clock_in')
            ->whereRaw('TIME(clock_in) <= ?', ['09:00:00'])
            ->count();
        $lateCount = AttendanceModel::whereNotNull('clock_in')
            ->whereRaw('TIME(clock_in) > ?', ['09:00:00'])
            ->count();
        $topDepartmentData = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->join('users_profile', 'users.id', '=', 'users_profile.user_id')
            ->select('users_profile.department', DB::raw('count(*) as total'))
            ->groupBy('users_profile.department')
            ->orderBy('total', 'desc')
            ->first();

        $topDepartment = $topDepartmentData ? $topDepartmentData->department : 'N/A';
        $topCount = $topDepartmentData ? $topDepartmentData->total : 0;

        return view('attendance.report_attendance', compact(
            'totalAttendances',
            'totalEmployees',
            'onTimeCount',
            'lateCount',
            'topDepartment',
            'topCount'
        ));
    }

    public function getAttendanceData(Request $request)
    {
        $query = AttendanceModel::with(['user.profile'])
            ->select('attendances.*');

        if ($request->has('start_date') && $request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        return DataTables::of($query)
            ->addColumn('employee_name', function ($attendance) {
                return $attendance->user->profile->name ?? 'N/A';
            })
            ->addColumn('actions', function ($attendance) {
                return '
                    <div class="d-flex gap-2">
                        <a href="' . route('attendance.report.show', $attendance->id) . '" class="btn btn-sm btn-info">
                            <i class="ti ti-eye"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger delete-attendance" data-id="' . $attendance->id . '">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->editColumn('date', function ($attendance) {
                return $attendance->date->format('d M Y');
            })
            ->editColumn('clock_in', function ($attendance) {
                return $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') : 'N/A';
            })
            ->editColumn('clock_out', function ($attendance) {
                return $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s') : 'N/A';
            })
            ->filterColumn('employee_name', function($query, $keyword) {
                $query->whereHas('user.profile', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['actions'])
            ->make(true);
    }



    public function show($id)
    {
        $attendance = AttendanceModel::with('user.profile')->findOrFail($id);
        return view('attendance.show_attendance', compact('attendance'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');

        $query = AttendanceModel::with('user.profile')
            ->select('attendances.*');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $formattedAttendances = [];
        foreach ($attendances as $attendance) {
            $workDuration = 'N/A';
            if ($attendance->clock_in && $attendance->clock_out) {
                $clockIn = Carbon::parse($attendance->clock_in);
                $clockOut = Carbon::parse($attendance->clock_out);
                if ($clockOut->lt($clockIn)) {
                    $clockOut->addDay();
                }

                $diffInMinutes = $clockOut->diffInMinutes($clockIn);
                $diffInHours = floor($diffInMinutes / 60);
                $remainingMinutes = $diffInMinutes % 60;

                $workDuration = $diffInHours . ' hours ' . $remainingMinutes . ' minutes';
            }

            $status = 'Absent';
            if ($attendance->clock_in) {
                $clockInTime = Carbon::parse($attendance->clock_in)->format('H:i:s');
                $status = $clockInTime <= '09:00:00' ? 'On Time' : 'Late';
            }

            $location = 'N/A';
            if ($attendance->clock_in_location) {
                try {
                    $locationData = json_decode($attendance->clock_in_location, true);
                    if (isset($locationData['address'])) {
                        $location = $locationData['address'];
                    }
                } catch (\Exception $e) {
                    $location = 'Error parsing location';
                }
            }

            $formattedAttendances[] = [
                'employee_id' => $attendance->user->profile->employee_id ?? 'N/A',
                'name' => $attendance->user->profile->name ?? 'N/A',
                'department' => $attendance->user->profile->department ?? 'N/A',
                'position' => $attendance->user->profile->position ?? 'N/A',
                'date' => $attendance->date->format('Y-m-d'),
                'day' => $attendance->date->format('l'),
                'clock_in' => $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i:s') : 'N/A',
                'clock_out' => $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i:s') : 'N/A',
                'status' => $status,
                'work_duration' => $workDuration,
                'location' => $location,
                'notes' => $attendance->notes ?? 'N/A'
            ];
        }

        $reportTitle = 'Attendance Report';
        if ($startDate && $endDate) {
            $reportTitle .= ' (' . $startDate . ' to ' . $endDate . ')';
        }

        $userName = '';
        if ($userId) {
            $user = User::with('profile')->find($userId);
            if ($user && $user->profile) {
                $userName = $user->profile->name;
                $reportTitle .= ' for ' . $userName;
            }
        }

        $data = [
            'title' => $reportTitle,
            'date' => date('Y-m-d'),
            'attendances' => $formattedAttendances
        ];

        $pdf = PDF::loadView('pdf.attendance_report', $data);
        return $pdf->download('attendance_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');

        
        $query = AttendanceModel::with('user.profile')
            ->select('attendances.*');

        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        
        $attendances = $query->orderBy('date', 'desc')->get();

        
        return Excel::download(new class ($attendances) implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize {
            private $attendances;

            public function __construct($attendances)
            {
                $this->attendances = $attendances;
            }

            public function collection()
            {
                return $this->attendances;
            }

            public function headings(): array
            {
                return [
                    'Employee ID',
                    'Employee Name',
                    'Department',
                    'Position',
                    'Date',
                    'Day',
                    'Clock In',
                    'Clock Out',
                    'Clock In Method',
                    'Clock Out Method',
                    'Status',
                    'Work Duration',
                    'Location',
                    'Notes'
                ];
            }

            public function map($attendance): array
            {
                
                $workDuration = 'N/A';
                if ($attendance->clock_in && $attendance->clock_out) {
                    $clockIn = Carbon::parse($attendance->clock_in);
                    $clockOut = Carbon::parse($attendance->clock_out);

                    
                    if ($clockOut->lt($clockIn)) {
                        $clockOut->addDay();
                    }

                    $diffInMinutes = $clockOut->diffInMinutes($clockIn);
                    $diffInHours = floor($diffInMinutes / 60);
                    $remainingMinutes = $diffInMinutes % 60;

                    $workDuration = $diffInHours . ' hours ' . $remainingMinutes . ' minutes';
                }

                
                $status = 'Absent';
                if ($attendance->clock_in) {
                    $clockInTime = Carbon::parse($attendance->clock_in)->format('H:i:s');
                    $status = $clockInTime <= '09:00:00' ? 'On Time' : 'Late';
                }

                
                $location = 'N/A';
                if ($attendance->clock_in_location) {
                    try {
                        $locationData = json_decode($attendance->clock_in_location, true);
                        if (isset($locationData['address'])) {
                            $location = $locationData['address'];
                            if (isset($locationData['distance'])) {
                                $location .= ' (Distance: ' . number_format($locationData['distance'], 2) . ' meters)';
                            }
                        }
                    } catch (\Exception $e) {
                        $location = 'Error parsing location';
                    }
                }

                return [
                    $attendance->user->profile->employee_id ?? 'N/A',
                    $attendance->user->profile->name ?? 'N/A',
                    $attendance->user->profile->department ?? 'N/A',
                    $attendance->user->profile->position ?? 'N/A',
                    $attendance->date->format('Y-m-d'),
                    $attendance->date->format('l'),
                    $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i:s') : 'N/A',
                    $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i:s') : 'N/A',
                    $attendance->clock_in_method ?? 'N/A',
                    $attendance->clock_out_method ?? 'N/A',
                    $status,
                    $workDuration,
                    $location,
                    $attendance->notes ?? 'N/A'
                ];
            }
        }, 'attendance_report_' . now()->format('Y-m-d') . '.xlsx');
    }
    public function employeeReport($userId)
    {
        $user = User::with('profile')->findOrFail($userId);

        
        $totalDays = AttendanceModel::where('user_id', $userId)->count();
        $onTimeDays = AttendanceModel::where('user_id', $userId)
            ->whereNotNull('clock_in')
            ->whereRaw('TIME(clock_in) <= ?', ['09:00:00'])
            ->count();
        $lateDays = AttendanceModel::where('user_id', $userId)
            ->whereNotNull('clock_in')
            ->whereRaw('TIME(clock_in) > ?', ['09:00:00'])
            ->count();
        $absentDays = AttendanceModel::where('user_id', $userId)
            ->whereNull('clock_in')
            ->count();

        
        $monthlyData = AttendanceModel::where('user_id', $userId)
            ->whereYear('date', Carbon::now()->year)
            ->select(DB::raw('MONTH(date) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->get();

        return view('attendance.employee_individual_report', compact(
            'user',
            'totalDays',
            'onTimeDays',
            'lateDays',
            'absentDays',
            'monthlyData'
        ));
    }

    public function getLocation($id)
    {
        try {
            $attendance = AttendanceModel::findOrFail($id);
            return response()->json([
                'success' => true,
                'clock_in_location' => $attendance->clock_in_location,
                'clock_out_location' => $attendance->clock_out_location
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found'
            ], 404);
        }
    }

}
