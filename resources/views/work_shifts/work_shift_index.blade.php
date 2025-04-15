@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Work Shifts List</h5>
                        <button type="button" class="btn btn-primary" id="create-btn">
                            <i class="mdi mdi-plus me-1"></i> Add Work Shift
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="shifts-table" class="table table-striped table-bordered dt-responsive nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Shift Name</th>
                                    <th>Time</th>
                                    <th>Late Threshold (min)</th>
                                    <th>Status</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shiftModal" tabindex="-1" aria-labelledby="shiftModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shiftModalLabel">Add Work Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="shiftForm">
                        <input type="hidden" id="shift_id" name="shift_id">

                        <div class="mb-3">
                            <label for="name" class="form-label">Shift Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                                <div class="invalid-feedback" id="start_time-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                                <div class="invalid-feedback" id="end_time-error"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="late_threshold_minutes" class="form-label">Late Threshold (minutes) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="late_threshold_minutes"
                                name="late_threshold_minutes" min="0" max="180" value="15" required>
                            <div class="invalid-feedback" id="late_threshold_minutes-error"></div>
                            <small class="text-muted">Employee will be marked as late if they check in after shift start
                                time + this threshold</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-btn">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            var shiftsTable = $('#shifts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('work-shifts.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'time_range',
                        name: 'time_range'
                    },
                    {
                        data: 'late_threshold_minutes',
                        name: 'late_threshold_minutes'
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
                    $('.edit-btn').click(function() {
                        editShift($(this).data('id'));
                    });

                    $('.delete-btn').click(function() {
                        deleteShift($(this).data('id'));
                    });
                }
            });


            $('#create-btn').click(function() {
                resetForm();
                $('#shiftModalLabel').text('Add Work Shift');
                $('#shiftModal').modal('show');
            });


            $('#save-btn').click(function() {
                saveShift();
            });


            function resetForm() {
                $('#shiftForm')[0].reset();
                $('#shift_id').val('');
                $('#late_threshold_minutes').val(15);
                clearErrors();
            }


            function clearErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }


            function editShift(id) {
                clearErrors();
                $('#shiftModalLabel').text('Edit Work Shift');

                $.ajax({
                    url: `{{ route('work-shifts.get', '') }}/${id}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            const shift = response.data;


                            $('#shift_id').val(shift.id);
                            $('#name').val(shift.name);
                            $('#start_time').val(shift.start_time.substring(0, 5));
                            $('#end_time').val(shift.end_time.substring(0, 5));
                            $('#late_threshold_minutes').val(shift.late_threshold_minutes);
                            $('#is_active').prop('checked', shift.is_active);


                            $('#shiftModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        showSweetAlert('error', 'Error', 'Failed to load shift data');
                    }
                });
            }


            function saveShift() {
                clearErrors();

                const id = $('#shift_id').val();
                const isUpdate = id !== '';
                const url = isUpdate ?
                    "{{ route('work-shifts.update', ':id') }}".replace(':id', id) :
                    "{{ route('work-shifts.store') }}";
                const method = isUpdate ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        name: $('#name').val(),
                        start_time: $('#start_time').val(),
                        end_time: $('#end_time').val(),
                        late_threshold_minutes: $('#late_threshold_minutes').val(),
                        is_active: $('#is_active').is(':checked') ? 1 : 0,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#shiftModal').modal('hide');

                            showSweetAlert('success', 'Success', response.message);


                            shiftsTable.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;


                            $.each(errors, function(field, messages) {
                                const errorMsg = Array.isArray(messages) ? messages[0] :
                                    messages;
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(errorMsg);
                            });
                        } else {
                            showSweetAlert('error', 'Error', 'An error occurred. Please try again.');
                        }
                    }
                });
            }


            function deleteShift(id) {
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
                            url: `{{ route('work-shifts.destroy', '') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.status) {
                                    showSweetAlert('success', 'Deleted!', response.message);
                                    shiftsTable.ajax.reload();
                                } else {
                                    showSweetAlert('error', 'Error', response.message);
                                }
                            },
                            error: function(xhr) {
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    showSweetAlert('error', 'Error', xhr.responseJSON.message);
                                } else {
                                    showSweetAlert('error', 'Error', 'Failed to delete shift');
                                }
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
