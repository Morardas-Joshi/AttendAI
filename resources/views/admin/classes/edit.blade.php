@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit Class</h3>

        <a href="{{ route('admin.classes') }}" class="btn btn-secondary btn-sm">
            Back
        </a>
    </div>

    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Course -->
                <div class="form-group mb-3">
                    <label>Course Name</label>
                    <input type="text"
                           name="course_name"
                           value="{{ $class->course_name }}"
                           class="form-control">
                </div>

                <!-- Year -->
                <div class="form-group mb-3">
                    <label>Year</label>
                    <select name="year" class="form-control">
                        <option value="1" {{ $class->year == 1 ? 'selected' : '' }}>1</option>
                        <option value="2" {{ $class->year == 2 ? 'selected' : '' }}>2</option>
                    </select>
                </div>

                <!-- Semester -->
                <div class="form-group mb-3">
                    <label>Semester</label>
                    <select name="semester" class="form-control">
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}"
                                {{ $class->semester == $i ? 'selected' : '' }}>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Division -->
                <div class="form-group mb-3">
                    <label>Division</label>
                    <select name="division" class="form-control">
                        @foreach(['A','B','C','D','E'] as $div)
                            <option value="{{ $div }}"
                                {{ $class->division == $div ? 'selected' : '' }}>
                                {{ $div }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Button -->
                <button class="btn btn-primary">
                    Update Class
                </button>

            </form>

        </div>
    </div>

</div>

@endsection