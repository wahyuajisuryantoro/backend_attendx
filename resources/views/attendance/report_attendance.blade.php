@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:calendar-mark-linear" class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Total Attendance Records</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $totalAttendances }}</h4>
                                <a href="javascript:void(0)" class="btn btn-white fs-2 fw-semibold text-nowrap">All
                                    Records</a>
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
                                <h6 class="fw-normal fs-3 mb-1">On Time Attendance</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $onTimeCount }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    {{ number_format($totalAttendances > 0 ? ($onTimeCount / $totalAttendances) * 100 : 0, 1) }}%
                                    On Time
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card danger-gradient">
                            <div class="card-body text-center px-9 pb-4">
                                <div
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-danger flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:clock-circle-linear" class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Late Attendance</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $lateCount }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    {{ number_format($totalAttendances > 0 ? ($lateCount / $totalAttendances) * 100 : 0, 1) }}%
                                    Late
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card secondary-gradient">
                            <div class="card-body text-center px-9 pb-4">
                                <div
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:buildings-3-linear" class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Top Department</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $topCount }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">{{ $topDepartment }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title">Attendance Report</h4>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" id="exportExcelDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-export mr-2"></i> Export Excel
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportExcelDropdown">
                            <li><a class="dropdown-item" href="{{ route('attendance.report.export.excel') }}">All Data</a>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('attendance.report.export.excel') }}?period=current_month">Current
                                    Month</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('attendance.report.export.excel') }}?period=previous_month">Previous
                                    Month</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('attendance.report.export.excel') }}?period=current_week">Current
                                    Week</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#" id="export-excel-filtered">Use Current Filters</a>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-danger dropdown-toggle" type="button" id="exportPdfDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-text mr-2"></i> Export PDF
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportPdfDropdown">
                            <li><a class="dropdown-item" href="{{ route('attendance.report.export.pdf') }}">All Data</a>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('attendance.report.export.pdf') }}?period=current_month">Current
                                    Month</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('attendance.report.export.pdf') }}?period=previous_month">Previous
                                    Month</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('attendance.report.export.pdf') }}?period=current_week">Current Week</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#" id="export-pdf-filtered">Use Current Filters</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Start Date</span>
                        <input type="text" id="start_date" class="form-control flatpickr-input"
                            placeholder="YYYY-MM-DD">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">End Date</span>
                        <input type="text" id="end_date" class="form-control flatpickr-input"
                            placeholder="YYYY-MM-DD">
                    </div>
                </div>
                <div class="col-md-4">
                    <button id="filter-btn" class="btn btn-primary">Filter</button>
                    <button id="reset-btn" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="attendanceTable" class="table table-striped text-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            var attendanceTable = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('attendance.report.getDataAttendance') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.search_value = d.search.value;
                    }
                },
                columns: [{
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'clock_in',
                        name: 'clock_in'
                    },
                    {
                        data: 'clock_out',
                        name: 'clock_out'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            flatpickr(".flatpickr-input", {
                dateFormat: "Y-m-d"
            });

            $('#filter-btn').click(function() {
                attendanceTable.draw();
            });

            $('#reset-btn').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                attendanceTable.draw();
            });

            $('#export-excel-filtered').click(function(e) {
                e.preventDefault();
                let url = "{{ route('attendance.report.export.excel') }}";
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                url += '?start_date=' + startDate + '&end_date=' + endDate;
                if ($('#user_id').length && $('#user_id').val()) {
                    url += '&user_id=' + $('#user_id').val();
                }

                window.location.href = url;
            });

            $('#export-pdf-filtered').click(function(e) {
                e.preventDefault();
                let url = "{{ route('attendance.report.export.pdf') }}";
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                url += '?start_date=' + startDate + '&end_date=' + endDate;
                if ($('#user_id').length && $('#user_id').val()) {
                    url += '&user_id=' + $('#user_id').val();
                }

                window.location.href = url;
            });
            
            $(document).on('click', '.delete-attendance', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/attendance/' + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    attendanceTable.draw();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong!',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
