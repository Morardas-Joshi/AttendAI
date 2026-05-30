@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow mb-4 border-0" style="border-radius: 12px;">
            <div class="card-header py-3 bg-white border-bottom-0">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-circle"></i> My Profile</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        @if($student->user->profile_photo)
                            <img src="{{ asset($student->user->profile_photo) }}" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('attendai/img/undraw_profile.svg') }}" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                        <h5 class="mt-3 font-weight-bold">{{ $student->user->name }}</h5>
                        <span class="badge {{ $student->user->face_registered ? 'badge-success' : 'badge-danger' }}">
                            {{ $student->user->face_registered ? 'Face Registered' : 'Face Not Registered' }}
                        </span>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;" class="text-muted">Email</th>
                                    <td>{{ $student->user->email }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Roll No</th>
                                    <td>{{ $student->roll_no }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Class</th>
                                    <td>{{ $student->class->course_name ?? 'N/A' }} {{ isset($student->class->division) ? ' - ' . $student->class->division : '' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        @if(!$student->user->face_registered)
                            <div class="mt-3">
                                <a href="{{ route('student.face.register') }}" class="btn btn-warning btn-sm shadow-sm"><i class="fas fa-camera"></i> Register Face Data Now</a>
                            </div>
                        @else
                        <div class="mt-3 text-success">
                            <i class="fas fa-check-circle"></i> Your face data is configured correctly for attendance.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
