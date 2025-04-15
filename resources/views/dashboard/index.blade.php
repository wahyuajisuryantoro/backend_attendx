@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-6">

            <div class="card text-white bg-primary-gt overflow-hidden">
                <div class="card-body position-relative z-1">
                    <h4 class="text-white fw-normal mt-5 pt-7 mb-1">
                        Hey, <span class="fw-semibold">{{ $user->username }}</span>
                    </h4>
                    <h6 class="opacity-75 fw-normal text-white mb-0">This is the admin view for managing attendance
                        data.</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-stretch">
            <a href="javascript:void(0)" class="card text-bg-warning text-white w-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-octagon display-6"></i>
                        <div class="ms-auto">
                            <i class="ti ti-arrow-right fs-8"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h4 class="card-title mb-1 text-white">
                            Leave Request Action
                        </h4>
                        <p class="card-text fw-normal text-white opacity-75">
                            There are {{ $pendingLeaveRequests }} leave requests that need to be handled immediately
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 d-flex align-items-stretch">
            <a href="javascript:void(0)" class="card text-bg-info text-white w-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-octagon display-6"></i>
                        <div class="ms-auto">
                            <i class="ti ti-arrow-right fs-8"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h4 class="card-title mb-1 text-white">
                            Active Shift
                        </h4>
                        <p class="card-text fw-normal text-white opacity-75">
                            There are {{ $activeShiftsCount }} shifts that are still active
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4 pb-0" data-simplebar="">
                    <div class="row flex-nowrap">
                        <div class="col">
                            <div class="card primary-gradient">
                                <div class="card-body text-center px-9 pb-4">
                                    <div
                                        class="d-flex align-items-center justify-content-center round-48 rounded text-bg-primary flex-shrink-0 mb-3 mx-auto">
                                        <iconify-icon icon="mdi:account-group" class="fs-7 text-white"></iconify-icon>
                                    </div>
                                    <h6 class="fw-normal fs-3 mb-1">Total Employees</h6>
                                    <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                        {{ $totalEmployees }}</h4>
                                    <a href="{{ route('employee.list') }}"
                                        class="btn btn-white fs-2 fw-semibold text-nowrap">View
                                        Details</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card success-gradient">
                                <div class="card-body text-center px-9 pb-4">
                                    <div
                                        class="d-flex align-items-center justify-content-center round-48 rounded text-bg-success flex-shrink-0 mb-3 mx-auto">
                                        <iconify-icon icon="mdi:clock-check-outline" class="fs-7 text-white"></iconify-icon>
                                    </div>
                                    <h6 class="fw-normal fs-3 mb-1">Today's Presence</h6>
                                    <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                        {{ $todayAttendances }}</h4>
                                    <a href="{{ route('attendance.report.index') }}"
                                        class="btn btn-white fs-2 fw-semibold text-nowrap">View
                                        Details</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card warning-gradient">
                                <div class="card-body text-center px-9 pb-4">
                                    <div
                                        class="d-flex align-items-center justify-content-center round-48 rounded text-bg-warning flex-shrink-0 mb-3 mx-auto">
                                        <iconify-icon icon="mdi:calendar-clock" class="fs-7 text-white"></iconify-icon>
                                    </div>
                                    <h6 class="fw-normal fs-3 mb-1">Leave Request</h6>
                                    <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                        {{ $pendingLeaves }}</h4>
                                    <a href="{{ route('leave-request.index') }}"
                                        class="btn btn-white fs-2 fw-semibold text-nowrap">View
                                        Details</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card secondary-gradient">
                                <div class="card-body text-center px-9 pb-4">
                                    <div
                                        class="d-flex align-items-center justify-content-center round-48 rounded text-bg-secondary flex-shrink-0 mb-3 mx-auto">
                                        <iconify-icon icon="mdi:map-marker-multiple" class="fs-7 text-white"></iconify-icon>
                                    </div>
                                    <h6 class="fw-normal fs-3 mb-1">Office Locations</h6>
                                    <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                        @foreach ($activeOfficeLocations as $location)
                                            <span class="badge bg-success">{{ $location }}</span>
                                        @endforeach
                                    </h4>
                                    <a href="{{ route('office.index') }}"
                                        class="btn btn-white fs-2 fw-semibold text-nowrap">View Details</a>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card danger-gradient">
                                <div class="card-body text-center px-9 pb-4">
                                    <div
                                        class="d-flex align-items-center justify-content-center round-48 rounded text-bg-danger flex-shrink-0 mb-3 mx-auto">
                                        <iconify-icon icon="mdi:chart-box" class="fs-7 text-white"></iconify-icon>
                                    </div>
                                    <h6 class="fw-normal fs-3 mb-1">Attendance Report</h6>
                                    <h4 class="mb-3 d-flex align-items-center justify-content-center gap-1">
                                        <span class="badge bg-primary">
                                            <small>This month</small>
                                        </span>
                                    </h4>

                                    <a href="{{ route('attendance.report.index') }}"
                                        class="btn btn-white fs-2 fw-semibold text-nowrap">View
                                        Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="card-title">Attendance Graph</h5>
                            <p class="card-subtitle mb-0">Overview of Attendance Monthly</p>
                        </div>

                        <div class="hstack gap-9 mt-4 mt-md-0">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-block flex-shrink-0 round-10 bg-primary rounded-circle"></span>
                                <span class="text-nowrap text-muted">This Monthly</span>
                            </div>
                        </div>
                    </div>
                    <div style="height: 305px;" class="me-n2 rounded-bars">
                        <div id="attendance-graph"></div>
                    </div>
                    <div class="row mt-4 mb-2">
                        <div class="col-md-4">
                            <div class="hstack gap-6 mb-3 mb-md-0">
                                <span class="d-flex align-items-center justify-content-center round-48 bg-light rounded">
                                    <iconify-icon icon="solar:pie-chart-2-linear" class="fs-7 text-dark"></iconify-icon>
                                </span>
                                <div>
                                    <span>Total Attendance This Year</span>
                                    <h5 class="mt-1 fw-medium mb-0">{{ $totalAttendanceThisYear }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="hstack gap-6 mb-3 mb-md-0">
                                <span
                                    class="d-flex align-items-center justify-content-center round-48 bg-primary-subtle rounded">
                                    <iconify-icon icon="solar:dollar-minimalistic-linear"
                                        class="fs-7 text-primary"></iconify-icon>
                                </span>
                                <div>
                                    <span>Last Month's Attendance</span>
                                    <h5 class="mt-1 fw-medium mb-0">{{ $lastMonthAttendance }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="hstack gap-6">
                                <span
                                    class="d-flex align-items-center justify-content-center round-48 bg-danger-subtle rounded">
                                    <iconify-icon icon="solar:database-linear" class="fs-7 text-danger"></iconify-icon>
                                </span>
                                <div>
                                    <span>Attendance Summary</span>
                                    <h5 class="mt-1 fw-medium mb-0">{{ $attendanceMonthsCount }} Months Data</h5>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/extra-libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/extra-libs/jvectormap/jquery-jvectormap-us-aea-en.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var attendanceData = @json($attendanceData);
            var options = {
                series: [{
                    name: "Attendance Stats",
                    data: attendanceData,
                }, ],

                chart: {
                    toolbar: {
                        show: false,
                    },

                    height: 220,
                    type: "bar",
                    offsetX: -30,
                    fontFamily: "inherit",
                    foreColor: "#adb0bb",
                },
                colors: [
                    "var(--bs-gray-100)",
                    "var(--bs-gray-100)",
                    "var(--bs-gray-100)",
                    "var(--bs-primary)",
                    "var(--bs-gray-100)",
                    "var(--bs-gray-100)",
                    "var(--bs-gray-100)",
                ],
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        columnWidth: "55%",
                        distributed: true,
                        endingShape: "rounded",
                    },
                },

                dataLabels: {
                    enabled: false,
                },
                legend: {
                    show: false,
                },
                grid: {
                    yaxis: {
                        lines: {
                            show: false,
                        },
                    },
                    xaxis: {
                        lines: {
                            show: false,
                        },
                    },
                },
                xaxis: {
                    categories: [
                        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                    ],
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                yaxis: {
                    labels: {
                        show: false,
                    },
                },
                tooltip: {
                    theme: "dark",
                },
            };

            var chart = new ApexCharts(document.querySelector("#attendance-graph"), options);
            chart.render();
        });
    </script>
@endsection
