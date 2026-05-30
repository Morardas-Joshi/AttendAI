@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Student Attendance Summary</h3>
        <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary">View All Records</a>
    </div>

    <!-- Filters -->
    <div class="card shadow border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.students') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search name or roll no..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->course_name }} - {{ $class->division }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.reports.students') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student Info</th>
                            <th>Class</th>
                            <th class="text-center">Total Sessions</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent / Rejected</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-end pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $student->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">Roll No: {{ $student->roll_no }}</small>
                            </td>
                            <td>{{ $student->class->course_name ?? 'N/A' }} - {{ $student->class->division ?? '' }}</td>
                            <td class="text-center fw-bold">{{ $student->total_sessions }}</td>
                            <td class="text-center">
                                <span class="badge bg-success opacity-75">{{ $student->present_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger opacity-75">{{ $student->absent_count }}</span>
                            </td>
                            <td class="text-center">
                                <div class="progress" style="height: 10px; border-radius: 10px;">
                                    <div class="progress-bar bg-{{ $student->attendance_percentage > 75 ? 'success' : ($student->attendance_percentage > 50 ? 'warning' : 'danger') }}" 
                                         role="progressbar" style="width:{{ $student->attendance_percentage }}%"></div>
                                </div>
                                <small class="fw-bold">{{ $student->attendance_percentage }}%</small>
                            </td>
                            <td class="text-end pe-4">
                                @if($student->attendance_percentage >= 75)
                                    <span class="badge bg-success">Safe</span>
                                @else
                                    <span class="badge bg-warning text-dark">Low Attendance</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No students found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
