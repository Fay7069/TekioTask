<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentGroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['group_name' => 'required|string|max:100']);

        StudentGroup::create([
            'group_name' => $request->group_name,
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->route('teacher.students')
                         ->with('success', 'Group "' . $request->group_name . '" created.');
    }

    public function addMember(Request $request, int $studentId)
    {
        $request->validate(['group_id' => 'required|exists:student_groups,group_id']);

        $group = StudentGroup::where('group_id', $request->group_id)
                             ->where('teacher_id', Auth::id())
                             ->firstOrFail();

        $exists = DB::table('group_members')
                    ->where('group_id', $group->group_id)
                    ->where('student_id', $studentId)
                    ->exists();

        if (!$exists) {
            DB::table('group_members')->insert([
                'group_id'   => $group->group_id,
                'student_id' => $studentId,
            ]);
        }

        return redirect()->route('teacher.students')
                         ->with('success', 'Student added to group.');
    }

    public function removeMember(Request $request, int $groupId, int $studentId)
    {
        // Verify this teacher owns the group
        StudentGroup::where('group_id', $groupId)
                    ->where('teacher_id', Auth::id())
                    ->firstOrFail();

        DB::table('group_members')
          ->where('group_id', $groupId)
          ->where('student_id', $studentId)
          ->delete();

        return redirect()->route('teacher.students')
                         ->with('success', 'Student removed from group.');
    }

    public function destroy(int $groupId)
    {
        $group = StudentGroup::where('group_id', $groupId)
                             ->where('teacher_id', Auth::id())
                             ->firstOrFail();
        $group->delete();

        return redirect()->route('teacher.students')
                         ->with('success', 'Group deleted.');
    }
}
