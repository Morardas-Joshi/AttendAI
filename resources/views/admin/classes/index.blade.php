@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Classes</h3>
            <small class="text-muted">Manage all academic classes</small>
        </div>

        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
            Add Class
        </a>
    </div>
    <div class="card mb-3">
        <div class="card-body">

            <form method="GET" class="row">

                <div class="col-md-4">
                    <input type="text" name="search"
                        class="form-control"
                        placeholder="Search course name..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-4">
                    <select name="year" class="form-control">
                        <option value="">All Years</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.classes') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>

        </div>
    </div>
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Division</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($classes as $class)
                    <tr>
                        <td>{{ $class->id }}</td>

                        <td class="font-weight-bold">
                            {{ $class->course_name }}
                        </td>

                        <td>Year {{ $class->year }}</td>

                        <td>Sem {{ $class->semester }}</td>

                        <td>
                            <span class="badge badge-info">
                                {{ $class->division }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex align-items-center" style="gap: 8px;">

                                <a href="{{ route('admin.classes.edit', $class->id) }}"
                                    class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.classes.destroy', $class->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this class?')">
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
                        <td colspan="6" class="text-center">
                            No classes created yet
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $classes->links() }}
            </div>
        </div>
    </div>

</div>

@endsection