@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4 pb-0" data-simplebar="">
                <div class="row flex-nowrap">
                    <div class="col">
                        <div class="card primary-gradient">
                            <div class="card-body text-center px-9 pb-4">
                                <div
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-warning flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:hourglass-line-duotone"
                                        class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Pending Requests</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $pendingCount }}</h4>
                                <a href="javascript:void(0)" class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    Pending Leave
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card success-gradient">
                            <div class="card-body text-center px-9 pb-4">
                                <div
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-success flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:user-check-rounded-linear"
                                        class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Approved Requests</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $approvedCount }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    {{ number_format($pendingCount > 0 ? ($approvedCount / ($pendingCount + $approvedCount)) * 100 : 0, 1) }}%
                                    Approved
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card danger-gradient">
                            <div class="card-body text-center px-9 pb-4">
                                <div
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-danger flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:user-block-rounded-linear"
                                        class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Rejected Requests</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $rejectedCount }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    {{ number_format($pendingCount > 0 ? ($rejectedCount / ($pendingCount + $rejectedCount)) * 100 : 0, 1) }}%
                                    Rejected
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card secondary-gradient">
                            <div class="card-body text-center px-9 pb-4">
                                <div
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:close-circle-line-duotone"
                                        class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Canceled Requests</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $canceledCount }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    Canceled Leave
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">Leave Request Data</h4>
            </div>
            <div class="table-responsive">
                <table id="employeeTable" class="table table-striped text-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Status Request</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const table = $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('leave-request.data') }}',
                columns: [{
                        data: 'employee_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'date_range'
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    paginate: {
                        previous: '<i class="ti ti-chevron-left"></i>',
                        next: '<i class="ti ti-chevron-right"></i>'
                    }
                }
            });
            
            $('#employeeTable').on('click', '.approve-leave', function(e) {
                e.preventDefault();
                const leaveId = $(this).data('id');

                Swal.fire({
                    title: 'Approve Leave Request',
                    text: 'Are you sure you want to approve this leave request?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateLeaveRequestStatus(leaveId, 'approved');
                    }
                });
            });


            $('#employeeTable').on('click', '.reject-leave', function(e) {
                e.preventDefault();
                const leaveId = $(this).data('id');

                Swal.fire({
                    title: 'Reject Leave Request',
                    text: 'Please provide a reason for rejection',
                    input: 'textarea',
                    inputPlaceholder: 'Enter rejection reason...',
                    showCancelButton: true,
                    confirmButtonText: 'Reject',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to write a reason!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        updateLeaveRequestStatus(leaveId, 'rejected', result.value);
                    }
                });
            });

            function updateLeaveRequestStatus(leaveId, status, reason = null) {
                $.ajax({
                    url: '{{ route('leave-request.update-status') }}',
                    method: 'POST',
                    data: {
                        id: leaveId,
                        status: status,
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success'
                        });

                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }
            $('#employeeTable').on('click', '.show-leave', function() {
                const leaveId = $(this).data('id');
                
                window.location.href = `/leave-request/detail/${leaveId}`;
            });


            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }


        });
    </script>
@endsection
