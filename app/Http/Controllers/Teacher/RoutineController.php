<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use App\Models\Task;
use App\Models\MicroStep;
use App\Models\StudentGroup;
use App\Models\RoutineAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoutineController extends Controller
{
    // ── Teacher dashboard ─────────────────────────────────────
    public function teacherDashboard()
    {
        return view('teacher.dashboard');
    }

    // ── Teacher students page ─────────────────────────────────
    public function teacherStudents()
    {
        $students = User::where('role', 'Student')
                        ->with(['groups' => function ($q) {
                            $q->where('teacher_id', Auth::id());
                        }])
                        ->orderBy('name')
                        ->get();

        $groups = StudentGroup::where('teacher_id', Auth::id())
                              ->with('members')
                              ->get();

        return view('teacher.students', compact('students', 'groups'));
    }

    // ── List routines ─────────────────────────────────────────
    // Eager loads assignments with student and group so the routines
    // blade can show who each routine is assigned to without extra queries.
    public function index()
    {
        $routines = Routine::where('teacher_id', Auth::id())
                           ->withCount('tasks')
                           ->with(['assignments' => function ($q) {
                               $q->whereDate('assigned_date', today())
                                 ->with(['student', 'group'])
                                 ->latest('assigned_date');
                           }])
                           ->latest()
                           ->get();

        $students = User::where('role', 'Student')->orderBy('name')->get();
        $groups   = StudentGroup::where('teacher_id', Auth::id())->get();

        return view('teacher.routines', compact('routines', 'groups', 'students'));
    }

    // ── Create form ───────────────────────────────────────────
    public function create()
    {
        return view('teacher.routine-form');
    }

    // ── Store ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                               => 'required|string|max:100',
            'tasks'                              => 'required|array|min:1',
            'tasks.*.title'                      => 'required|string|max:200',
            'tasks.*.estimated_duration_seconds' => 'required|integer|min:10|max:3600',
        ]);

        DB::transaction(function () use ($request) {
            $routine = Routine::create([
                'name'       => $request->name,
                'teacher_id' => Auth::id(),
            ]);

            foreach ($request->tasks as $order => $taskData) {
                $hasMicro = !empty($taskData['micro_steps']);
                $task = Task::create([
                    'routine_id'                 => $routine->routine_id,
                    'title'                      => $taskData['title'],
                    'estimated_duration_seconds' => $taskData['estimated_duration_seconds'],
                    'has_micro_steps'            => $hasMicro,
                    'display_order'              => $order + 1,
                ]);

                if ($hasMicro) {
                    foreach ($taskData['micro_steps'] as $stepOrder => $step) {
                        MicroStep::create([
                            'task_id'     => $task->task_id,
                            'step_order'  => $stepOrder + 1,
                            'description' => $step['description'],
                            'image_url'   => $step['image_url'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('teacher.routines.index')
                         ->with('success', 'Routine created.');
    }

    // ── Show → redirect to edit ───────────────────────────────
    public function show(Routine $routine)
    {
        return redirect()->route('teacher.routines.edit', $routine);
    }

    // ── Edit form ─────────────────────────────────────────────
    public function edit(Routine $routine)
    {
        $this->authorizeRoutine($routine);
        $routine->load([
            'tasks'            => fn($q) => $q->orderBy('display_order'),
            'tasks.microSteps' => fn($q) => $q->orderBy('step_order'),
        ]);
        return view('teacher.routine-form', compact('routine'));
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request, Routine $routine)
    {
        $this->authorizeRoutine($routine);

        $request->validate([
            'name'                               => 'required|string|max:100',
            'tasks'                              => 'required|array|min:1',
            'tasks.*.title'                      => 'required|string|max:200',
            'tasks.*.estimated_duration_seconds' => 'required|integer|min:10|max:3600',
        ]);

        DB::transaction(function () use ($request, $routine) {
            $routine->update(['name' => $request->name]);
            $routine->tasks()->delete();

            foreach ($request->tasks as $order => $taskData) {
                $hasMicro = !empty($taskData['micro_steps']);
                $task = Task::create([
                    'routine_id'                 => $routine->routine_id,
                    'title'                      => $taskData['title'],
                    'estimated_duration_seconds' => $taskData['estimated_duration_seconds'],
                    'has_micro_steps'            => $hasMicro,
                    'display_order'              => $order + 1,
                ]);

                if ($hasMicro) {
                    foreach ($taskData['micro_steps'] as $stepOrder => $step) {
                        MicroStep::create([
                            'task_id'     => $task->task_id,
                            'step_order'  => $stepOrder + 1,
                            'description' => $step['description'],
                            'image_url'   => $step['image_url'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('teacher.routines.index')
                         ->with('success', 'Routine updated.');
    }

    // ── Destroy routine ───────────────────────────────────────
    public function destroy(Routine $routine)
    {
        $this->authorizeRoutine($routine);
        $routine->delete();
        return redirect()->route('teacher.routines.index')
                         ->with('success', 'Routine deleted.');
    }

    // ── Assign to student or group ────────────────────────────
    public function assign(Request $request, Routine $routine)
    {
        $this->authorizeRoutine($routine);

        $request->validate([
            'assign_to'  => 'required|in:student,group',
            'student_id' => 'required_if:assign_to,student|nullable|exists:users,user_id',
            'group_id'   => 'required_if:assign_to,group|nullable|exists:student_groups,group_id',
        ]);

        RoutineAssignment::create([
            'routine_id'    => $routine->routine_id,
            'student_id'    => $request->assign_to === 'student' ? $request->student_id : null,
            'group_id'      => $request->assign_to === 'group'   ? $request->group_id   : null,
            'assigned_date' => today()->toDateString(),
            'is_active'     => true,
        ]);

        return redirect()->route('teacher.routines.index')
                         ->with('success', 'Routine assigned.');
    }

    // ── Remove a single assignment (× button on tag) ──────────
    public function removeAssignment(int $routineId, int $assignmentId)
    {
        // Verify the routine belongs to this teacher before deleting
        $routine = Routine::where('routine_id', $routineId)
                          ->where('teacher_id', Auth::id())
                          ->firstOrFail();

        RoutineAssignment::where('assignment_id', $assignmentId)
                         ->where('routine_id', $routine->routine_id)
                         ->delete();

        return redirect()->route('teacher.routines.index')
                         ->with('success', 'Assignment removed.');
    }

    // ── Private helpers ───────────────────────────────────────
    private function authorizeRoutine(Routine $routine): void
    {
        abort_if($routine->teacher_id !== Auth::id(), 403, 'Unauthorized.');
    }
}
