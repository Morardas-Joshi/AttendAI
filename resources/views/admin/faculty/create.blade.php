@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Add Faculty</h4>
            <a href="{{ route('admin.faculty') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
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

            <form action="{{ route('admin.faculty.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Faculty Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" placeholder="e.g. EMP001" value="{{ old('employee_id') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="e.g. Computer Science" value="{{ old('department') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Profile Photo <small class="text-muted">(optional)</small></label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                </div>

                <button class="btn btn-success">
                    <i class="fas fa-plus mr-1"></i> Create Faculty
                </button>

            </form>

        </div>
    </div>

</div>

@endsection