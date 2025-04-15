@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-results__option {
            padding: 0.375rem 0.75rem;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Shift Assignments</h5>
                    <button type="button" class="btn btn-primary" id="create-btn">
                        <i class="mdi mdi-plus me-1"></i> Assign New Shift
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="assignments-table" class="table table-striped text-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Employee</th>
                            <th>ID</th>
                            <th>Department</th>
                            <th>Shift</th>
                            <th>Time</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignmentModalLabel">Assign Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignmentForm">
                        <input type="hidden" id="assignment_id" name="assignment_id">

                        <div class="mb-3" id="employee-container">
                            <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="user_id" name="user_id" required>
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        data-department="{{ $employee->profile->department ?? 'N/A' }}"
                                        data-employee-id="{{ $employee->profile->employee_id ?? 'N/A' }}">
                                        {{ $employee->profile->name ?? 'Unknown' }}
                                        ({{ $employee->profile->employee_id ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="user_id-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="employee_details" class="form-label">Employee Details</label>
                            <div class="alert alert-info" id="employee_details">
                                <p class="mb-0"><strong>ID:</strong> <span id="employee_id">-</span></p>
                                <p class="mb-0"><strong>Department:</strong> <span id="employee_department">-</span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shift_id" class="form-label">Work Shift <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="shift_id" name="shift_id" required>
                                <option value="">Select Work Shift</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}"
                                        data-start="{{ date('H:i', strtotime($shift->start_time)) }}"
                                        data-end="{{ date('H:i', strtotime($shift->end_time)) }}"
                                        data-late="{{ $shift->late_threshold_minutes }}">
                                        {{ $shift->name }} ({{ date('H:i', strtotime($shift->start_time)) }} -
                                        {{ date('H:i', strtotime($shift->end_time)) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="shift_id-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="shift_details" class="form-label">Shift Details</label>
                            <div class="alert alert-info" id="shift_details">
                                <p class="mb-0"><strong>Work Hours:</strong> <span id="shift_hours">-</span></p>
                                <p class="mb-0"><strong>Late Threshold:</strong> <span id="shift_late">-</span>
                                    minutes
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" id="start_date" name="start_date"
                                    placeholder="Select date" required>
                                <div class="invalid-feedback" id="start_date-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date (Optional)</label>
                                <input type="text" class="form-control datepicker" id="end_date" name="end_date"
                                    placeholder="Leave empty for indefinite">
                                <div class="invalid-feedback" id="end_date-error"></div>
                            </div>
                        </div>

                        <div class="mb-3 form-check" id="is_active_container">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-btn">Save Assignment</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#assignmentModal')
            });

            const today = new Date();
            
            $('.datepicker').flatpickr({
                dateFormat: "Y-m-d",
                minDate: today,
                allowInput: true
            });

            $('#user_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    $('#employee_id').text(selectedOption.data('employee-id'));
                    $('#employee_department').text(selectedOption.data('department'));
                } else {
                    $('#employee_id').text('-');
                    $('#employee_department').text('-');
                }
            });

            $('#shift_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    $('#shift_hours').text(selectedOption.data('start') + ' - ' + selectedOption.data('end'));
                    $('#shift_late').text(selectedOption.data('late'));
                } else {
                    $('#shift_hours').text('-');
                    $('#shift_late').text('-');
                }
            });

            var assignmentsTable = $('#assignments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('shift-assignments.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'employee_id',
                        name: 'employee_id'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'shift_name',
                        name: 'shift_name'
                    },
                    {
                        data: 'shift_time',
                        name: 'shift_time'
                    },
                    {
                        data: 'period',
                        name: 'period'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $('.edit-btn').off('click').on('click', function() {
                        editAssignment($(this).data('id'));
                    });

                    $('.delete-btn').off('click').on('click', function() {
                        deleteAssignment($(this).data('id'));
                    });
                }
            });

            $('#create-btn').click(function() {
                resetForm();
                $('#assignmentModalLabel').text('Assign Shift');
                $('#assignmentModal').modal('show');
            });
            $('#save-btn').click(function() {
                saveAssignment();
            });

            function resetForm() {
                $('#assignmentForm')[0].reset();
                $('#assignment_id').val('');
                $('#user_id').val('').trigger('change');
                $('#shift_id').val('').trigger('change');
                $('#employee-container').show();
                $('#is_active_container').hide();

                $('#employee_id').text('-');
                $('#employee_department').text('-');
                $('#shift_hours').text('-');
                $('#shift_late').text('-');

                clearErrors();
            }

            function clearErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function editAssignment(id) {
                clearErrors();
                $('#assignmentModalLabel').text('Edit Shift Assignment');

                $.ajax({
                    url: "{{ route('shift-assignments.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            const assignment = response.data;
                            $('#assignment_id').val(assignment.id);
                            $('#employee-container').hide();
                            $('#employee_id').text(assignment.user.profile?.employee_id || 'N/A');
                            $('#employee_department').text(assignment.user.profile?.department ||
                                'N/A');
                            $('#shift_id').val(assignment.shift_id).trigger('change');
                            $('#start_date').val(assignment.start_date);
                            $('#end_date').val(assignment.end_date || '');
                            $('#is_active_container').show();
                            $('#is_active').prop('checked', assignment.is_active);
                            const selectedShift = $('#shift_id').find('option:selected');
                            if (selectedShift.val()) {
                                $('#shift_hours').text(selectedShift.data('start') + ' - ' +
                                    selectedShift.data('end'));
                                $('#shift_late').text(selectedShift.data('late'));
                            }
                            $('#assignmentModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        showSweetAlert('error', 'Error', 'Failed to load assignment data');
                        console.error(xhr);
                    }
                });
            }

            function saveAssignment() {
                clearErrors();

                const id = $('#assignment_id').val();
                const isUpdate = id !== '';
                const url = isUpdate ?
                    "{{ route('shift-assignments.update', ':id') }}".replace(':id', id) :
                    "{{ route('shift-assignments.store') }}";
                const method = isUpdate ? 'PUT' : 'POST';

                const formData = {
                    shift_id: $('#shift_id').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val() || null,
                    _token: "{{ csrf_token() }}"
                };

                if (!isUpdate) {
                    formData.user_id = $('#user_id').val();
                } else {
                    formData.is_active = $('#is_active').is(':checked') ? 1 : 0;
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#assignmentModal').modal('hide');
                            showSweetAlert('success', 'Success', response.message);
                            assignmentsTable.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                const errorMsg = Array.isArray(messages) ? messages[0] : messages;
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(errorMsg);
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            showSweetAlert('error', 'Error', xhr.responseJSON.message);
                        } else {
                            showSweetAlert('error', 'Error', 'An error occurred. Please try again.');
                        }
                        console.error(xhr);
                    }
                });
            }
            function deleteAssignment(id) {
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
                            url: "{{ route('shift-assignments.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.status) {
                                    showSweetAlert('success', 'Deleted!', response.message);
                                    assignmentsTable.ajax.reload();
                                } else {
                                    showSweetAlert('error', 'Error', response.message);
                                }
                            },
                            error: function(xhr) {
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    showSweetAlert('error', 'Error', xhr.responseJSON.message);
                                } else {
                                    showSweetAlert('error', 'Error', 'Failed to delete assignment');
                                }
                                console.error(xhr);
                            }
                        });
                    }
                });
            }
            function showSweetAlert(icon, title, text) {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        });
    </script>
@endsection