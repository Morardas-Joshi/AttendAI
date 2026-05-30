<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;

class AttendanceRecordController extends Controller
{
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    public function mark(Request $request)
    {
        try {
            // 1. Check Session
            $sessionId = session('active_session_id');
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ No active session. Please join a session first.'
                ]);
            }

            $session = AttendanceSession::find($sessionId);
            if (!$session || $session->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Session is invalid or already closed.'
                ]);
            }

            $user = auth()->user();
            if (!$user->face_registered) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ You must register your face first before marking attendance.'
                ]);
            }

            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if (!$student) {
                return response()->json(['success' => false, 'message' => '❌ Logged in user is not a student.']);
            }

            // Duplicate check moved to after face verification so 'Face Mismatch' errors can properly show when scanning friends' faces
            // 3. Location Check (Server Side)
            if ($session->classroom_lat && $session->classroom_lng) {
                if (!$request->lat || !$request->lon) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ GPS location missing.'
                    ]);
                }

                $distance = $this->calculateDistance($request->lat, $request->lon, $session->classroom_lat, $session->classroom_lng);
                
                if ($distance > $session->radius_meters) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ You are too far from the classroom (' . round($distance) . 'm away).'
                    ]);
                }
            } else {
                $distance = null;
            }

            // 4. Send to FastAPI for face recognition & liveness
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png|max:5120'
            ]);

            $response = Http::timeout(10)->attach(
                'file',
                file_get_contents($request->file('file')),
                'image.jpg'
            )->post(env('AI_SERVICE_URL', 'http://127.0.0.1:8001') . '/recognize_face');

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Recognition service temporarily unavailable'
                ]);
            }

            $result = $response->json();

            if (!isset($result['success']) || !$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ ' . ($result['message'] ?? 'Face not recognized')
                ]);
            }

            // 5. Match logged-in user
            if ($result['student_id'] != auth()->user()->id) {
                // Find who it matched
                $matchedUser = \App\Models\User::find($result['student_id']);
                $matchedName = $matchedUser ? $matchedUser->name : "Unknown User";

                return response()->json([
                    'success' => false,
                    'message' => "❌ Face Mismatch! Matched: $matchedName. Logged in as: " . auth()->user()->name,
                    'face_location' => $result['face_location'] ?? null,
                    'image_dims' => $result['image_dims'] ?? null,
                ]);
            }

            // 6. Check for duplicate attendance AFTER face was verified successfully
            $exists = AttendanceRecord::where('student_id', $student->id)
                ->where('session_id', $sessionId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ You have already successfully marked your attendance for this session.'
                ]);
            }

            // 6. Liveness check
            $livenessPassed = $result['liveness']['passed'] ?? true;
            $status = 'present';
            $reason = null;

            if (!$livenessPassed) {
                $status = 'rejected';
                $ear = $result['liveness']['ear'] ?? 'N/A';
                $reason = "Liveness check failed (Score: $ear). Ensure your eyes are open and clearly visible.";
            }

            // 7. Save attendance
            AttendanceRecord::create([
                'student_id' => $student->id,
                'session_id' => $sessionId,
                'status' => $status,
                'rejection_reason' => $reason,
                'confidence_score' => $result['confidence'] ?? 0,
                'ip_address' => $request->ip(),
                'marked_at' => now(),
                'student_lat' => $request->lat,
                'student_lng' => $request->lon,
                'distance_meters' => $distance,
                'liveness_passed' => $livenessPassed,
            ]);

            if ($status === 'rejected') {
                 return response()->json([
                    'success' => false,
                    'message' => '❌ ' . $reason,
                    'face_location' => $result['face_location'] ?? null,
                    'image_dims' => $result['image_dims'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Attendance Marked Successfully!',
                'student_name' => $student->user->name,
                'student_id' => $student->roll_no,
                'face_location' => $result['face_location'] ?? null,
                'image_dims' => $result['image_dims'] ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backend Error: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ]);
        }
    }
}