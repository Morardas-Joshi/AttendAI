@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-center mb-4">
        <h3>Join Attendance Session</h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4">

            <!-- Alerts -->
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Card -->
            <div class="card shadow">
                <div class="card-body">

                    <form method="POST" action="{{ route('student.join.store') }}">
                        @csrf

                        <!-- Session Code -->
                        <div class="form-group mb-3">
                            <label>Session Code</label>
                            <input type="text"
                                   name="session_code"
                                   class="form-control text-uppercase form-control-lg"
                                   placeholder="Enter 6-Character Code"
                                   maxlength="6"
                                   style="letter-spacing: 2px; font-weight: bold; text-align: center"
                                   value="{{ request('code') }}"
                                   oninput="this.value = this.value.toUpperCase()"
                                   required>
                        </div>

                        <!-- Button -->
                        <button class="btn btn-primary btn-block">
                            Join Session
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection