<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    ClassController,
    SubjectController,
    FacultyController,
    StudentController,
    AttendanceSessionController,
    AttendanceRecordController,
    ReportController,
    AdminController
};

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect (ROLE BASED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'faculty' => redirect()->route('faculty.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        default => redirect('/login'),
    };
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ✅ FIXED: Dashboard (use controller)
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Classes
        Route::get('/classes', [ClassController::class, 'index'])->name('classes');
        Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{id}/edit', [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{id}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{id}', [ClassController::class, 'destroy'])->name('classes.destroy');

        // Subjects
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects'); // ✅ THIS WAS MISSING
        Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{id}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('subjects.destroy');


        // Faculty
        Route::get('/faculty', [FacultyController::class, 'index'])->name('faculty');
        Route::get('/faculty/create', [FacultyController::class, 'create'])->name('faculty.create');
        Route::post('/faculty', [FacultyController::class, 'store'])->name('faculty.store');
        Route::get('/faculty/{id}/edit', [FacultyController::class, 'edit'])->name('faculty.edit');
        Route::put('/faculty/{id}', [FacultyController::class, 'update'])->name('faculty.update');
        Route::delete('/faculty/{id}', [FacultyController::class, 'destroy'])->name('faculty.destroy');

        // Students
        Route::get('/students', [StudentController::class, 'index'])->name('students');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/students/{id}/register-face', [StudentController::class, 'adminRegisterFaceForm'])->name('students.face.register');
        Route::post('/students/{id}/register-face', [StudentController::class, 'adminRegisterFace'])->name('students.face.register.store');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/students', [ReportController::class, 'studentSummary'])->name('reports.students');
        Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
    });

/*
|--------------------------------------------------------------------------
| FACULTY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:faculty'])
    ->prefix('faculty')
    ->name('faculty.')
    ->group(function () {

        // Dashboard
        Route::get('/', fn() => view('faculty.dashboard'))->name('dashboard');

        Route::get('/sessions', [AttendanceSessionController::class, 'index'])->name('sessions');
        Route::get('/sessions/create', [AttendanceSessionController::class, 'create'])->name('sessions.create');
        Route::post('/sessions', [AttendanceSessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{id}', [AttendanceSessionController::class, 'show'])->name('sessions.show');
        Route::patch('/sessions/{id}/end', [AttendanceSessionController::class, 'end'])->name('sessions.end');
        Route::get('/sessions/{id}/attendees', [AttendanceSessionController::class, 'attendees'])->name('sessions.attendees');

        // Profile
        Route::get('/profile', function () {
            $faculty = \App\Models\Faculty::where('user_id', auth()->id())->first();
            return view('faculty.profile', compact('faculty'));
        })->name('profile');

        // Update Profile Photo
        Route::post('/profile/photo', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $user = auth()->user();
            $photo = $request->file('profile_photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/profiles'), $filename);
            $user->profile_photo = 'uploads/profiles/' . $filename;
            $user->save();

            return back()->with('success', 'Profile photo updated successfully!');
        })->name('profile.photo.update');
    });


/*
|--------------------------------------------------------------------------
| STUDENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // Dashboard
        Route::get('/', fn() => view('student.dashboard'))->name('dashboard');

        Route::get('/attendance', [StudentController::class, 'attendanceCamera'])->name('attendance.page');

        Route::post('/attendance/mark', [AttendanceRecordController::class, 'mark'])->name('attendance.mark');
        Route::get('/join', [StudentController::class, 'joinForm'])->name('join');
        Route::post('/join', [StudentController::class, 'joinSession'])->name('join.store')->middleware('throttle:5,1');
        Route::get('/face/register', [StudentController::class, 'registerFaceForm'])->name('face.register');
        Route::post('/face/register', [StudentController::class, 'registerFace'])->name('face.register.store');
        Route::get('/my-attendance', [StudentController::class, 'myAttendance'])->name('my_attendance');

        // Reports & Profile
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::get('/performance', [StudentController::class, 'performance'])->name('performance');
    });


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
