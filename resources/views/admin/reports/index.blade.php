@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Attendance Reports</h3>
        <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-success" onclick="event.preventDefault(); window.location.href='{{ route('admin.reports.export', request()->query()) }}';">
            <i class="bi bi-file-earmark-excel-fill"></i> Export CSV
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form action="{{ route('admin.reports') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Filter by Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->course_name }} - {{ $class->division }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Filter by Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Filter by Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter Reports</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white" style="border-radius: 10px;">
                <div class="card-body text-center">
                    <h6 class="text-uppercase mb-1">Total Records</h6>
                    <h2 class="mb-0">{{ $total }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white" style="border-radius: 10px;">
                <div class="card-body text-center">
                    <h6 class="text-uppercase mb-1">Total Present</h6>
                    <h2 class="mb-0">{{ $present }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white" style="border-radius: 10px;">
                <div class="card-body text-center">
                    <h6 class="text-uppercase mb-1">Total Rejected / Absent</h6>
                    <h2 class="mb-0">{{ $absent }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Date / Time</th>
                            <th>Status</th>
                            <th>Liveness</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-primary">{{ $rec->student->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $rec->student->roll_no ?? '' }}</small>
                                </td>
                                <td>{{ $rec->student->class->course_name ?? 'N/A' }} - {{ $rec->student->class->division ?? '' }}</td>
                                <td>{{ $rec->session->subject->subject_name ?? 'N/A' }}</td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($rec->marked_at)->format('d M, Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($rec->marked_at)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    @if($rec->status === 'present')
                                        <span class="badge bg-success">Present</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                        <br><small class="text-muted" style="font-size: 0.70rem;">{{ $rec->rejection_reason }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($rec->liveness_passed)
                                        <span class="badge bg-outline-success border border-success text-success">Pass</span>
                                    @else
                                        <span class="badge bg-outline-danger border border-danger text-danger">Fail</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 60px;">
                                        <div class="progress-bar {{ $rec->confidence_score > 80 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $rec->confidence_score }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $rec->confidence_score }}%</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No records match the current filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($records->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $records->links() }}
        </div>
        @endif
    </div>

</div>
@endsection