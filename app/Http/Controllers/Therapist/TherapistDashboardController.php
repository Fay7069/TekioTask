<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\CaseNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TherapistDashboardController extends Controller
{
    public function dashboard()
    {
        $recentNotes = CaseNote::where('therapist_id', Auth::id())
                               ->with('student')
                               ->latest()
                               ->take(5)
                               ->get();

        $students = User::where('role', 'Student')->orderBy('name')->get();

        return view('therapist.dashboard', compact('recentNotes', 'students'));
    }

    public function caseNotes(Request $request)
    {
        $students = User::where('role', 'Student')->orderBy('name')->get();

        $notes = CaseNote::where('therapist_id', Auth::id())
                         ->when($request->filled('student_id'), function ($q) use ($request) {
                             $q->where('student_id', $request->student_id);
                         })
                         ->with('student')
                         ->latest()
                         ->get();

        return view('therapist.case-notes', compact('students', 'notes'));
    }

    public function storeNote(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,user_id',
            'content'    => 'required|string|max:5000',
        ]);

        $student = User::where('user_id', $request->student_id)
                       ->where('role', 'Student')
                       ->firstOrFail();

        CaseNote::create([
            'therapist_id' => Auth::id(),
            'student_id'   => $student->user_id,
            'content'      => $request->content,
        ]);

        return redirect()->route('therapist.case-notes')
                         ->with('success', '✅ Case note saved.');
    }

    public function deleteNote(CaseNote $note)
    {
        // Only the therapist who wrote it can delete it
        abort_if($note->therapist_id !== Auth::id(), 403);
        $note->delete();

        return redirect()->route('therapist.case-notes')
                         ->with('success', 'Case note deleted.');
    }
}
