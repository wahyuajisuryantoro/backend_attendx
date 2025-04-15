@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('content')
    <div class="shop-detail">
        <div class="card">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="item rounded overflow-hidden">
                            <img src="{{ $employee->profile->profile_photo ? asset('storage/' . $employee->profile->profile_photo) : asset('assets/images/products/s1.jpg') }}" alt="matdash-img" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="shop-content">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge fs-2 fw-semibold {{ $employee->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                
                            </div>
                            <h4>{{ $employee->profile->name }}</h4>
                           
                            <div class="d-flex align-items-center gap-8 pb-4 border-bottom">
                                <div class="vstack gap-3 mt-4">
                                    <div class="hstack gap-6">
                                      <i class="ti ti-briefcase text-dark fs-6"></i>
                                      <h6 class="mb-0">{{ $employee->profile->department }}</h6>
                                    </div>
                                    <div class="hstack gap-6">
                                      <i class="ti ti-mail text-dark fs-6"></i>
                                      <h6 class="mb-0">{{ $employee->profile->email }}</h6>
                                    </div>
                                    <div class="hstack gap-6">
                                      <i class="ti ti-device-desktop text-dark fs-6"></i>
                                      <h6 class="mb-0">{{ $employee->profile->position }}</h6>
                                    </div>
                                    <div class="hstack gap-6">
                                      <i class="ti ti-map-pin text-dark fs-6"></i>
                                      <h6 class="mb-0">{{ $employee->profile->address }}</h6>
                                    </div>
                                  </div>
                            </div>
                            <div class="d-sm-flex align-items-center gap-6 pt-12 mb-7">
                                <!-- Tombol Edit dan Hapus -->
                                <a href="{{ route('employee.edit', $employee->id) }}" class="btn d-block btn-primary px-5 py-8 mb-6 mb-sm-0"><i class="ti ti-edit"></i> Edit</a>
                                <a href="javascript:void(0)" class="btn d-block btn-danger px-7 py-8" id="delete-btn"><i class="ti ti-trash"></i> Delete Employee</a>
                                
                                <form id="delete-form" action="{{ route('employee.destroy', $employee->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
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
        document.getElementById('delete-btn').addEventListener('click', function() {
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
                    document.getElementById('delete-form').submit();
                }
            });
        });
    </script>
@endsection