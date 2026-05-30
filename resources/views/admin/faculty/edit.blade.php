@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Edit Faculty</h4>
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

            <form action="{{ route('admin.faculty.update', $faculty->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Faculty Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $faculty->user->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ $faculty->user->email }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password (optional)">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" value="{{ $faculty->employee_id }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Department</label>
                    <input type="text" name="department" class="form-control" value="{{ $faculty->department }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Profile Photo</label>
                    @if($faculty->user->profile_photo)
                        <div class="mb-2">
                            <img src="{{ asset($faculty->user->profile_photo) }}" alt="Current Photo" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                            <small class="text-muted ml-2">Current photo</small>
                        </div>
                    @endif
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                    <small class="text-muted">Leave empty to keep current photo</small>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Update Faculty
                </button>

            </form>

        </div>
    </div>

</div>

@endsection