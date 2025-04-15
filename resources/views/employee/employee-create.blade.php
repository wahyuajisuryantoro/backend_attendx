@extends('layouts.app')

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header text-bg-primary">
                <h4 class="mb-0 text-white">Add New Employee</h4>
            </div>
            <form id="employee-form" action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <input type="hidden" name="user_id" value="{{ old('user_id', $userId ?? '') }}" />
                    <div class="card-body">
                        <div class="row pt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="jhondoe"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required />
                                </div>
                            </div>
                        </div>
                        <h4 class="card-title">Person Info</h4>
                        <div class="row pt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="John doe"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="jhon@attendx.com"
                                        required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control" placeholder="IT"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Position</label>
                                    <input type="text" name="position" class="form-control" placeholder="Fullstack Dev"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" placeholder="+62-888-888-888"
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="3"
                                        placeholder="1600 Pennsylvania Avenue NW, Washington, D.C. 20500, USA" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <input class="form-control" type="file" name="profile_photo" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="card-body border-top">
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                Save
                            </button>
                            <button type="button" class="btn bg-danger-subtle text-danger ms-6">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/plugins/toastr-init.js') }}"></script>
    <script>
        $(document).ready(function() {

            $("#employee-form").on("submit", function(e) {
                e.preventDefault();


                toastr.info("Processing your request...", "Please wait", {
                    progressBar: true,
                    timeOut: 5000,
                    extendedTimeOut: 0,
                    showDuration: 300,
                    hideDuration: 1000,
                });


                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr("action"),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        toastr.success("Employee created successfully!", "Success", {
                            progressBar: true,
                            timeOut: 5000,
                        });

                        window.location.href =
                            "{{ route('employee.list') }}";
                    },
                    error: function(response) {
                        toastr.error("Failed to create employee: " + response.responseJSON
                            .message, "Error", {
                                progressBar: true,
                                timeOut: 5000,
                            });
                    }
                });
            });
        });
    </script>
@endsection
