@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Subjects</h3>

        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
            Add Subject
        </a>
    </div>

    <!-- 🔍 FILTER -->
    <div class="card mb-3">
        <div class="card-body">

            <form method="GET" class="row">

                <div class="col-md-4">
                    <input type="text" name="search"
                        class="form-control"
                        placeholder="Search subject..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-4">
                    <select name="class_id" class="form-control">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}"
                            {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->course_name }} - {{ $class->division }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.subjects') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>

        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th><i class="fas fa-book mr-1"></i> Subject</th>
                        <th><i class="fas fa-users mr-1"></i> Class</th>
                        <th><i class="fas fa-chalkboard-teacher mr-1"></i> Faculty</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td class="font-weight-bold text-primary">{{ $subject->subject_name }}</td>

                        <td>
                            <span class="badge badge-info">{{ $subject->class->course_name }}</span>
                            <span class="badge badge-secondary">{{ $subject->class->division }}</span>
                            <div class="small text-muted mt-1">{{ $subject->class->year }} - {{ $subject->class->semester }}</div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                @if($subject->faculty->user->profile_photo)
                                    <img src="{{ asset($subject->faculty->user->profile_photo) }}" class="rounded-circle mr-2" width="30" height="30" style="object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle text-secondary mr-2 icon-sm"></i>
                                @endif
                                {{ $subject->faculty->user->name ?? 'N/A' }}
                            </div>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center" style="gap:8px;">
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                                    class="btn btn-warning btn-sm">Edit</a>

                                <form method="POST"
                                    action="{{ route('admin.subjects.destroy', $subject->id) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            No subjects found
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $subjects->links() }}
            </div>
        </div>
    </div>

</div>

@endsection