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
                                    class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
                                    <iconify-icon icon="solar:users-group-rounded-linear"
                                        class="fs-7 text-white"></iconify-icon>
                                </div>
                                <h6 class="fw-normal fs-3 mb-1">Total Employees</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $totalEmployees }}</h4>
                                <a href="javascript:void(0)" class="btn btn-white fs-2 fw-semibold text-nowrap">All
                                    Staff</a>
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
                                <h6 class="fw-normal fs-3 mb-1">Active Employees</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $activeEmployees }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    {{ number_format($totalEmployees > 0 ? ($activeEmployees / $totalEmployees) * 100 : 0, 1) }}%
                                    Active
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
                                <h6 class="fw-normal fs-3 mb-1">Inactive Employees</h6>
                                <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                    {{ $inactiveEmployees }}</h4>
                                <div class="btn btn-white fs-2 fw-semibold text-nowrap">
                                    {{ number_format($totalEmployees > 0 ? ($inactiveEmployees / $totalEmployees) * 100 : 0, 1) }}%
                                    Inactive
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
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">Employee Data</h4>
                <a href="{{ route('employee.create') }}" class="btn btn-primary">
                    <i class="ti ti-user-plus ml-2"></i> Add Employee
                </a>
            </div>
            <div class="table-responsive">
                <table id="employeeTable" class="table table-striped text-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Profile Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Start Date</th>
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
            $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                scrollY: false,
                ajax: '{{ route('employee.getData') }}',
                columns: [{
                        data: 'employee_id',
                        name: 'profile.employee_id'
                    },
                    {
                        data: 'profile_photo',
                        name: 'profile_photo',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'profile.name',
                        name: 'profile.name'
                    },
                    {
                        data: 'profile.position',
                        name: 'profile.position'
                    },
                    {
                        data: 'profile.department',
                        name: 'profile.department'
                    },
                    {
                        data: 'join_date',
                        name: 'profile.join_date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                const deleteForm = $(this).data('form');

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
                        document.getElementById(deleteForm).submit();
                    }
                });
            });
        });
    </script>
@endsection
