<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ProgressLog;
use App\Models\RoutineAssignment;
use App\Models\Reward;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────
    // ── Dashboard ─────────────────────────────────────────────
    public function index()
    {
        $student = Auth::user();
        $tasks   = $this->getTodayTasks($student->user_id);

        $hasRoutine = $tasks->isNotEmpty();
        $totalTasks = $tasks->count();

        $completedTaskIds = collect();
        $skippedTaskIds    = collect();
        $completedToday    = 0;

        if ($hasRoutine) {
            $taskIds = $tasks->pluck('task_id');

            $completedTaskIds = ProgressLog::where('student_id', $student->user_id)
                                           ->whereIn('task_id', $taskIds)
                                           ->where('status', 'completed')
                                           ->whereDate('attempt_timestamp', today())
                                           ->pluck('task_id');

            $skippedTaskIds = ProgressLog::where('student_id', $student->user_id)
                                         ->whereIn('task_id', $taskIds)
                                         ->where('status', 'skipped')
                                         ->whereDate('attempt_timestamp', today())
                                         ->pluck('task_id');

            $completedToday = $completedTaskIds->merge($skippedTaskIds)->unique()->count();
        }

        $currentTask    = $completedToday + 1;
        $weeklyProgress = $this->getWeeklyProgress($student->user_id);

        $reward = Reward::firstOrCreate(
            ['student_id' => $student->user_id],
            ['points' => 0, 'badges' => json_encode([])]
        );

        $totalPoints = $reward->points;
        $badges      = is_array($reward->badges)
                        ? $reward->badges
                        : (json_decode($reward->badges, true) ?? []);

        $motivational = "You're doing great today, {$student->name}!";

        return view('student.dashboard', compact(
            'student', 'hasRoutine', 'tasks', 'completedTaskIds', 'skippedTaskIds',
            'currentTask', 'totalTasks', 'completedToday',
            'weeklyProgress', 'totalPoints', 'badges',
            'motivational'
        ));
    }

    // ── Routine view ──────────────────────────────────────────
    public function routine()
    {
        $student = Auth::user();
        $tasks   = $this->getTodayTasks($student->user_id);

        if ($tasks->isEmpty()) {
            return redirect()->route('student.no-routine');
        }

        $taskIds = $tasks->pluck('task_id');

        $doneOrSkippedIds = ProgressLog::where('student_id', $student->user_id)
                                       ->whereIn('task_id', $taskIds)
                                       ->whereIn('status', ['completed', 'skipped'])
                                       ->whereDate('attempt_timestamp', today())
                                       ->pluck('task_id');

        $task = $tasks->whereNotIn('task_id', $doneOrSkippedIds)->first();

        if (!$task) {
            return redirect()->route('student.summary');
        }

        // If this task has already hit the failure threshold with no
        // micro-steps to fall back on, it's locked awaiting teacher
        // action (Skip Task) — don't restart the timer and re-trigger
        // the same failure/notification cycle every time the student
        // lands back here.
        $isLocked = false;
        if (!$task->has_micro_steps) {
            $tracker = \App\Models\FailureTracker::where('student_id', $student->user_id)
                                                   ->where('task_id', $task->task_id)
                                                   ->first();
            if ($tracker && $tracker->consecutive_failures >= \App\Http\Controllers\Student\TaskProgressController::FAILURE_THRESHOLD) {
                $isLocked = true;
            }
        }

        $totalTasks     = $tasks->count();
        $completedToday = $doneOrSkippedIds->count();
        $currentTaskNum = $completedToday + 1;

        return view('student.routine', compact('task', 'totalTasks', 'currentTaskNum', 'isLocked'));
    }

    // ── Microstep view ────────────────────────────────────────
    public function microstep()
    {
        $student = Auth::user();
        $tasks   = $this->getTodayTasks($student->user_id);

        $task       = null;
        $microSteps = collect();

        if ($tasks->isNotEmpty()) {
            $taskIds = $tasks->pluck('task_id');

            $doneOrSkippedIds = ProgressLog::where('student_id', $student->user_id)
                                           ->whereIn('task_id', $taskIds)
                                           ->whereIn('status', ['completed', 'skipped'])
                                           ->whereDate('attempt_timestamp', today())
                                           ->pluck('task_id');

            $task = $tasks->whereNotIn('task_id', $doneOrSkippedIds)->first();

            if ($task) {
                $microSteps = $task->microSteps;
            }
        }

        return view('student.microstep', compact('task', 'microSteps'));
    }

    // ── Rewards view ──────────────────────────────────────────
    public function rewards()
    {
        $student = Auth::user();

        $reward = Reward::firstOrCreate(
            ['student_id' => $student->user_id],
            ['points' => 0, 'badges' => json_encode([])]
        );

        $totalPoints = $reward->points;
        $badges      = is_array($reward->badges)
                        ? $reward->badges
                        : (json_decode($reward->badges, true) ?? []);

        $taskHistory = ProgressLog::where('student_id', $student->user_id)
                                   ->where('status', 'completed')
                                   ->with('task')
                                   ->latest('attempt_timestamp')
                                   ->take(20)
                                   ->get();

        return view('student.rewards', compact('totalPoints', 'badges', 'taskHistory'));
    }

    // ── Summary view ──────────────────────────────────────────
    public function summary()
    {
        $student = Auth::user();
        $tasks   = $this->getTodayTasks($student->user_id);

        $taskSummary = collect();
        $totalPoints = 0;

        if ($tasks->isNotEmpty()) {
            $taskIds = $tasks->pluck('task_id');

            $logs = ProgressLog::where('student_id', $student->user_id)
                                ->whereIn('task_id', $taskIds)
                                ->whereDate('attempt_timestamp', today())
                                ->with('task')
                                ->get()
                                ->groupBy('task_id');

            $taskSummary = $tasks->map(function ($task) use ($logs) {
                $taskLogs = $logs->get($task->task_id, collect());
                $lastLog  = $taskLogs->sortByDesc('log_id')->first();
                $status   = $lastLog?->status ?? 'pending';
                $points   = $status === 'completed' ? 10 : 0;
                return (object) [
                    'title'  => $task->title,
                    'status' => $status,
                    'points' => $points,
                ];
            });

            $totalPoints = $taskSummary->sum('points');
        }

        return view('student.summary', compact('taskSummary', 'totalPoints'));
    }

    // ── Private helpers ───────────────────────────────────────

    /**
     * Returns a combined, ordered collection of Task models from
     * every active routine assignment for this student today —
     * whether assigned directly or via a group they belong to.
     * Tasks are ordered by assignment_id (assignment order), then
     * by the task's own display_order within its routine.
     */
    private function getTodayTasks(int $studentId)
    {
        $groupIds = DB::table('group_members')
                      ->where('student_id', $studentId)
                      ->pluck('group_id');

        $assignments = RoutineAssignment::where('is_active', true)
            ->whereDate('assigned_date', today())
            ->where(function ($q) use ($studentId, $groupIds) {
                $q->where('student_id', $studentId)
                  ->orWhereIn('group_id', $groupIds);
            })
            ->with(['routine.tasks' => function ($q) {
                $q->orderBy('display_order')->with('microSteps');
            }])
            ->orderBy('assignment_id')
            ->get();

        $tasks = collect();
        foreach ($assignments as $assignment) {
            if ($assignment->routine) {
                foreach ($assignment->routine->tasks as $task) {
                    $tasks->push($task);
                }
            }
        }

        return $tasks->unique('task_id')->values();
    }

    private function getWeeklyProgress(int $studentId): array
    {
        $progress = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = now()->subDays($i)->toDateString();
            $total = ProgressLog::where('student_id', $studentId)
                                ->whereDate('attempt_timestamp', $date)
                                ->distinct('task_id')->count('task_id');
            $done  = ProgressLog::where('student_id', $studentId)
                                ->whereDate('attempt_timestamp', $date)
                                ->where('status', 'completed')
                                ->distinct('task_id')->count('task_id');
            $progress[] = $total > 0 ? round(($done / $total) * 100) : 0;
        }
        return $progress;
    }
}
