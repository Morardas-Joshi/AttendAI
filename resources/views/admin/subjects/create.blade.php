@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h4 class="m-0 font-weight-bold text-primary">Create Subject</h4>
            <a href="{{ route('admin.subjects') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf

                <div class="form-group mb-4">
                    <label class="form-label fw-bold text-dark">Subject Name</label>
                    <input type="text" name="subject_name"
                           class="form-control"
                           placeholder="e.g. Java Programming"
                           value="{{ old('subject_name') }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Class</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->course_name }} (Year: {{ $class->year }}, Sem: {{ $class->semester }}, Div: {{ $class->division }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Faculty</label>
                        <select name="faculty_id" class="form-control" required>
                            <option value="">-- Select Faculty --</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->user->name }} ({{ $faculty->department }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <div class="mt-2 text-right">
                    <button class="btn btn-primary px-4">
                        <i class="fas fa-plus mr-1"></i> Create Subject
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection