<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{

    public function index(Request $request)
    {
        $query = ClassModel::query();

        // 🔍 Search
        if ($request->search) {
            $query->where('course_name', 'like', "%{$request->search}%");
        }

        // 🎯 Filter Year
        if ($request->year) {
            $query->where('year', $request->year);
        }

        $classes = $query->latest()->paginate(10)->withQueryString();

        return view('admin.classes.index', compact('classes'));
    }


    public function create()
    {
        return view('admin.classes.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required',
            'year' => 'required',
            'semester' => 'required',
            'division' => 'required'
        ]);

        ClassModel::create([
            'course_name' => $request->course_name,
            'year' => $request->year,
            'semester' => $request->semester,
            'division' => $request->division
        ]);

        return redirect('/admin/classes')
            ->with('success', 'Class created successfully');
    }

    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);

        return view('admin.classes.edit', compact('class'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'course_name' => 'required',
            'year' => 'required',
            'semester' => 'required',
            'division' => 'required'
        ]);

        $class = ClassModel::findOrFail($id);

        $class->update([
            'course_name' => $request->course_name,
            'year' => $request->year,
            'semester' => $request->semester,
            'division' => $request->division
        ]);

        return redirect('/admin/classes')->with('success', 'Class updated successfully');
    }


    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);

        $class->delete();

        return redirect('/admin/classes')->with('success', 'Class deleted successfully');
    }
}
