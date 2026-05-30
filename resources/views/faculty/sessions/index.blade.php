@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row w-100 align-items-center mb-4">
        <div class="col">
            <h3>My Attendance Sessions</h3>
        </div>
        <div class="col text-end">
            <a href="{{ route('faculty.sessions.create') }}" class="btn btn-primary">+ Create New Session</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Date / Time</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Attendees</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $session->subject->subject_name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $session->subject->class->course_name ?? '' }} | Y:{{ $session->subject->class->year ?? '-' }} S:{{ $session->subject->class->semester ?? '-' }} | Div:{{ $session->subject->class->division ?? '-' }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($session->start_time)->format('d M, h:i A') }}</td>
                                <td><span class="badge bg-dark fw-bold fs-6" style="letter-spacing: 1px">{{ $session->session_code }}</span></td>
                                <td>
                                    @if($session->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Closed</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark rounded-pill">{{ $session->records_count }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('faculty.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">Live View ➔</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No attendance sessions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sessions->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection