@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4>Add Student</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Roll No (Student ID)</label>
                    <input type="text" name="roll_no" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold font-weight-bold">Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>

                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->course_name }} ({{ $class->division }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold font-weight-bold">Profile Photo</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                </div>

                <button class="btn btn-success px-4">
                    <i class="fas fa-save mr-1"></i> Save Student
                </button>

            </form>

        </div>
    </div>

</div>

@endsection