<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $title = 'Leave Request';
        $pendingCount = LeaveRequestModel::where('status', 'pending')->count();
        $approvedCount = LeaveRequestModel::where('status', 'approved')->count();
        $rejectedCount = LeaveRequestModel::where('status', 'rejected')->count();
        $canceledCount = LeaveRequestModel::where('status', 'canceled')->count();

        return view("leave_request.leave_request_index", [
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'canceledCount' => $canceledCount,
        ], compact('title'));
    }
    public function getDataLeaveRequest()
    {
        $query = LeaveRequestModel::with(['user', 'user.profile'])
            ->whereHas('user', function ($q) {
                $q->where('is_admin', 0);
            })
            ->select('leave_requests.*');

        return DataTables::of($query)
            ->addColumn('employee_id', function ($leaveRequest) {
                return $leaveRequest->user->profile->employee_id ?? '-';
            })
            ->addColumn('name', function ($leaveRequest) {
                return $leaveRequest->user->profile->name ?? '-';
            })
            ->addColumn('date_range', function ($leaveRequest) {
                return $leaveRequest->start_date->format('d M Y') . ' - ' .
                    $leaveRequest->end_date->format('d M Y');
            })
            ->addColumn('status_badge', function ($leaveRequest) {
                $badgeClass = match ($leaveRequest->status) {
                    'pending' => 'badge bg-warning text-white',
                    'approved' => 'badge bg-success text-white',
                    'rejected' => 'badge bg-danger text-white',
                    'canceled' => 'badge bg-secondary text-white',
                    default => 'badge bg-light text-dark'
                };
                return "<span class='{$badgeClass}'>" . ucfirst($leaveRequest->status) . "</span>";
            })
            ->addColumn('actions', function ($leaveRequest) {
                $actions = '<div class="btn-group" role="group">';


                $actions .= '<button type="button" class="btn btn-info btn-sm show-leave" data-id="' . $leaveRequest->id . '">
                <i class="ti ti-eye"></i>
            </button>';


                switch ($leaveRequest->status) {
                    case 'pending':
                        $actions .= '
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item approve-leave" href="#" data-id="' . $leaveRequest->id . '">Approve</a></li>
                                <li><a class="dropdown-item reject-leave" href="#" data-id="' . $leaveRequest->id . '">Reject</a></li>
                            </ul>
                        </div>';
                        break;

                    case 'approved':
                        $actions .= '
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item reject-leave" href="#" data-id="' . $leaveRequest->id . '">Reject</a></li>
                            </ul>
                        </div>';
                        break;

                    case 'rejected':
                        $actions .= '
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item approve-leave" href="#" data-id="' . $leaveRequest->id . '">Approve</a></li>
                            </ul>
                        </div>';
                        break;

                    case 'canceled':

                        break;
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function updateStatusLeaveRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:leave_requests,id',
            'status' => 'required|in:approved,rejected,canceled',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $leaveRequest = LeaveRequestModel::findOrFail($request->id);


            if (!$this->isStatusChangeAllowed($leaveRequest->status, $request->status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status transition.'
                ], 400);
            }


            $leaveRequest->status = $request->status;


            if ($request->status === 'rejected') {
                $leaveRequest->rejection_reason = $request->reason ?? 'No reason provided';
            }

            $leaveRequest->approved_by = Auth::id();
            $leaveRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Leave request status updated successfully.',
                'status' => $leaveRequest->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isStatusChangeAllowed($currentStatus, $newStatus)
    {
        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['rejected'],
            'rejected' => ['approved'],
            'canceled' => []
        ];

        return isset($allowedTransitions[$currentStatus]) &&
            in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

    public function showLeaveRequestDetail($id)
    {
        $title = 'Leave Request Detail';
        try {
            $leaveRequest = LeaveRequestModel::with([
                'user' => function($query) {
                    $query->with('profile');
                }
            ])->findOrFail($id);
    
            return view('leave_request.leave_request_show', [
                'leaveRequest' => $leaveRequest
            ], compact('title'));
        } catch (\Exception $e) {
            \Log::error('Leave Request Detail Error: ' . $e->getMessage());
            return redirect()->route('leave-request.index')
                ->with('error', 'Failed to fetch leave request details.');
        }
    }
}