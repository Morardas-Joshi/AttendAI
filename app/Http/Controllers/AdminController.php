<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\ClassModel;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = Student::count();
        $totalFaculty = Faculty::count();
        $totalClasses = ClassModel::count();

        $activeSessions = \App\Models\AttendanceSession::where('status', 'active')->count();

        // Today's attendance percentage
        $todaySessions = \App\Models\AttendanceSession::whereDate('created_at', now()->toDateString())->pluck('id');
        $totalExpected = Student::whereIn('class_id', \App\Models\Subject::whereIn('id', \App\Models\AttendanceSession::whereIn('id', $todaySessions)->pluck('subject_id'))->pluck('class_id'))->count();
        $todayPresent = \App\Models\AttendanceRecord::whereIn('session_id', $todaySessions)->where('status', 'present')->count();
        $attendancePercent = $totalExpected > 0 ? round(($todayPresent / $totalExpected) * 100) : 0;

        // Ping AI Service
        $aiStatus = 'offline';
        $aiData = [];
        try {
            $baseUrl = trim(env('AI_SERVICE_URL', 'http://127.0.0.1:8001'), '/');
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($baseUrl . '/encodings_status');
            
            if ($response->ok()) {
                $aiStatus = 'online';
                $aiData = $response->json();
            }
        } catch (\Exception $e) {
            // Log for debugging if needed: \Illuminate\Support\Facades\Log::error("AI Service Ping failed: " . $e->getMessage());
        }


        return view('admin.dashboard', compact(
            'totalStudents',
            'totalFaculty',
            'totalClasses',
            'activeSessions',
            'attendancePercent',
            'aiStatus',
            'aiData'
        ));
    }
}