<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\Subject;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\AttendanceRecord;
use Illuminate\Support\Str;

class AttendanceSessionController extends Controller
{
    // Show sessions
    public function index()
    {
        $faculty = Faculty::where('user_id', auth()->id())->first();
        if (!$faculty) abort(403, 'Unauthorized');

        $sessions = AttendanceSession::where('faculty_id', $faculty->id)
            ->with('subject')
            ->withCount('records') // automatically counts students who marked attendance
            ->latest()
            ->paginate(10);
            
        return view('faculty.sessions.index', compact('sessions'));
    }

    // Create session form
    public function create()
    {
        // Find all faculty IDs belonging to this user (handling potential duplicates)
        $facultyIds = Faculty::where('user_id', auth()->id())->pluck('id');
        
        // Get all subjects assigned to any of those faculty records
        $subjects = Subject::whereIn('faculty_id', $facultyIds)->with('class')->get();
        
        return view('faculty.sessions.create', compact('subjects'));
    }

    // Store session
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'radius_meters' => 'nullable|integer|min:10|max:100',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
        ]);

        $faculty = Faculty::where('user_id', auth()->id())->first();
        
        // Safety check to ensure subject actually belongs to this faculty/user
        $facultyIds = Faculty::where('user_id', auth()->id())->pluck('id');
        $subject = Subject::where('id', $request->subject_id)->whereIn('faculty_id', $facultyIds)->first();
        
        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not assigned to this subject.']);
        }

        $sessionCode = strtoupper(Str::random(6));

        AttendanceSession::create([
            'subject_id' => $request->subject_id,
            'faculty_id' => $subject->faculty_id, // Use the actual faculty_id assigned to this subject
            'start_time' => now(),
            'end_time' => now()->addMinutes(60), // Arbitrary auto-close buffer
            'session_code' => $sessionCode,
            'status' => 'active',
            'classroom_lat' => $request->lat,
            'classroom_lng' => $request->lon,
            'radius_meters' => $request->radius_meters ?? 30,
            'allowed_ip_range' => '*', // Kept for backwards DB compatibility if not removed
        ]);

        return redirect()->route('faculty.sessions')->with('success', 'Session started successfully');
    }

    // Show session (Live View)
    public function show($id)
    {
        $session = AttendanceSession::with(['subject.class', 'records.student.user'])->findOrFail($id);
        
        // Ensure this faculty owns it
        $faculty = Faculty::where('user_id', auth()->id())->first();
        if($session->faculty_id != $faculty->id) abort(403);

        return view('faculty.sessions.show', compact('session'));
    }

    // End session
    public function end($id)
    {
        $session = AttendanceSession::findOrFail($id);
        
        $faculty = Faculty::where('user_id', auth()->id())->first();
        if($session->faculty_id != $faculty->id) abort(403);

        $session->update([
            'status' => 'closed',
            'end_time' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Session closed.']);
    }

    // Get live attendees list
    public function attendees($id)
    {
        $session = AttendanceSession::findOrFail($id);
        
        $records = AttendanceRecord::where('session_id', $id)
            ->with(['student.user'])
            ->latest()
            ->get()
            ->map(function($record) {
                return [
                    'id' => $record->id,
                    'name' => $record->student->user->name ?? 'Unknown',
                    'roll_no' => $record->student->roll_no ?? '-',
                    'liveness_passed' => $record->liveness_passed,
                    'status' => $record->status,
                    'time' => $record->marked_at,
                    'distance' => round($record->distance_meters) . 'm'
                ];
            });

        return response()->json(['success' => true, 'attendees' => $records]);
    }
}
