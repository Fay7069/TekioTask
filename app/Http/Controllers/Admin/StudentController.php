<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // ── Admin dashboard ───────────────────────────────────────
    public function adminDashboard()
    {
        $students = User::where('role', 'Student')->orderBy('name')->get();

        $today    = now()->toDateString();
        $checkins = DB::table('attendance')
                      ->where('attendance_date', $today)
                      ->pluck('checked_in_at', 'student_id')
                      ->toArray();

        $present = count($checkins);
        $total   = $students->count();

        return view('admin.dashboard', compact('students', 'checkins', 'present', 'total'));
    }

    // ── Check in ──────────────────────────────────────────────
    public function checkIn(Request $request, int $studentId)
    {
        $today = now()->toDateString();

        DB::table('attendance')->updateOrInsert(
            ['student_id' => $studentId, 'attendance_date' => $today],
            ['checked_in_at' => now()->format('H:i'), 'updated_at' => now(), 'created_at' => now()]
        );

        $present = DB::table('attendance')->where('attendance_date', $today)->count();
        $total   = User::where('role', 'Student')->count();

        return response()->json([
            'status'  => 'ok',
            'time'    => now()->format('h:i A'),
            'present' => $present,
            'total'   => $total,
        ]);
    }

    // ── List students ─────────────────────────────────────────
    public function index()
    {
        $students = User::where('role', 'Student')->orderBy('name')->get();

        // Get all parent_child links for these students in one query,
        // then map student_id => parent (name + email)
        $studentIds = $students->pluck('user_id');

        $links = DB::table('parent_child')
                   ->join('users', 'users.user_id', '=', 'parent_child.parent_id')
                   ->whereIn('parent_child.student_id', $studentIds)
                   ->select('parent_child.student_id', 'users.name', 'users.email')
                   ->get()
                   ->groupBy('student_id');

        // Attach linked parents directly onto each student object
        foreach ($students as $student) {
            $student->linked_parents = $links->get($student->user_id, collect());
        }

        return view('admin.students', compact('students'));
    }

    // ── Create form ───────────────────────────────────────────
    public function create()
    {
        return view('admin.student-form');
    }

    // ── Store ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email|max:100',
            'password'  => 'required|string|min:6|confirmed',
            'age'       => 'nullable|integer|min:2|max:25',
            'diagnosis' => 'nullable|in:ADHD,Autism,Both,Other',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
            'role'          => 'Student',
            'age'           => $request->age,
            'diagnosis'     => $request->diagnosis,
            'accessibility_settings' => json_encode([
                'large_buttons' => $request->boolean('large_buttons'),
                'high_contrast' => $request->boolean('high_contrast'),
                'mute_sounds'   => $request->boolean('mute_sounds'),
                'text_size'     => (int) ($request->text_size ?? 16),
            ]),
        ]);

        return redirect()->route('admin.students.index')
                         ->with('success', 'Student profile created.');
    }

    // ── Show → redirect to edit ───────────────────────────────
    // Parameter renamed: $user → $student to match route param {student}
    public function show(User $student)
    {
        return redirect()->route('admin.students.edit', $student->user_id);
    }

    // ── Edit form ─────────────────────────────────────────────
    // Parameter renamed: $user → $student to match route param {student}
    public function edit(User $student)
    {
        abort_if($student->role !== 'Student', 404);
        return view('admin.student-form', ['student' => $student]);
    }

    // ── Update ────────────────────────────────────────────────
    // Parameter renamed: $user → $student to match route param {student}
    public function update(Request $request, User $student)
    {
        abort_if($student->role !== 'Student', 404);

        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email,' . $student->user_id . ',user_id',
            'age'       => 'nullable|integer|min:2|max:25',
            'diagnosis' => 'nullable|in:ADHD,Autism,Both,Other',
        ]);

        DB::table('users')->where('user_id', $student->user_id)->update([
            'name'                   => $request->name,
            'email'                  => $request->email,
            'age'                    => $request->age,
            'diagnosis'              => $request->diagnosis,
            'accessibility_settings' => json_encode([
                'large_buttons' => $request->boolean('large_buttons'),
                'high_contrast' => $request->boolean('high_contrast'),
                'mute_sounds'   => $request->boolean('mute_sounds'),
                'text_size'     => (int) ($request->text_size ?? 16),
            ]),
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            DB::table('users')->where('user_id', $student->user_id)->update([
                'password_hash' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.students.index')
                         ->with('success', 'Student profile updated.');
    }

    // ── Delete ────────────────────────────────────────────────
    // Parameter renamed: $user → $student to match route param {student}
    public function destroy(User $student)
    {
        abort_if($student->role !== 'Student', 404);
        DB::table('users')->where('user_id', $student->user_id)->delete();

        return redirect()->route('admin.students.index')
                         ->with('success', 'Student deleted.');
    }

    // ── Link parent ───────────────────────────────────────────
    // Already correctly named $student — this one was working
    public function linkParent(Request $request, User $student)
    {
        abort_if($student->role !== 'Student', 404);

        $request->validate(['parent_email' => 'required|email']);

        $parent = User::where('email', $request->parent_email)
                      ->where('role', 'Parent')
                      ->first();

        if (!$parent) {
            return redirect()->route('admin.students.index')
                             ->withErrors(['parent_email' => 'No Parent account found with that email.']);
        }

        $exists = DB::table('parent_child')
                    ->where('parent_id', $parent->user_id)
                    ->where('student_id', $student->user_id)
                    ->exists();

        if (!$exists) {
            DB::table('parent_child')->insert([
                'parent_id'  => $parent->user_id,
                'student_id' => $student->user_id,
                'created_at' => now(),
            ]);
        }

        return redirect()->route('admin.students.index')
                         ->with('success', $parent->name . ' linked to ' . $student->name . '.');
    }
}
