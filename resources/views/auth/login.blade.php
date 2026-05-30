<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AttendAI Login</title>

    <!-- CSS -->
    <link href="{{ asset('attendai/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('attendai/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container">

    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">

        <div class="col-xl-5 col-lg-6 col-md-8">

            <div class="card shadow-lg border-0 rounded-lg">

                <div class="card-body p-5">

                    <!-- 🔵 TITLE -->
                    <div class="text-center mb-4">
                        <h2 class="text-primary font-weight-bold">AttendAI</h2>
                        <p class="text-gray-600">Welcome Back 👋</p>
                        <small class="text-muted">Login to your account</small>
                    </div>

                    <!-- 🔥 LOGIN FORM -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                <input type="email" name="email"
                                    class="form-control"
                                    placeholder="Enter Email Address..."
                                    value="{{ old('email') }}"
                                    required autofocus>
                            </div>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <input type="password" name="password"
                                    class="form-control"
                                    placeholder="Password"
                                    required>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Remember -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" name="remember" class="custom-control-input" id="remember">
                                <label class="custom-control-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                            Login
                        </button>

                    </form>

                    <!-- ERROR (general) -->
                    @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <hr>

                    <!-- LINKS -->
                    <div class="text-center">
                        <a class="small" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    </div>

                    <div class="text-center">
                        <a class="small" href="{{ route('register') }}">
                            Create an Account!
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- JS -->
<script src="{{ asset('attendai/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('attendai/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('attendai/js/sb-admin-2.min.js') }}"></script>

</body>
</html>