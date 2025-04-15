<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\UserShiftController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveRequestController;

Route::post('/login', [authController::class , 'login']);
Route::post('/register', [authController::class , 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('/password/update', [AccountController::class, 'update']);
    
    // Attendance
    Route::post('attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('attendance/clock-out', [AttendanceController::class, 'clockout']);
    Route::get('attendance/today', [AttendanceController::class, 'getTodayAttendance']);
    Route::get('attendance/history', [AttendanceController::class, 'historyAllAttendance']);

    // Leave Request
    Route::get('leave/index', [LeaveRequestController::class, 'index']);
    Route::post('leave/store', [LeaveRequestController::class, 'store']);
    Route::get('leave/detail/{id}', [LeaveRequestController::class, 'show']);
    Route::put('leave/edit/{id}', [LeaveRequestController::class, 'update']);
    Route::get('leave/summary', [LeaveRequestController::class, 'summary']);
    Route::put('leave/cancel/{id}', [LeaveRequestController::class, 'cancel']);

    Route::get('/user/current-shift', [UserShiftController::class, 'getCurrentShift']);
    Route::get('/user/upcoming-shifts', [UserShiftController::class, 'getUpcomingShifts']);
    Route::get('/user/shifts', [UserShiftController::class, 'getUserAssignments']);
    
    Route::get('office-locations', [AttendanceController::class, 'getOfficeLocations']);

});
