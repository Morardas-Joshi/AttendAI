<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\Faculty;

class SubjectController extends Controller
{
    // List Subjects
    public function index(Request $request)
    {
        $query = Subject::with(['class', 'faculty.user']);

        // 🔍 Search
        if ($request->search) {
            $query->where('subject_name', 'like', "%{$request->search}%");
        }

        // 🎯 Filter Class
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        $subjects = $query->latest()->paginate(10)->withQueryString();
        $classes = ClassModel::all();

        return view('admin.subjects.index', compact('subjects', 'classes'));
    }

    // Create Form
    public function create()
    {
        $classes = ClassModel::all();
        $faculties = Faculty::all();

        return view('admin.subjects.create', compact('classes', 'faculties'));
    }

    // Store Subject
    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => 'required',
            'class_id' => 'required|exists:classes,id',
            'faculty_id' => 'required|exists:faculty,id'
        ]);

        Subject::create([
            'subject_name' => $request->subject_name,
            'class_id' => $request->class_id,
            'faculty_id' => $request->faculty_id
        ]);

        return redirect('/admin/subjects')->with('success', 'Subject Created Successfully');
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        $classes = ClassModel::all();
        $faculties = Faculty::with('user')->get();

        return view('admin.subjects.edit', compact('subject', 'classes', 'faculties'));
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'subject_name' => 'required',
            'class_id' => 'required|exists:classes,id',
            'faculty_id' => 'required|exists:faculty,id'
        ]);

        $subject->update([
            'subject_name' => $request->subject_name,
            'class_id' => $request->class_id,
            'faculty_id' => $request->faculty_id
        ]);

        return redirect()->route('admin.subjects')->with('success', 'Updated Successfully');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return back()->with('success', 'Deleted');
    }   
}
