<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // ✅ LIST
    public function index(Request $request)
    {
        $query = Student::with(['user', 'class']);

        // 🔍 Search
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            })
                ->orWhere('roll_no', 'like', "%{$request->search}%");
        }

        // 🎯 Filter by Class
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $classes = \App\Models\ClassModel::all();

        return view('admin.students.index', compact('students', 'classes'));
    }

    // ✅ CREATE FORM
    public function create()
    {
        $classes = ClassModel::all();
        return view('admin.students.create', compact('classes'));
    }

    // ✅ STORE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'roll_no' => 'required',
            'class_id' => 'required|exists:classes,id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle profile photo upload
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/profiles'), $filename);
            $photoPath = 'uploads/profiles/' . $filename;
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'student_id' => $request->roll_no,
            'profile_photo' => $photoPath
        ]);

        // Create Student
        Student::create([
            'user_id' => $user->id,
            'roll_no' => $request->roll_no,
            'class_id' => $request->class_id
        ]);

        return redirect()->route('admin.students')
            ->with('success', 'Student Created Successfully');
    }

    // ✅ EDIT FORM
    public function edit($id)
    {
        $student = Student::with('user')->findOrFail($id);
        $classes = ClassModel::all();

        return view('admin.students.edit', compact('student', 'classes'));
    }

    // ✅ UPDATE
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $student->user->id,
            'roll_no' => 'required',
            'class_id' => 'required|exists:classes,id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Update User Data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->roll_no
        ];

        // Handle profile photo update
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/profiles'), $filename);
            $userData['profile_photo'] = 'uploads/profiles/' . $filename;
        }

        $student->user->update($userData);

        // Update Student
        $student->update([
            'roll_no' => $request->roll_no,
            'class_id' => $request->class_id
        ]);

        return redirect()->route('admin.students')
            ->with('success', 'Student Updated Successfully');
    }

    // ✅ DELETE
    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        $student->user()->delete();
        $student->delete();

        return back()->with('success', 'Student Deleted Successfully');
    }

    public function joinForm()
    {
        return view('student.join');
    }

    public function joinSession(Request $request)
    {
        $request->validate([
            'session_code' => 'required|string|max:6'
        ]);

        $session = \App\Models\AttendanceSession::where('session_code', strtoupper($request->session_code))
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return back()->with('error', 'Invalid or expired session code.');
        }

        session(['active_session_id' => $session->id]);

        return redirect()->route('student.attendance.page')->with('success', 'Joined session successfully. Please mark your attendance.');
    }

    public function attendanceCamera()
    {
        $sessionId = session('active_session_id');
        if (!$sessionId) {
            return redirect()->route('student.join.store')->with('error', 'Please join a session to mark attendance.');
        }

        $session = \App\Models\AttendanceSession::find($sessionId);
        if (!$session || $session->status !== 'active') {
            return redirect()->route('student.join.store')->with('error', 'Session is invalid or closed.');
        }

        return view('student.attendance', compact('session'));
    }

    public function registerFaceForm()
    {
        return view('student.register_face');
    }

    public function registerFace(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png|max:5120'
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::attach(
                'file',
                file_get_contents($request->file('image')),
                'face.jpg'
            )->post(env('AI_SERVICE_URL', 'http://127.0.0.1:8001') . '/register_face', [
                'student_id' => auth()->user()->id
            ]);

            $result = $response->json();

            if (isset($result['success']) && $result['success']) {
                $user = auth()->user();
                $user->face_registered = 1;

                // ✅ Save the captured image as profile photo
                $photo = $request->file('image');
                $filename = 'face_' . time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('uploads/profiles'), $filename);
                $user->profile_photo = 'uploads/profiles/' . $filename;
                
                $user->save();

                // ✅ Get the correct student record (not user_id)
                $student = \App\Models\Student::where('user_id', $user->id)->first();
                if (!$student) {
                    return response()->json(['success' => false, 'message' => 'Student record not found.']);
                }

                // ✅ Store in DB
                \App\Models\FaceEncoding::updateOrCreate(
                    ['student_id' => $student->id],
                    ['encoding_data' => json_encode($result['encoding'])]
                );

                return response()->json(['success' => true, 'message' => 'Face registered successfully!']);
            }

            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Face not detected or invalid']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Recognition service temporarily unavailable']);
        }
    }

    public function adminRegisterFaceForm($id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('admin.students.register_face', compact('student'));
    }

    public function adminRegisterFace(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png|max:5120'
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::attach(
                'file',
                file_get_contents($request->file('image')),
                'face.jpg'
            )->post(env('AI_SERVICE_URL', 'http://127.0.0.1:8001') . '/register_face', [
                'student_id' => $student->user->id
            ]);

            $result = $response->json();

            if (isset($result['success']) && $result['success']) {
                $user = $student->user;
                $user->face_registered = 1;

                // ✅ Save the captured image as profile photo
                $photo = $request->file('image');
                $filename = 'face_' . time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('uploads/profiles'), $filename);
                $user->profile_photo = 'uploads/profiles/' . $filename;

                $user->save();

                // ✅ Store in DB
                \App\Models\FaceEncoding::updateOrCreate(
                    ['student_id' => $student->id],
                    ['encoding_data' => json_encode($result['encoding'])]
                );

                return response()->json(['success' => true, 'message' => 'Student face registered successfully!']);
            }

            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Face not detected or invalid']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Recognition service temporarily unavailable']);
        }
    }

    public function myAttendance()
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        // Detailed History
        $records = \App\Models\AttendanceRecord::where('student_id', $student->id)
            ->with(['session.subject', 'session.faculty.user'])
            ->latest('marked_at')
            ->paginate(15);

        // Subject-wise Summary
        $subjectSummary = \App\Models\AttendanceRecord::where('student_id', $student->id)
            ->select('session_id')
            ->with('session.subject')
            ->get()
            ->groupBy(function($record) {
                return $record->session->subject->subject_name ?? 'Unknown';
            })
            ->map(function($records) {
                return [
                    'present' => $records->where('status', 'present')->count(),
                    'total' => $records->count(),
                    'percentage' => $records->count() > 0 ? round(($records->where('status', 'present')->count() / $records->count()) * 100, 1) : 0
                ];
            });

        return view('student.my_attendance', compact('records', 'subjectSummary'));
    }

    public function profile()
    {
        $student = Student::where('user_id', auth()->id())->first();
        return view('student.profile', compact('student'));
    }

    public function performance()
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        // Basic stats
        $totalSessions = \App\Models\AttendanceRecord::where('student_id', $student->id)->count();
        $present = \App\Models\AttendanceRecord::where('student_id', $student->id)->where('status', 'present')->count();
        $absent = 0; // Usually calculated against total course sessions, let's just show present for now
        
        $percentage = $totalSessions > 0 ? round(($present / $totalSessions) * 100, 1) : 0;

        return view('student.performance', compact('student', 'totalSessions', 'present', 'percentage'));
    }
}
