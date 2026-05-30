@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Faculty</h3>

        <a href="{{ route('admin.faculty.create') }}" class="btn btn-primary">
            Add Faculty
        </a>
    </div>
    <div class="card mb-3">
        <div class="card-body">

            <form method="GET" class="row">

                <div class="col-md-4">
                    <input type="text" name="search"
                        class="form-control"
                        placeholder="Search name or employee ID..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-4">
                    <select name="department" class="form-control">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}"
                            {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.faculty') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>

        </div>
    </div>
    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="80">Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($faculties as $f)
                    <tr>
                        <td class="text-center">
                            @if($f->profile_photo)
                                <img src="{{ asset($f->profile_photo) }}" class="rounded-circle shadow-sm" width="50" height="50" style="object-fit: cover; border: 2px solid #ddd;">
                            @else
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border: 2px solid #eee;">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                            @endif
                        </td>
                        <td class="font-weight-bold text-primary">{{ $f->name ?? $f->user->name }}</td>
                        <td>{{ $f->user->email }}</td>
                        <td class="text-center"><span class="badge badge-light border">{{ $f->employee_id }}</span></td>
                        <td>{{ $f->department }}</td>

                        <td>
                            <div class="d-flex justify-content-center" style="gap: 8px;">

                                <a href="{{ route('admin.faculty.edit', $f->id) }}"
                                    class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                    action="{{ route('admin.faculty.destroy', $f->id) }}"
                                    onsubmit="return confirm('Are you sure?')">
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
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-info-circle text-info mb-2 fa-2x"></i><br>
                            No Faculty found
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $faculties->links() }}
            </div>
        </div>
    </div>

</div>

@endsection