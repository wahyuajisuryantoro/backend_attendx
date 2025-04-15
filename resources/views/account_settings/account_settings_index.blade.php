@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection
@section('content')
    <div class="card card-body py-3">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="d-sm-flex align-items-center justify-space-between">
                    <h4 class="mb-4 mb-sm-0 card-title">Account Setting</h4>
                    <nav aria-label="breadcrumb" class="ms-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item d-flex align-items-center">
                                <a class="text-muted text-decoration-none d-flex" href="{{ route('dashboard') }}">
                                    <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">
                                    Account Setting
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-account" role="tabpanel"
                        aria-labelledby="pills-account-tab" tabindex="0">
                        <div class="row">
                            <div class="col-lg-6 d-flex align-items-stretch">
                                <div class="card w-100 border position-relative overflow-hidden">
                                    <div class="card">
                                        <div class="card-body p-4">
                                            <div
                                                class="text-bg-light rounded-1 p-6 d-inline-flex align-items-center justify-content-center mb-3">
                                                <i class="ti ti-device-laptop text-primary d-block fs-7" width="22"
                                                    height="22"></i>
                                            </div>
                                            <h4 class="card-title mb-0">Device Information</h4>
                                            <p class="mb-3">These are the devices that have logged into your account
                                                recently.</p>
                                            <div class="alert alert-info">
                                                <h5 class="fs-4 fw-semibold mb-0">Current Device</h5>
                                                <p class="mb-0">{{ $deviceInfo['device'] }} ({{ $deviceInfo['browser'] }}
                                                    on {{ $deviceInfo['platform'] }})</p>
                                                <p class="mb-0">IP: {{ $deviceInfo['ip_address'] }}</p>
                                                <p class="mb-0">Last active: {{ $deviceInfo['last_activity'] }}</p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-stretch">
                                <div class="card w-100 border position-relative overflow-hidden">
                                    <div class="card-body p-4">
                                        <h4 class="card-title">Change Password</h4>
                                        <p class="card-subtitle mb-4">To change your password please confirm here</p>
                                        <form action="{{ route('account.update-password') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label">Current Password</label>
                                                <input type="password"
                                                    class="form-control @error('current_password') is-invalid @enderror"
                                                    id="current_password" name="current_password">
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label">New Password</label>
                                                <input type="password"
                                                    class="form-control @error('new_password') is-invalid @enderror"
                                                    id="new_password" name="new_password">
                                                @error('new_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="new_password_confirmation" class="form-label">Confirm
                                                    Password</label>
                                                <input type="password" class="form-control" id="new_password_confirmation"
                                                    name="new_password_confirmation">
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_key_password" class="form-label">Admin Secret Key</label>
                                                <input type="password"
                                                    class="form-control @error('admin_key') is-invalid @enderror"
                                                    id="admin_key_password" name="admin_key">
                                                @error('admin_key')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 mt-2">
                                                <div class="d-flex align-items-center justify-content-end gap-6">
                                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                                    <button type="reset"
                                                        class="btn bg-danger-subtle text-danger">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const adminKeyInput = form.find('input[name="admin_key"]');

                $.ajax({
                    url: "{{ route('account.verify-key') }}",
                    type: "POST",
                    data: {
                        admin_key: adminKeyInput.val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Confirmation',
                                text: 'Are you sure you want to save these changes?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, save it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form[0].submit();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Invalid admin key. Changes cannot be saved.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while verifying admin key',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
@endsection
