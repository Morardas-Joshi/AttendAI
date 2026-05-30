<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassModel;
use App\Models\Subject;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendanceRecord::with([
            'student.user',
            'student.class',
            'session.subject'
        ]);

        // 🔍 Filters
        if ($request->class_id) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->subject_id) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        if ($request->date) {
            $query->whereDate('marked_at', $request->date);
        }

        $records = $query->latest()->paginate(5)->withQueryString();

        // 📊 Summary
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();

        $classes = ClassModel::all();
        $subjects = Subject::all();

        return view('admin.reports.index', compact(
            'records',
            'classes',
            'subjects',
            'total',
            'present',
            'absent'
        ));
    }

    public function exportCsv(Request $request)
    {
        $query = AttendanceRecord::with([
            'student.user',
            'student.class',
            'session.subject'
        ]);

        if ($request->class_id) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->subject_id) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        if ($request->date) {
            $query->whereDate('marked_at', $request->date);
        }

        $records = $query->latest()->get();

        $filename = "attendance_report_" . date('Y_m_d_H_i_s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Name', 'Roll No', 'Class', 'Subject', 'Date', 'Time', 'Status', 'Liveness', 'Confidence'];

        $callback = function() use($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $record) {
                $row['Name']  = $record->student->user->name ?? 'N/A';
                $row['Roll No']    = $record->student->roll_no ?? 'N/A';
                $row['Class']    = ($record->student->class->course_name ?? 'N/A') . ' - ' . ($record->student->class->division ?? '');
                $row['Subject']  = $record->session->subject->subject_name ?? 'N/A';
                $row['Date']  = \Carbon\Carbon::parse($record->marked_at)->format('Y-m-d');
                $row['Time']  = \Carbon\Carbon::parse($record->marked_at)->format('h:i A');
                $row['Status']  = $record->status;
                $row['Liveness']  = $record->liveness_passed ? 'Yes' : 'No';
                $row['Confidence']  = $record->confidence_score . '%';

                fputcsv($file, array($row['Name'], $row['Roll No'], $row['Class'], $row['Subject'], $row['Date'], $row['Time'], $row['Status'], $row['Liveness'], $row['Confidence']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function studentSummary(Request $request)
    {
        $query = \App\Models\Student::with(['user', 'class']);

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->search) {
            $query->whereHas('user', function($q) use($request) {
                $q->where('name', 'like', "%{$request->search}%");
            })->orWhere('roll_no', 'like', "%{$request->search}%");
        }

        $students = $query->paginate(15)->withQueryString();

        // Attach stats to each student
        $students->getCollection()->transform(function($student) {
            // Total sessions conducted for this student's class
            $classSessions = AttendanceSession::whereHas('subject', function($q) use($student) {
                $q->where('class_id', $student->class_id);
            })->count();

            $present = AttendanceRecord::where('student_id', $student->id)->where('status', 'present')->count();
            $rejected = AttendanceRecord::where('student_id', $student->id)->where('status', 'rejected')->count();
            
            $student->total_sessions = $classSessions;
            $student->present_count = $present;
            $student->absent_count = max(0, $classSessions - $present);
            $student->attendance_percentage = $classSessions > 0 ? round(($present / $classSessions) * 100) : 0;
            
            return $student;
        });

        $classes = ClassModel::all();

        return view('admin.reports.students', compact('students', 'classes'));
    }
}