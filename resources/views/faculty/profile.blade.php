@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
    </div>

    <div class="row">

        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset(auth()->user()->profile_photo) }}" alt="Profile Photo"
                                class="rounded-circle shadow" width="120" height="120" style="object-fit: cover; border: 3px solid #4e73df;">
                        @else
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        @endif
                    </div>
                    <h4 class="font-weight-bold">{{ auth()->user()->name }}</h4>
                    <p class="text-muted mb-1">{{ auth()->user()->email }}</p>
                    <span class="badge badge-primary px-3 py-2 mb-3">Faculty</span>

                    @if(session('success'))
                        <div class="alert alert-success small mt-2">{{ session('success') }}</div>
                    @endif

                    <!-- Photo Upload Form -->
                    <hr>
                    <form action="{{ route('faculty.profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small text-muted">Change Profile Photo</label>
                            <input type="file" name="profile_photo" class="form-control form-control-sm" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-camera mr-1"></i> Upload Photo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Details</h6>
                </div>
                <div class="card-body">

                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="200" class="text-gray-600"><i class="fas fa-user mr-2"></i> Full Name</th>
                                <td class="font-weight-bold">{{ auth()->user()->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600"><i class="fas fa-envelope mr-2"></i> Email</th>
                                <td>{{ auth()->user()->email }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600"><i class="fas fa-id-badge mr-2"></i> Employee ID</th>
                                <td>{{ $faculty->employee_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600"><i class="fas fa-building mr-2"></i> Department</th>
                                <td>{{ $faculty->department ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600"><i class="fas fa-calendar mr-2"></i> Joined</th>
                                <td>{{ auth()->user()->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

    <!-- Change Password Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-lock mr-2"></i> Change Password</h6>
                </div>
                <div class="card-body">

                    <div id="passwordAlert" class="d-none"></div>

                    <form id="changePasswordForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Current Password</label>
                                <input type="password" name="current_password" id="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">New Password</label>
                                <input type="password" name="password" id="new_password" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="confirm_password" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Update Password
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const alertDiv = document.getElementById('passwordAlert');
        const currentPw = document.getElementById('current_password').value;
        const newPw = document.getElementById('new_password').value;
        const confirmPw = document.getElementById('confirm_password').value;

        if (newPw !== confirmPw) {
            alertDiv.className = 'alert alert-danger';
            alertDiv.textContent = 'New passwords do not match!';
            return;
        }

        if (newPw.length < 8) {
            alertDiv.className = 'alert alert-danger';
            alertDiv.textContent = 'Password must be at least 8 characters!';
            return;
        }

        fetch('{{ route("password.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                current_password: currentPw,
                password: newPw,
                password_confirmation: confirmPw
            })
        })
        .then(response => {
            if (response.ok) {
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = '✅ Password updated successfully!';
                document.getElementById('changePasswordForm').reset();
            } else {
                return response.json().then(data => {
                    throw new Error(data.message || 'Current password is incorrect.');
                });
            }
        })
        .catch(err => {
            alertDiv.className = 'alert alert-danger';
            alertDiv.textContent = '❌ ' + err.message;
        });
    });
});
</script>

@endsection
