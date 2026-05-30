@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Create Class</h3>
            <small class="text-muted">Add a new academic class</small>
        </div>

        <a href="{{ route('admin.classes') }}" class="btn btn-secondary btn-sm">
            Back
        </a>
    </div>

    <!-- Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.classes.store') }}" method="POST">
                @csrf

                <!-- Course -->
                <div class="form-group mb-3">
                    <label>Course Name</label>
                    <input type="text"
                           name="course_name"
                           value="{{ old('course_name') }}"
                           class="form-control"
                           placeholder="e.g. MCA, BCA">
                </div>

                <!-- Year -->
                <div class="form-group mb-3">
                    <label>Year</label>
                    <select name="year" class="form-control">
                        <option value="">Select Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                    </select>
                </div>

                <!-- Semester -->
                <div class="form-group mb-3">
                    <label>Semester</label>
                    <select name="semester" class="form-control">
                        <option value="">Select Semester</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Division -->
                <div class="form-group mb-3">
                    <label>Division</label>
                    <select name="division" class="form-control">
                        <option value="">Select Division</option>
                        @foreach(['A','B','C','D','E'] as $div)
                            <option value="{{ $div }}">
                                Division {{ $div }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Button -->
                <button class="btn btn-primary">
                    Create Class
                </button>

            </form>

        </div>
    </div>

</div>

@endsection