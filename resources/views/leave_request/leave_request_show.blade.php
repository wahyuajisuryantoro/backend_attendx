@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Leave Request Details</h5>
            <a href="{{ route('leave-request.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Back to Leave Requests
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Employee Information</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Name</th>
                                <td>{{ $leaveRequest->user->profile->name }}</td>
                            </tr>
                            <tr>
                                <th>Employee ID</th>
                                <td>{{ $leaveRequest->user->profile->employee_id }}</td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td>{{ $leaveRequest->user->profile->department ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Position</th>
                                <td>{{ $leaveRequest->user->profile->position ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Leave Request Details</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Leave Type</th>
                                <td>
                                    <span class="badge bg-primary text-white text-uppercase">
                                        {{ $leaveRequest->type }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Date Range</th>
                                <td>
                                    {{ $leaveRequest->start_date->format('d M Y') }} - 
                                    {{ $leaveRequest->end_date->format('d M Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @php
                                        $statusBadgeClass = match($leaveRequest->status) {
                                            'pending' => 'bg-warning',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'canceled' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusBadgeClass }} text-uppercase">
                                        {{ $leaveRequest->status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Reason</th>
                                <td>{{ $leaveRequest->reason }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            @if($leaveRequest->attachment)
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="text-muted mb-3">Attachment</h6>
                    <div>
                        <a href="{{ Storage::url($leaveRequest->attachment) }}" 
                           target="_blank" 
                           class="btn btn-outline-primary">
                            <i class="ti ti-file"></i> View Attachment
                        </a>
                    </div>
                </div>
            </div>
            @endif

            @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="text-muted mb-3">Rejection Reason</h6>
                    <div class="alert alert-danger">
                        {{ $leaveRequest->rejection_reason }}
                    </div>
                </div>
            </div>
            @endif

           
        </div>
    </div>
</div>
@endsection