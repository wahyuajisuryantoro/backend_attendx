<?php

namespace App\Http\Controllers\Api;

use App\Models\UserShiftModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class UserShiftController extends Controller
{
    public function getUserAssignments(Request $request)
    {
        try {
            $user = auth()->user();
            
            $assignments = UserShiftModel::with('shift')
                ->where('user_id', $user->id)
                ->where('is_active', 1)
                ->where(function($query) {
                    $today = Carbon::today();
                    $query->where('start_date', '<=', $today)
                          ->where(function($q) use ($today) {
                              $q->whereNull('end_date')
                                ->orWhere('end_date', '>=', $today);
                          });
                })
                ->orderBy('start_date', 'asc')
                ->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Shift assignments retrieved successfully',
                'data' => $assignments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve shift assignments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getCurrentShift(Request $request)
    {
        try {
            $user = auth()->user();
            $today = Carbon::today()->format('Y-m-d');
            
            $currentShift = UserShiftModel::with('shift')
                ->where('user_id', $user->id)
                ->where('is_active', 1)
                ->where('start_date', '<=', $today)
                ->where(function($query) use ($today) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $today);
                })
                ->first();
            
            if (!$currentShift) {
                return response()->json([
                    'status' => false,
                    'message' => 'No active shift found for today',
                    'data' => null
                ]);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Current shift retrieved successfully',
                'data' => $currentShift
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve current shift',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getUpcomingShifts(Request $request)
    {
        try {
            $user = auth()->user();
            $today = Carbon::today();
            
            $upcomingShifts = UserShiftModel::with('shift')
                ->where('user_id', $user->id)
                ->where('is_active', 1)
                ->where('start_date', '>', $today)
                ->orderBy('start_date', 'asc')
                ->take(7) 
                ->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Upcoming shifts retrieved successfully',
                'data' => $upcomingShifts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve upcoming shifts',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
