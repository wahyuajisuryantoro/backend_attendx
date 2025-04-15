<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\OfficeController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserShiftController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\Web\LoginAdminController;
use App\Http\Controllers\Web\WorkShiftsController;
use App\Http\Controllers\Web\AccountSettingsController;
use App\Http\Controllers\Web\LeaveRequestController;


Route::get('/', [LoginAdminController::class, 'index'])->name('login');

Route::post('/login', [LoginAdminController::class, 'login'])->name('login.post');

Route::get("/verif/email/{token}", [AuthController::class, 'verifEmail']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginAdminController::class, 'logout'])->name('logout');
    Route::middleware('is_admin')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/employee-list', [EmployeeController::class, 'index'])->name('employee.list');
        Route::get('/employee/create', [EmployeeController::class, 'create'])->name('employee.create');
        Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
        Route::get('/employee/{id}', [EmployeeController::class, 'show'])->name('employee.show');
        Route::get('/employee/{id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::put('/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
        Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
        Route::get('employee-data', [EmployeeController::class, 'getData'])->name('employee.getData');

        Route::get('/attendance-report', [AttendanceController::class, 'index'])->name('attendance.report.index');
        Route::get('/attendance-report/data', [AttendanceController::class, 'getAttendanceData'])->name('attendance.report.getDataAttendance');
        Route::get('/attendance-report/export/excel', [AttendanceController::class, 'exportExcel'])->name('attendance.report.export.excel');
        Route::get('/attendance-report/export/pdf', [AttendanceController::class, 'exportPdf'])->name('attendance.report.export.pdf');
        Route::get('/attendance-report/{id}', [AttendanceController::class, 'show'])->name('attendance.report.show');
        Route::get('/attendance-report/employee/{userId}', [AttendanceController::class, 'employeeReport'])->name('attendance.report.employee');
        Route::get('/attendance-report/employee/{userId}/export', [AttendanceController::class, 'employeeExport'])->name('attendance.report.employee.export');
        Route::get('/attendance-report/location/{id}', [AttendanceController::class, 'getLocation'])->name('attendance.report.getLocation');

        Route::get('/leave-request', [LeaveRequestController::class, 'index'])->name('leave-request.index');
        Route::get('/leave-request/data', [LeaveRequestController::class, 'getDataLeaveRequest'])->name('leave-request.data');
        Route::post('/leave-request/update-status', [LeaveRequestController::class, 'updateStatusLeaveRequest'])->name('leave-request.update-status');
        Route::get('/leave-request/detail/{id}', [LeaveRequestController::class, 'showLeaveRequestDetail'])
            ->name('leave-request.detail');

        Route::get('/office', [OfficeController::class, 'index'])->name('office.index');
        Route::put('/office/update', [OfficeController::class, 'update'])->name('office.update');

        Route::get('/work-shifts', [WorkShiftsController::class, 'index'])->name('work-shifts.index');
        Route::post('/work-shifts', [WorkShiftsController::class, 'store'])->name('work-shifts.store');
        Route::get('/work-shifts/{id}', [WorkShiftsController::class, 'get'])->name('work-shifts.get');
        Route::put('/work-shifts/{id}', [WorkShiftsController::class, 'update'])->name('work-shifts.update');
        Route::delete('/work-shifts/{id}', [WorkShiftsController::class, 'destroy'])->name('work-shifts.destroy');

        Route::get('/shift-assignments', [UserShiftController::class, 'index'])->name('shift-assignments.index');
        Route::post('/shift-assignments', [UserShiftController::class, 'store'])->name('shift-assignments.store');
        Route::get('/shift-assignments/{id}', [UserShiftController::class, 'show'])->name('shift-assignments.show');
        Route::put('/shift-assignments/{id}', [UserShiftController::class, 'update'])->name('shift-assignments.update');
        Route::delete('/shift-assignments/{id}', [UserShiftController::class, 'destroy'])->name('shift-assignments.destroy');

        Route::get('/account-settings', [AccountSettingsController::class, 'index'])->name('account-settings.index');
        Route::post('/account/update-password', [AccountSettingsController::class, 'updatePassword'])->name('account.update-password');
        Route::post('/account/update-profile', [AccountSettingsController::class, 'updateProfile'])->name('account.update-profile');
        Route::post('/account/verify-key', [AccountSettingsController::class, 'verifyAdminKey'])->name('account.verify-key');
    });
});