@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between mb-4 mt-2">
        <h3 class="font-weight-bold text-gray-800">My Attendance History</h3>
    </div>

    <!-- 📊 Subject-wise Summary Cards -->
    <div class="row mb-4">
        @foreach($subjectSummary as $subject => $stats)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2" style="border-left: 4px solid {{ $stats['percentage'] >= 75 ? '#1cc88a' : '#f6c23e' }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #5a5c69;">{{ $subject }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['percentage'] }}%</div>
                            <div class="small text-muted mt-1">{{ $stats['present'] }} / {{ $stats['total'] }} Present</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 📋 Detailed History Table -->
    <div class="card shadow border-0" style="border-radius: 12px;">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Detailed Log</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Subject</th>
                            <th>Faculty</th>
                            <th>Date / Time</th>
                            <th>Distance</th>
                            <th>Status</th>
                            <th>Confidence Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $rec->session->subject->subject_name ?? 'N/A' }}</td>
                                <td>{{ $rec->session->faculty->user->name ?? 'N/A' }}</td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($rec->marked_at)->format('d M, Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($rec->marked_at)->format('h:i A') }}</small>
                                </td>
                                <td>{{ $rec->distance_meters ? round($rec->distance_meters) . 'm' : 'N/A' }}</td>
                                <td>
                                    @if($rec->status === 'present')
                                        <span class="badge bg-success">Present</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                        <br><span title="{{ $rec->rejection_reason }}">ℹ️</span>
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
                                <td colspan="6" class="text-center py-4 text-muted">You have no attendance records yet.</td>
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
