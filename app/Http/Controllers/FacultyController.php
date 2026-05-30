<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FacultyController extends Controller
{
    // List
    public function index(Request $request)
    {
        $query = Faculty::with('user');

        // 🔍 Search
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            })
                ->orWhere('employee_id', 'like', "%{$request->search}%");
        }

        // 🎯 Filter by Department
        if ($request->department) {
            $query->where('department', $request->department);
        }

        $faculties = $query->latest()->paginate(10)->withQueryString();

        // Get unique departments
        $departments = Faculty::select('department')->distinct()->pluck('department');

        return view('admin.faculty.index', compact('faculties', 'departments'));
    }

    // Create Form
    public function create()
    {
        return view('admin.faculty.create');
    }

    // Store - Creates both User and Faculty in one step
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'employee_id' => 'required|unique:faculty,employee_id',
            'department' => 'required|string',
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

        // Create User with 'faculty' role
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'faculty',
            'profile_photo' => $photoPath,
            'faculty_id' => $request->employee_id, // Sync with users table
        ]);

        // Create Faculty record linked to the user
        Faculty::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'profile_photo' => $photoPath
        ]);

        return redirect('/admin/faculty')->with('success', 'Faculty Created Successfully');
    }

    // Edit Form
    public function edit($id)
    {
        $faculty = Faculty::with('user')->findOrFail($id);
        return view('admin.faculty.edit', compact('faculty'));
    }

    // Update - Updates both User and Faculty
    public function update(Request $request, $id)
    {
        $faculty = Faculty::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $faculty->user->id,
            'password' => 'nullable|min:6',
            'employee_id' => 'required|unique:faculty,employee_id,' . $faculty->id,
            'department' => 'required|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Update User
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'faculty_id' => $request->employee_id, // Sync with users table
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/profiles'), $filename);
            $userData['profile_photo'] = 'uploads/profiles/' . $filename;
        }

        $faculty->user->update($userData);

        // Update Faculty
        $facultyData = [
            'name' => $request->name,
            'employee_id' => $request->employee_id,
            'department' => $request->department
        ];

        if (isset($userData['profile_photo'])) {
            $facultyData['profile_photo'] = $userData['profile_photo'];
        }

        $faculty->update($facultyData);

        return redirect('/admin/faculty')->with('success', 'Faculty Updated Successfully');
    }

    // Delete - Deletes both User and Faculty
    public function destroy($id)
    {
        $faculty = Faculty::findOrFail($id);
        $faculty->user()->delete(); // Delete the user account too
        $faculty->delete();

        return back()->with('success', 'Faculty Deleted Successfully');
    }
}
