<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\ProgressLog;
use App\Models\FailureTracker;
use App\Models\Reward;
use App\Models\TekioNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskProgressController extends Controller
{
    const FAILURE_THRESHOLD = 2;
    const POINTS_PER_TASK   = 10;

    // ── Student taps "Done" ───────────────────────────────────
    public function complete(Request $request)
    {
        $request->validate([
            'task_id'            => 'required|exists:tasks,task_id',
            'time_taken_seconds' => 'required|integer|min:0',
        ]);

        $task    = Task::findOrFail($request->task_id);
        $student = Auth::user();

        DB::transaction(function () use ($student, $task, $request) {

            ProgressLog::create([
                'student_id'         => $student->user_id,
                'task_id'            => $task->task_id,
                'status'             => 'completed',
                'time_taken_seconds' => $request->time_taken_seconds,
                'attempt_timestamp'  => now(),
                'was_adapted'        => false,
            ]);

            FailureTracker::where('student_id', $student->user_id)
                          ->where('task_id', $task->task_id)
                          ->update(['consecutive_failures' => 0]);

            $reward = Reward::firstOrCreate(
                ['student_id' => $student->user_id],
                ['points' => 0, 'badges' => json_encode([])]
            );
            $reward->increment('points', self::POINTS_PER_TASK);

            $this->checkAndAwardBadges($student->user_id, $reward->fresh());
        });

        try {
            $this->checkAllTasksDone($student, $task);
        } catch (\Throwable $e) {
            Log::warning('checkAllTasksDone failed: ' . $e->getMessage());
        }

        return response()->json([
            'status'  => 'completed',
            'points'  => self::POINTS_PER_TASK,
            'message' => 'Great job! Task completed.',
        ]);
    }

    // ── Student taps "Need Help" or timer expires ─────────────
    public function fail(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,task_id',
        ]);

        $task    = Task::findOrFail($request->task_id);
        $student = Auth::user();
        $adapt   = false;

        $tracker = FailureTracker::firstOrCreate(
            [
                'student_id' => $student->user_id,
                'task_id'    => $task->task_id,
            ],
            [
                'consecutive_failures' => 0,
                'last_failure_date'    => now(),
            ]
        );

        $tracker->consecutive_failures += 1;
        $tracker->last_failure_date = now();
        $tracker->save();

        $log = ProgressLog::create([
            'student_id'         => $student->user_id,
            'task_id'            => $task->task_id,
            'status'             => 'failed',
            'time_taken_seconds' => 0,
            'attempt_timestamp'  => now(),
            'was_adapted'        => false,
        ]);

        $stop = false;

        if ($tracker->consecutive_failures >= self::FAILURE_THRESHOLD) {
            if ($task->has_micro_steps) {
                $adapt = true;
                $log->update(['was_adapted' => true]);
            } else {
                // Threshold reached but there is nothing to adapt to —
                // stop the retry loop instead of silently restarting the
                // timer forever. Tell the front-end to stop, and let a
                // teacher intervene manually.
                $stop = true;
            }

            try {
                $this->alertTeacher($student, $task);
            } catch (\Throwable $e) {
                Log::warning('alertTeacher failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'               => 'failed',
            'consecutive_failures' => $tracker->consecutive_failures,
            'adapt'                => $adapt,
            'stop'                 => $stop,
            'message'              => $adapt
                ? "Showing micro-steps now — you've got this!"
                : ($stop
                    ? "Let's come back to this one — your teacher has been notified."
                    : "Let's try again!"),
        ]);
    }

    // ── Teacher taps "Skip Task" ──────────────────────────────
    public function skip(Request $request)
    {
        $request->validate([
            'task_id'    => 'required|exists:tasks,task_id',
            'student_id' => 'required|exists:users,user_id',
        ]);

        ProgressLog::create([
            'student_id'         => $request->student_id,
            'task_id'            => $request->task_id,
            'status'             => 'skipped',
            'time_taken_seconds' => 0,
            'attempt_timestamp'  => now(),
            'was_adapted'        => false,
        ]);

        FailureTracker::where('student_id', $request->student_id)
                      ->where('task_id', $request->task_id)
                      ->update(['consecutive_failures' => 0]);

        return response()->json([
            'status'  => 'skipped',
            'message' => 'Task skipped. Student moved to next task.',
        ]);
    }

    // ── Live visual map data for teacher dashboard ────────────
    public function teacherMapStatus()
    {
        $today = now()->toDateString();

        $students = \App\Models\User::where('role', 'Student')->orderBy('name')->get();

        $result = $students->map(function ($student) use ($today) {

            $groupIds = DB::table('group_members')
                          ->where('student_id', $student->user_id)
                          ->pluck('group_id');

            $assignments = \App\Models\RoutineAssignment::where('is_active', true)
                ->whereDate('assigned_date', $today)
                ->where(function ($q) use ($student, $groupIds) {
                    $q->where('student_id', $student->user_id)
                      ->orWhereIn('group_id', $groupIds);
                })
                ->with(['routine.tasks' => fn($q) => $q->orderBy('display_order')])
                ->orderBy('assignment_id')
                ->get();

            $tasks = collect();
            foreach ($assignments as $assignment) {
                if ($assignment->routine) {
                    foreach ($assignment->routine->tasks as $t) {
                        $tasks->push($t);
                    }
                }
            }
            $tasks = $tasks->unique('task_id')->values();

            if ($tasks->isEmpty()) {
                return [
                    'user_id'         => $student->user_id,
                    'name'            => $student->name,
                    'status'          => 'pending',
                    'current_task'    => 'No routine today',
                    'current_task_id' => null,
                    'failures'        => 0,
                ];
            }

            $taskIds = $tasks->pluck('task_id');

            $doneOrSkippedIds = ProgressLog::where('student_id', $student->user_id)
                                           ->whereIn('task_id', $taskIds)
                                           ->whereIn('status', ['completed', 'skipped'])
                                           ->whereDate('attempt_timestamp', $today)
                                           ->pluck('task_id');

            $currentTask = $tasks->whereNotIn('task_id', $doneOrSkippedIds)->first();

            $lastLog = ProgressLog::where('student_id', $student->user_id)
                                  ->whereIn('task_id', $taskIds)
                                  ->whereDate('attempt_timestamp', $today)
                                  ->latest('log_id')
                                  ->first();

            // Status should reflect whether there's still a task to do,
            // not just blindly mirror the last log entry. A student can
            // complete task A (logging "completed") and then have a 2nd
            // routine assigned with task B still pending — in that case
            // they are NOT done overall, they're back "in progress" on
            // the new task, not stuck showing green/"completed".
            if ($currentTask) {
                $status = ($lastLog && $lastLog->task_id === $currentTask->task_id)
                    ? $lastLog->status
                    : 'pending';
            } else {
                $status = 'completed';
            }

            // Failures only count against the CURRENT task, today
            $failures = 0;
            if ($currentTask) {
                $tracker = FailureTracker::where('student_id', $student->user_id)
                                         ->where('task_id', $currentTask->task_id)
                                         ->whereDate('last_failure_date', $today)
                                         ->first();
                $failures = $tracker->consecutive_failures ?? 0;
            }

            return [
                'user_id'         => $student->user_id,
                'name'            => $student->name,
                'status'          => $status,
                'current_task'    => $currentTask?->title ?? 'All tasks done',
                'current_task_id' => $currentTask?->task_id,
                'failures'        => $failures,
            ];
        });

        return response()->json($result->values());
    }

    // ── Private helpers ───────────────────────────────────────

    private function alertTeacher($student, Task $task): void
    {
        $teacherId = $task->routine->teacher_id ?? null;
        if (!$teacherId) {
            Log::warning("alertTeacher: no teacher_id found for task {$task->task_id}");
            return;
        }

        TekioNotification::create([
            'user_id' => $teacherId,
            'type'    => 'alert',
            'message' => $student->name . ' is stuck on "' . $task->title . '" (' . self::FAILURE_THRESHOLD . ' consecutive failures). Micro-steps triggered.',
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }

    private function checkAndAwardBadges(int $studentId, Reward $reward): void
    {
    $badges = is_array($reward->badges)
        ? $reward->badges
        : json_decode($reward->badges ?? '[]', true);

    $totalDone = ProgressLog::where('student_id', $studentId)
                            ->where('status', 'completed')
                            ->count();

    // Load dynamic badges from DB (all teachers' badges apply to all students)
    $badgeDefs = DB::table('badges')->orderBy('threshold')->get();

    // Fallback hardcoded badges if no custom ones exist
    if ($badgeDefs->isEmpty()) {
        $badgeDefs = collect([
            (object)['name' => 'First Task', 'threshold' => 1],
            (object)['name' => 'High Five',  'threshold' => 5],
            (object)['name' => 'Ten Done',   'threshold' => 10],
            (object)['name' => 'Champion',   'threshold' => 20],
        ]);
    }

    $newBadges = [];
    foreach ($badgeDefs as $def) {
        if ($totalDone >= $def->threshold && !in_array($def->name, $badges)) {
            $newBadges[] = $def->name;
        }
    }

    if (!empty($newBadges)) {
        $reward->update(['badges' => json_encode(array_merge($badges, $newBadges))]);
    }
    }

    private function checkAllTasksDone($student, Task $completedTask): void
    {
        $routine = $completedTask->routine;
        if (!$routine) return;

        $totalTasks     = $routine->tasks()->count();
        $completedToday = ProgressLog::where('student_id', $student->user_id)
                                     ->whereIn('task_id', $routine->tasks()->pluck('task_id'))
                                     ->where('status', 'completed')
                                     ->whereDate('attempt_timestamp', today())
                                     ->distinct('task_id')
                                     ->count('task_id');

        if ($completedToday >= $totalTasks) {
            $parentIds = DB::table('parent_child')
                           ->where('student_id', $student->user_id)
                           ->pluck('parent_id');

            foreach ($parentIds as $parentId) {
                TekioNotification::create([
                    'user_id' => $parentId,
                    'type'    => 'email',
                    'message' => $student->name . ' has completed all tasks for today!',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }
        }
    }
}
