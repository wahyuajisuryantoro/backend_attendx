<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\LeaveRequestModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->leaveRequests();

        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected' , 'canceled'])) {
            $query->where('status', $request->status);
        }

        $query->latest();

        $leaveRequests = $query->get();

        return response()->json([
            'success' => true,
            'data' => $leaveRequests,
            'message' => 'Daftar permintaan cuti berhasil dimuat'
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $leaveRequest = $user->leaveRequests()
            ->with(['approver', 'approver.profile'])
            ->find($id);

        if (!$leaveRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan cuti tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $leaveRequest,
            'message' => 'Detail permintaan cuti berhasil dimuat'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:annual,sick,emergency,other',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        $user = auth()->user();
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $conflictingLeaves = $user->leaveRequests()
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->first();

        if ($conflictingLeaves) {
            return response()->json([
                'success' => false,
                'message' => 'There are other leave requests in the same date range.'
            ], 422);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $fileName = 'leave_' . $user->id . '_' . time() . '.' . $request->file('attachment')->extension();
            $attachmentPath = $request->file('attachment')->storeAs('leave_attachments', $fileName, 'public');
        }

        $leaveRequest = $user->leaveRequests()->create([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'attachment' => $attachmentPath
        ]);

        return response()->json([
            'success' => true,
            'data' => $leaveRequest,
            'message' => 'Permintaan cuti berhasil dibuat'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $leaveRequest = $user->leaveRequests()
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$leaveRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan cuti tidak ditemukan atau tidak dapat diedit'
            ], 404);
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:annual,sick,emergency,other',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $conflictingLeaves = $user->leaveRequests()
            ->where('id', '!=', $id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->first();

        if ($conflictingLeaves) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat permintaan cuti lain di rentang tanggal yang sama'
            ], 422);
        }

        $leaveRequest->start_date = $validated['start_date'];
        $leaveRequest->end_date = $validated['end_date'];
        $leaveRequest->type = $validated['type'];
        $leaveRequest->reason = $validated['reason'];

        if ($request->hasFile('attachment')) {
            if ($leaveRequest->attachment) {
                Storage::disk('public')->delete($leaveRequest->attachment);
            }

            $fileName = 'leave_' . $user->id . '_' . time() . '.' . $request->file('attachment')->extension();
            $leaveRequest->attachment = $request->file('attachment')->storeAs('leave_attachments', $fileName, 'public');
        }

        $leaveRequest->save();

        return response()->json([
            'success' => true,
            'data' => $leaveRequest,
            'message' => 'Permintaan cuti berhasil diperbarui'
        ]);
    }

    public function cancel($id)
    {
        $user = auth()->user();

        $leaveRequest = $user->leaveRequests()
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$leaveRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Leave request not found or cannot be cancelled'
            ], 404);
        }

        $leaveRequest->update([
            'status' => 'canceled',
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $leaveRequest,
            'message' => 'Leave request has been cancelled successfully'
        ]);
    }

    public function summary()
    {
        $user = auth()->user();
        $allLeaves = $user->leaveRequests()->get();
        $approvedLeaves = $allLeaves->where('status', 'approved');

        $totalAllocation = 22;

        $usedLeave = 0;
        foreach ($approvedLeaves as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            $usedLeave += $endDate->diffInDays($startDate) + 1;
        }

        $annualLeave = $allLeaves->where('type', 'annual')->count();
        $sickLeave = $allLeaves->where('type', 'sick')->count();
        $emergencyLeave = $allLeaves->where('type', 'emergency')->count();
        $otherLeave = $allLeaves->where('type', 'other')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_allocation' => $totalAllocation,
                'used_leave' => $usedLeave,
                'remaining_leave' => $totalAllocation - $usedLeave,
                'annual_leave' => $annualLeave,
                'sick_leave' => $sickLeave,
                'emergency_leave' => $emergencyLeave,
                'other_leave' => $otherLeave,
                'pending_count' => $allLeaves->where('status', 'pending')->count(),
                'approved_count' => $approvedLeaves->count(),
                'rejected_count' => $allLeaves->where('status', 'rejected')->count(),
            ],
            'message' => 'Ringkasan cuti berhasil dimuat'
        ]);
    }
}

