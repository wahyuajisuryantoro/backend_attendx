@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/dist/apexcharts.css') }}" />
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                @if($user->profile && $user->profile->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile->profile_photo) }}" class="rounded-circle me-3" width="80" alt="Profile Photo">
                @else
                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background-color: #ddd; font-size: 32px;">
                        {{ strtoupper(substr($user->profile->name ?? 'N/A', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h3 class="mb-1">{{ $user->profile->name ?? 'N/A' }}</h3>
                    <p class="mb-0 text-muted">
                        {{ $user->profile->position ?? 'N/A' }} | {{ $user->profile->department ?? 'N/A' }}
                    </p>
                    <p class="mb-0 text-muted">
                        Employee ID: {{ $user->profile->employee_id ?? 'N/A' }}
                    </p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <h5>Total Attendance</h5>
                            <h2>{{ $totalDays }}</h2>
                            <p class="mb-0">Days</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <h5>On Time</h5>
                            <h2>{{ $onTimeDays }}</h2>
                            <p class="mb-0">{{ number_format($totalDays > 0 ? ($onTimeDays / $totalDays) * 100 : 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <h5>Late</h5>
                            <h2>{{ $lateDays }}</h2>
                            <p class="mb-0">{{ number_format($totalDays > 0 ? ($lateDays / $totalDays) * 100 : 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body text-center">
                            <h5>Absent</h5>
                            <h2>{{ $absentDays }}</h2>
                            <p class="mb-0">{{ number_format($totalDays > 0 ? ($absentDays / $totalDays) * 100 : 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Monthly Attendance</h5>
                </div>
                <div class="card-body">
                    @if(count($monthlyData) > 0)
                        <div id="monthlyAttendanceChart" style="height: 300px;"></div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-chart-bar text-muted" style="font-size: 48px;"></i>
                            <p class="mt-2">No monthly attendance data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Attendance Summary</h5>
                </div>
                <div class="card-body">
                    @if($totalDays > 0)
                        <div id="attendanceSummaryChart" style="height: 300px;"></div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-chart-pie text-muted" style="font-size: 48px;"></i>
                            <p class="mt-2">No attendance data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Attendance History</h5>
            <div>
                @if(count($user->attendances ?? []) > 0)
                    <a href="{{ route('attendance.report.employee.export', $user->id) }}" class="btn btn-sm btn-primary">
                        <i class="ti ti-file-export"></i> Export Report
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="employeeAttendanceTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Status</th>
                            <th>Work Hours</th>
                            <th>Method</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->attendances ?? [] as $attendance)
                            <tr>
                                <td>{{ $attendance->date->format('Y-m-d') }}</td>
                                <td>{{ $attendance->clock_in ? Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') : 'N/A' }}</td>
                                <td>{{ $attendance->clock_out ? Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s') : 'N/A' }}</td>
                                <td>
                                    @if(!$attendance->clock_in)
                                        <span class="badge bg-danger">Absent</span>
                                    @elseif(Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') <= '09:00:00')
                                        <span class="badge bg-success">On Time</span>
                                    @else
                                        <span class="badge bg-warning">Late</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->clock_in && $attendance->clock_out)
                                        @php
                                            $clockIn = Carbon\Carbon::parse($attendance->clock_in);
                                            $clockOut = Carbon\Carbon::parse($attendance->clock_out);
                                            $diffInHours = $clockOut->diffInHours($clockIn);
                                            $diffInMinutes = $clockOut->diffInMinutes($clockIn) % 60;
                                            echo $diffInHours . 'h ' . $diffInMinutes . 'm';
                                        @endphp
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $attendance->clock_in_method ?? 'N/A' }}</td>
                                <td>{{ $attendance->notes ?? 'No notes' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No attendance records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            
            @if(count($user->attendances ?? []) > 0)
                $('#employeeAttendanceTable').DataTable({
                    order: [[0, 'desc']],
                    responsive: true
                });
            @endif
            
            
            @if(count($monthlyData ?? []) > 0)
                
                var monthlyAttendanceOptions = {
                    series: [{
                        name: 'Attendance',
                        data: [
                            @foreach($monthlyData as $data)
                                {{ $data->count }},
                            @endforeach
                        ]
                    }],
                    chart: {
                        height: 300,
                        type: 'bar',
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '50%',
                            borderRadius: 4
                        }
                    },
                    xaxis: {
                        categories: [
                            @foreach($monthlyData as $data)
                                '{{ date("F", mktime(0, 0, 0, $data->month, 10)) }}',
                            @endforeach
                        ]
                    },
                    colors: ['#3699ff'],
                    title: {
                        text: 'Monthly Attendance ({{ date("Y") }})',
                        align: 'center'
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return value + ' days';
                            }
                        }
                    }
                };
                
                var monthlyChartElement = document.querySelector("#monthlyAttendanceChart");
                if (monthlyChartElement) {
                    var monthlyAttendanceChart = new ApexCharts(
                        monthlyChartElement, 
                        monthlyAttendanceOptions
                    );
                    monthlyAttendanceChart.render();
                }
            @endif
            
            @if($totalDays > 0)
                
                var attendanceSummaryOptions = {
                    series: [{{ $onTimeDays }}, {{ $lateDays }}, {{ $absentDays }}],
                    chart: {
                        height: 300,
                        type: 'donut',
                    },
                    labels: ['On Time', 'Late', 'Absent'],
                    colors: ['#28c76f', '#ff9f43', '#ea5455'],
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return value + ' days';
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };
                
                var summaryChartElement = document.querySelector("#attendanceSummaryChart");
                if (summaryChartElement) {
                    var attendanceSummaryChart = new ApexCharts(
                        summaryChartElement, 
                        attendanceSummaryOptions
                    );
                    attendanceSummaryChart.render();
                }
            @endif
        });
    </script>
@endsection