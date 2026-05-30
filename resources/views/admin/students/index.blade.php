@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0">Manage Students</h3>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">+ Add New Student</a>
    </div>

    <!-- 🔍 Filters -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form action="{{ route('admin.students') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email or roll no..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->course_name }} - {{ $class->division }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Photo</th>
                            <th>Name</th>
                            <th>Student ID (Roll No)</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Face Registered</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="ps-4 text-center">
                                    @if($student->user->profile_photo)
                                        <img src="{{ asset($student->user->profile_photo) }}" class="rounded-circle shadow-sm" width="45" height="45" style="object-fit: cover; border: 2px solid #ddd;">
                                    @else
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border: 2px solid #eee;">
                                            <i class="fas fa-user text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold text-primary">{{ $student->user->name ?? 'N/A' }}</td>
                                <td class="fw-bold">{{ $student->roll_no }}</td>
                                <td>{{ $student->user->email ?? 'N/A' }}</td>
                                <td>
                                    @if($student->class)
                                        {{ $student->class->course_name }} ({{ $student->class->division }})
                                    @else
                                        <span class="text-danger">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->user->face_registered)
                                        <span class="badge badge-success px-3 py-2">
                                            <i class="fas fa-check-circle mr-1"></i> Yes
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">
                                            <i class="fas fa-times-circle mr-1"></i> No
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end" style="gap: 5px;">
                                        <a href="{{ route('admin.students.face.register', $student->id) }}" class="btn btn-sm btn-info" title="Register Face">
                                            <i class="fas fa-camera"></i>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($students->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>
@endsection