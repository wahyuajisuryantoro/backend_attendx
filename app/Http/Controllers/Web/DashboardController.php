<?php

namespace App\Http\Controllers\Web;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserShiftModel;
use App\Models\AttendanceModel;
use App\Models\LeaveRequestModel;
use Illuminate\Support\Facades\DB;
use App\Models\OfficeLocationModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pendingLeaveRequests = LeaveRequestModel::where('status', 'pending')->count();

        $activeShiftsCount = UserShiftModel::where('is_active', 1)->count();

        $totalEmployees = User::where('is_active', 1)
            ->where('is_admin', 0)
            ->count();

        $todayAttendances = AttendanceModel::whereDate('date', Carbon::today())
            ->count();

        $pendingLeaves = LeaveRequestModel::where('status', 'pending')
            ->count();

        $activeOfficeLocations = OfficeLocationModel::where('is_active', 1)
            ->pluck('name');


        $attendanceByDepartment = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->join('users_profile', 'users.id', '=', 'users_profile.user_id')
            ->whereMonth('attendances.date', Carbon::now()->month)
            ->select('users_profile.department', DB::raw('count(*) as total'))
            ->groupBy('users_profile.department')
            ->get();

        $attendanceByMonth = AttendanceModel::selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereYear('date', Carbon::now()->year)
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('month', 'asc')
            ->get();

        $attendanceData = [];
        for ($month = 1; $month <= 12; $month++) {
            $attendance = $attendanceByMonth->where('month', $month)->first();
            $attendanceData[] = $attendance ? $attendance->total : 0;
        }
        $totalAttendanceThisYear = $attendanceByMonth->sum('total');

        $lastMonthAttendance = $attendanceByMonth->where('month', Carbon::now()->subMonth()->month)->sum('total');

        $attendanceMonthsCount = $attendanceByMonth->count();



        return view("dashboard.index", data: compact(
            'user',
            'pendingLeaveRequests',
            'activeShiftsCount',
            'totalEmployees',
            'todayAttendances',
            'pendingLeaves',
            'activeOfficeLocations',
            'attendanceByDepartment',
            'attendanceData',
            'totalAttendanceThisYear',
            'lastMonthAttendance',
            'attendanceMonthsCount'
        ));
    }
}
