@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h4 class="m-0 font-weight-bold text-primary">Edit Subject</h4>
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

            <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-4">
                    <label class="form-label fw-bold text-dark">Subject Name</label>
                    <input type="text" name="subject_name"
                           class="form-control"
                           value="{{ old('subject_name', $subject->subject_name) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Class</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $subject->class_id == $class->id ? 'selected' : '' }}>
                                    {{ $class->course_name }} (Year: {{ $class->year }}, Sem: {{ $class->semester }}, Div: {{ $class->division }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Faculty</label>
                        <select name="faculty_id" class="form-control" required>
                            <option value="">-- Select Faculty --</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ $subject->faculty_id == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->user->name }} ({{ $faculty->department }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <div class="mt-2 text-right">
                    <button class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Update Subject
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
