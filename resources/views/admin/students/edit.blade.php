@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4>Edit Student</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Full Name</label>
                    <input type="text" name="name" value="{{ $student->user->name }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Email Address</label>
                    <input type="email" name="email" value="{{ $student->user->email }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Roll No (Student ID)</label>
                    <input type="text" name="roll_no" value="{{ $student->roll_no }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Class</label>
                    <select name="class_id" class="form-control" required>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $student->class_id == $class->id ? 'selected' : '' }}>
                                {{ $class->course_name }} ({{ $class->division }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold font-weight-bold">Profile Photo</label>
                    @if($student->user->profile_photo)
                        <div class="mb-2">
                            <img src="{{ asset($student->user->profile_photo) }}" class="rounded-circle" width="60" height="60" style="object-fit: cover;">
                            <small class="text-muted ml-2">Current photo</small>
                        </div>
                    @endif
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                </div>

                <button class="btn btn-primary px-4">
                    <i class="fas fa-save mr-1"></i> Update Student
                </button>

            </form>

        </div>
    </div>

</div>

@endsection