<?php


namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────
    public function dashboard()
    {
        $parentId = Auth::id();

        $children = DB::table('parent_child')
                      ->where('parent_id', $parentId)
                      ->join('users', 'users.user_id', '=', 'parent_child.student_id')
                      ->select('users.user_id', 'users.name', 'users.diagnosis')
                      ->get();

        $childId = session('active_child_id', $children->first()?->user_id);
        $child   = $children->firstWhere('user_id', $childId);

        $todayCompleted  = 0;
        $todayTotal      = 0;
        $weeklyProgress  = array_fill(0, 7, 0);
        $weekStart       = now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weeklyLabels    = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i)->format('D'))->all();
        $taskHistory     = collect();
        $recentComments  = collect();
        $recentHomeTasks = collect();

        if ($child) {
            $today = now()->toDateString();

            $todayTotal = ProgressLog::where('student_id', $child->user_id)
                                     ->whereDate('attempt_timestamp', $today)
                                     ->distinct('task_id')->count('task_id');

            $todayCompleted = ProgressLog::where('student_id', $child->user_id)
                                          ->whereDate('attempt_timestamp', $today)
                                          ->where('status', 'completed')
                                          ->distinct('task_id')->count('task_id');

            $weekStart = now()->startOfWeek(\Carbon\Carbon::MONDAY);
            for ($i = 0; $i < 7; $i++) {
                $date    = $weekStart->copy()->addDays($i);
                $dateStr = $date->toDateString();
                $total = ProgressLog::where('student_id', $child->user_id)
                                    ->whereDate('attempt_timestamp', $dateStr)
                                    ->distinct('task_id')->count('task_id');
                $done  = ProgressLog::where('student_id', $child->user_id)
                                    ->whereDate('attempt_timestamp', $dateStr)
                                    ->where('status', 'completed')
                                    ->distinct('task_id')->count('task_id');
                $weeklyProgress[$i] = $total > 0 ? round(($done / $total) * 100) : 0;
                $weeklyLabels[$i]   = $date->format('D'); // Mon..Sun, fixed calendar week
            }

            $taskHistory = ProgressLog::where('student_id', $child->user_id)
                                       ->with('task')
                                       ->latest('attempt_timestamp')
                                       ->take(20)
                                       ->get();

            $recentComments = DB::table('parent_comments')
                                 ->where('student_id', $child->user_id)
                                 ->where('parent_id', $parentId)
                                 ->latest('created_at')
                                 ->take(10)
                                 ->get();

            $recentHomeTasks = DB::table('home_progress')
                                  ->where('student_id', $child->user_id)
                                  ->where('parent_id', $parentId)
                                  ->latest('completed_date')
                                  ->take(5)
                                  ->get();
        }

        return view('parent.dashboard', compact(
            'children', 'child',
            'todayCompleted', 'todayTotal',
            'weeklyProgress', 'weeklyLabels', 'taskHistory',
            'recentComments', 'recentHomeTasks'
        ));
    }

    // ── Switch active child ───────────────────────────────────
    public function switchChild(int $studentId)
    {
        session(['active_child_id' => $studentId]);
        return redirect()->route('parent.dashboard');
    }

    // ── Store comment ─────────────────────────────────────────
    public function storeComment(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:users,user_id',
            'comment_text' => 'required|string|max:500',
        ]);

        DB::table('parent_comments')->insert([
            'parent_id'    => Auth::id(),
            'student_id'   => $request->student_id,
            'comment_text' => $request->comment_text,
            'created_at'   => now(),
        ]);

        return redirect()->route('parent.dashboard')
                         ->with('success', 'Comment added.');
    }

    // ── Delete comment ────────────────────────────────────────
    public function deleteComment(int $id)
    {
        DB::table('parent_comments')
          ->where('comment_id', $id)
          ->where('parent_id', Auth::id())
          ->delete();

        return redirect()->route('parent.dashboard')
                         ->with('success', 'Comment deleted.');
    }

    // ── Home task form ────────────────────────────────────────
    public function homeTaskForm()
    {
        $parentId = Auth::id();

        $children = DB::table('parent_child')
                      ->where('parent_id', $parentId)
                      ->join('users', 'users.user_id', '=', 'parent_child.student_id')
                      ->select('users.user_id', 'users.name')
                      ->get();

        return view('parent.home-task', compact('children'));
    }

    // ── Store home task ───────────────────────────────────────
    public function storeHomeTask(Request $request)
    {
        $request->validate([
            'student_id'     => 'required|exists:users,user_id',
            'task_name'      => 'required|string|max:200',
            'completed_date' => 'required|date|before_or_equal:today',
            'notes'          => 'nullable|string|max:500',
        ]);

        DB::table('home_progress')->insert([
            'parent_id'      => Auth::id(),
            'student_id'     => $request->student_id,
            'task_name'      => $request->task_name,
            'completed_date' => $request->completed_date,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('parent.home-task')
                         ->with('success', 'Home task recorded.');
    }

    // ── Delete home task entry ────────────────────────────────
    // Only allows deleting entries that belong to this parent
    public function deleteHomeTask(int $id)
    {
        DB::table('home_progress')
          ->where('home_task_id', $id)
          ->where('parent_id', Auth::id())
          ->delete();

        return redirect()->route('parent.home-task.history')
                         ->with('success', 'Home task entry deleted.');
    }

    // ── Home task history ─────────────────────────────────────
    public function homeTaskHistory()
    {
        $parentId = Auth::id();
        $childId  = session('active_child_id');

        $query = DB::table('home_progress')
                   ->where('home_progress.parent_id', $parentId)
                   ->join('users', 'users.user_id', '=', 'home_progress.student_id')
                   ->select('home_progress.*', 'users.name as student_name');

        if ($childId) {
            $query->where('home_progress.student_id', $childId);
        }

        $homeTasks = $query->latest('completed_date')->paginate(15);

        return view('parent.home-task-history', compact('homeTasks'));
    }
}
