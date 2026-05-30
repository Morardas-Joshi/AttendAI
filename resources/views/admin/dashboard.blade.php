@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <div class="row">

        <!-- STUDENTS -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">

                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalStudents }}
                            </div>
                        </div>

                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- FACULTY -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">

                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Faculty
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalFaculty }}
                            </div>
                        </div>

                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- ACTIVE SESSIONS -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeSessions }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATTENDANCE % -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Today's Attendance %</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendancePercent }}%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <!-- AI SERVICE STATUS -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">AI Recognition Service</h6>
                    @if($aiStatus === 'online')
                        <span class="badge bg-success">Online</span>
                    @else
                        <span class="badge bg-danger">Offline</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($aiStatus === 'online')
                        <p><strong>Students Registered:</strong> {{ $aiData['students_registered'] ?? 0 }}</p>
                        <p><strong>Total Encodings:</strong> {{ $aiData['total_encodings'] ?? 0 }}</p>
                        <p class="text-muted small">The face recognition engine is running normally and connected to the database.</p>
                    @else
                        <p class="text-danger">Could not connect to the Python AI service. Face recognition is currently suspended.</p>
                        <p class="small text-muted">Ensure <code>uvicorn main:app</code> is running on port 8001.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- WELCOME CARD -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Welcome</h6>
                </div>
                <div class="card-body">
                    Welcome back, {{ auth()->user()->name }}.<br><br>
                    You can manage students, faculty, and monitor live attendance sessions directly from this panel.
                </div>
            </div>
        </div>

    </div>

</div>

@endsection