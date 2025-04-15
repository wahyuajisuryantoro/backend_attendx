<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AttendanceModel;
use App\Models\OfficeLocationModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'clock_in_method' => 'required|in:fingerprint,face_recognition',
            'clock_in_photo' => 'required_if:clock_in_method,face_recognition|image|max:2048',
            'clock_in_location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance_from_office' => 'required|numeric',
            'office_id' => 'required|exists:office_locations,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $today = Carbon::now()->toDateString();
        $existingAttendance = AttendanceModel::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->clock_in) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah melakukan clock in hari ini'
            ], 400);
        }

        $office = OfficeLocationModel::findOrFail($request->office_id);
        $distance = $request->distance_from_office;

        if ($distance > $office->radius) {
            return response()->json([
                'status' => false,
                'message' => 'Anda berada di luar radius kantor yang diizinkan',
                'data' => [
                    'office' => [
                        'name' => $office->name,
                        'radius' => $office->radius,
                        'your_distance' => $distance
                    ]
                ]
            ], 400);
        }


        $clockInPhotoPath = null;
        if ($request->hasFile('clock_in_photo')) {
            $photo = $request->file('clock_in_photo');
            $filename = $user->id . '_' . time() . '_in.' . $photo->getClientOriginalExtension();
            $clockInPhotoPath = $photo->storeAs('attendance_photos', $filename, 'public');
        }

        $locationInfo = [
            'address' => $request->clock_in_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'office_name' => $office->name,
            'distance' => $distance
        ];

        $locationJson = json_encode($locationInfo);


        if ($existingAttendance) {
            $existingAttendance->update([
                'clock_in' => $request->clock_in,
                'clock_in_method' => $request->clock_in_method,
                'clock_in_photo' => $clockInPhotoPath,
                'clock_in_location' => $locationJson,
                'notes' => $request->notes ?? $existingAttendance->notes,
            ]);
            $attendance = $existingAttendance;
        } else {
            $attendance = AttendanceModel::create([
                'user_id' => $user->id,
                'date' => $today,
                'clock_in' => $request->clock_in,
                'clock_in_method' => $request->clock_in_method,
                'clock_in_photo' => $clockInPhotoPath,
                'clock_in_location' => $locationJson,
                'notes' => $request->notes,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Clock in berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->profile->name ?? $user->username,
                    'employee_id' => $user->profile->employee_id ?? null,
                ],
                'attendance' => [
                    'date' => $attendance->date,
                    'clock_in' => $attendance->clock_in,
                    'clock_in_method' => $attendance->clock_in_method,
                    'office' => [
                        'name' => $office->name,
                        'distance' => $distance
                    ]
                ]
            ]
        ], 200);
    }

    public function clockOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clock_out_method' => 'required|in:fingerprint,face_recognition',
            'clock_out_photo' => 'required_if:clock_out_method,face_recognition|image|max:2048',
            'clock_out_location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance_from_office' => 'required|numeric',
            'office_id' => 'required|exists:office_locations,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $today = Carbon::now()->toDateString();

        $attendance = AttendanceModel::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'status' => false,
                'message' => 'Anda belum melakukan clock in hari ini'
            ], 400);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah melakukan clock out hari ini'
            ], 400);
        }

        $office = OfficeLocationModel::findOrFail($request->office_id);
        $distance = $request->distance_from_office;


        $isOutsideRadius = false;
        if ($distance > $office->radius) {
            $isOutsideRadius = true;
        }

        $clockOutPhotoPath = null;
        if ($request->hasFile('clock_out_photo')) {
            $photo = $request->file('clock_out_photo');
            $filename = $user->id . '_' . time() . '_out.' . $photo->getClientOriginalExtension();
            $clockOutPhotoPath = $photo->storeAs('attendance_photos', $filename, 'public');
        }

        $locationInfo = [
            'address' => $request->clock_out_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'office_name' => $office->name,
            'distance' => $distance,
            'outside_radius' => $isOutsideRadius
        ];

        $locationJson = json_encode($locationInfo);


        $attendance->update([
            'clock_out' => $request->clock_out,
            'clock_out_method' => $request->clock_out_method,
            'clock_out_photo' => $clockOutPhotoPath,
            'clock_out_location' => $locationJson,
            'notes' => $request->notes ?? $attendance->notes,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Clock out berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->profile->name ?? $user->username,
                    'employee_id' => $user->profile->employee_id ?? null,
                ],
                'attendance' => [
                    'date' => $attendance->date,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => $attendance->clock_out,
                    'clock_out_method' => $attendance->clock_out_method,
                    'office' => [
                        'name' => $office->name,
                        'distance' => $distance,
                        'outside_radius' => $isOutsideRadius
                    ]
                ]
            ]
        ], 200);
    }
    public function getOfficeLocations()
    {
        $offices = OfficeLocationModel::where('is_active', true)
            ->get(['id', 'name', 'address', 'coordinates', 'radius']);

        $formattedOffices = $offices->map(function ($office) {
            $coordinates = explode(',', $office->coordinates);
            return [
                'id' => $office->id,
                'name' => $office->name,
                'address' => $office->address,
                'latitude' => trim($coordinates[0]),
                'longitude' => trim($coordinates[1]),
                'radius' => $office->radius
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedOffices
        ], 200);
    }
    public function getTodayAttendance()
    {
        $user = auth()->user();
        $today = Carbon::now()->toDateString();

        $attendance = AttendanceModel::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'status' => true,
                'data' => [
                    'has_attendance' => false,
                    'message' => 'No attendance today'
                ]
            ], 200);
        }

        $clockInLocation = json_decode($attendance->clock_in_location, true);
        $clockOutLocation = json_decode($attendance->clock_out_location, true);

        return response()->json([
            'status' => true,
            'data' => [
                'has_attendance' => true,
                'attendance' => [
                    'date' => $attendance->date,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => $attendance->clock_out,
                    'clock_in_method' => $attendance->clock_in_method,
                    'clock_out_method' => $attendance->clock_out_method,
                    'clock_in_location' => $clockInLocation,
                    'clock_out_location' => $clockOutLocation,
                    'notes' => $attendance->notes
                ]
            ]
        ], 200);
    }

    public function historyAllAttendance(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sort_by', 'date');
        $sortOrder = $request->input('sort_order', 'desc');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');


        $query = AttendanceModel::where('user_id', $user->id);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else if ($startDate) {
            $query->where('date', '>=', $startDate);
        } else if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $query->orderBy($sortBy, $sortOrder);
        $attendances = $query->paginate($perPage);
        $attendances->getCollection()->transform(function ($attendance) {
            $attendance->date_formatted = date('l, d F Y', strtotime($attendance->date));
            if ($attendance->clock_in && $attendance->clock_out) {
                $dateOnly = date('Y-m-d', strtotime($attendance->date));
                $clockIn = \Carbon\Carbon::parse($dateOnly . ' ' . $attendance->clock_in);
                $clockOut = \Carbon\Carbon::parse($dateOnly . ' ' . $attendance->clock_out);
                $clockInTimestamp = $clockIn->timestamp;
                $clockOutTimestamp = $clockOut->timestamp;

                if ($clockOutTimestamp < $clockInTimestamp) {
                    $clockOut->addDay();
                    $clockOutTimestamp = $clockOut->timestamp;
                }
                $durationMinutes = ($clockOutTimestamp - $clockInTimestamp) / 60;
                $hours = floor($durationMinutes / 60);
                $minutes = $durationMinutes % 60;

                $attendance->duration = [
                    'minutes' => $durationMinutes,
                    'hours' => $hours,
                    'minutes_remainder' => $minutes,
                    'formatted' => sprintf('%02d:%02d', $hours, $minutes)
                ];
            } else {
                $attendance->duration = null;
            }

            if ($attendance->clock_in_location) {
                $attendance->clock_in_location_data = json_decode($attendance->clock_in_location, true);
            }

            if ($attendance->clock_out_location) {
                $attendance->clock_out_location_data = json_decode($attendance->clock_out_location, true);
            }

            return $attendance;
        });


        $totalRecords = $attendances->total();
        $completedRecords = $query->whereNotNull('clock_in')->whereNotNull('clock_out')->count();
        $incompleteRecords = $totalRecords - $completedRecords;

        $totalMinutes = 0;
        foreach ($attendances as $attendance) {
            if (isset($attendance->duration) && isset($attendance->duration['minutes'])) {
                $totalMinutes += $attendance->duration['minutes'];
            }
        }

        $totalHours = floor($totalMinutes / 60);
        $remainderMinutes = $totalMinutes % 60;

        return response()->json([
            'status' => true,
            'data' => [
                'attendances' => $attendances,
                'summary' => [
                    'total_records' => $totalRecords,
                    'completed_records' => $completedRecords,
                    'incomplete_records' => $incompleteRecords,
                    'total_working_time' => [
                        'minutes' => $totalMinutes,
                        'hours' => $totalHours,
                        'minutes_remainder' => $remainderMinutes,
                        'formatted' => sprintf('%02d:%02d', $totalHours, $remainderMinutes)
                    ]
                ]
            ]
        ], 200);
    }
}
