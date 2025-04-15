<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <title>Login - AttendX</title>
    <style>
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
        .is-invalid~.invalid-feedback {
            display: block;
        }
    </style>
</head>

<body class="link-sidebar">
    <div id="main-wrapper">
        <div class="position-relative overflow-hidden auth-bg min-vh-100 w-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100 my-5 my-xl-0">
                    <div class="col-md-9 d-flex flex-column justify-content-center">
                        <div class="card mb-0 bg-body auth-login m-auto w-100">
                            <div class="row gx-0">
                                <div class="col-xl-6 border-end">
                                    <div class="row justify-content-center py-4">
                                        <div class="col-lg-11">
                                            <div class="card-body">
                                                <a href="#" class="text-nowrap logo-img d-block mb-4 w-50">
                                                    <img src="{{ asset('assets/images/logos/logo.png') }}"
                                                        class="dark-logo" alt="Logo-Dark" />
                                                </a>
                                                <h2 class="lh-base mb-4">Let's get you signed in</h2>

                                                <form method="POST" action="{{ route('login.post') }}">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="username" class="form-label">Username</label>
                                                        <input type="text"
                                                            class="form-control @error('username') is-invalid @enderror"
                                                            id="username" name="username" value="{{ old('username') }}"
                                                            placeholder="Enter your username" required autofocus>
                                                        @error('username')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="password" class="form-label">Password</label>
                                                        <input type="password"
                                                            class="form-control @error('password') is-invalid @enderror"
                                                            id="password" name="password"
                                                            placeholder="Enter your password" required>
                                                        @error('password')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <button type="submit"
                                                        class="btn btn-dark w-100 py-8 mb-4 rounded-1">
                                                        Sign In
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 d-none d-xl-block">
                                    <div class="row justify-content-center align-items-start h-100">
                                        <div class="col-lg-9">
                                            <div class="text-center p-5">
                                                <img src="{{ asset('assets/images/backgrounds/login-side-1.png') }}"
                                                    alt="login-side-img" width="300" class="img-fluid" />
                                                <h4 class="mb-0 mt-4">Easy and Secure Login</h4>
                                                <p class="mt-2">Experience seamless access with our advanced login system.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                alert('Error: {{ $error }}');
            @endforeach
        @endif

        @if (session('success'))
            alert('Success: {{ session('success') }}');
        @endif
        @if (session('error'))
            alert('Error: {{ session('error') }}');
        @endif
    </script>
</body>

</html>