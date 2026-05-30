@extends('layouts.app')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Student Dashboard</h1>
</div>

@if(auth()->user() && !auth()->user()->face_registered)
    <div class="alert alert-danger shadow-sm d-flex justify-content-between align-items-center mb-4" role="alert">
        <div>
            <h5 class="alert-heading font-weight-bold mb-1"><i class="fas fa-exclamation-triangle"></i> Face Registration Required</h5>
            <p class="mb-0">You have not registered your face yet. You won't be able to mark attendance until you do.</p>
        </div>
        <a href="{{ route('student.face.register') }}" class="btn btn-danger font-weight-bold">Register Now <i class="fas fa-arrow-right"></i></a>
    </div>
@endif

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Join Attendance Session</h6>
            </div>
            <div class="card-body">
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

                <form method="POST" action="{{ route('student.join.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="session_code">Session Code</label>
                        <input type="text" class="form-control form-control-lg text-center fw-bold" name="session_code" id="session_code" placeholder="ENTER 6-DIGIT CODE" required  style="letter-spacing: 2px;">
                    </div>

                    <button class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold">
                        <i class="fas fa-sign-in-alt"></i> Join Room Session
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-circle"></i> Profile Overview</h6>
            </div>
            <div class="card-body text-center">
                @if(auth()->user()->profile_photo)
                    <img src="{{ asset(auth()->user()->profile_photo) }}" class="rounded-circle shadow mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <img src="{{ asset('attendai/img/undraw_profile.svg') }}" class="rounded-circle shadow mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @endif
                <h5 class="font-weight-bold mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                <div class="mt-3">
                    <a href="{{ route('student.profile') }}" class="btn btn-outline-info btn-sm">View Full Profile</a>
                    <a href="{{ route('student.my_attendance') }}" class="btn btn-outline-primary btn-sm">My Attendance</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection