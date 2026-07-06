<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\TaskProgressController;
use App\Http\Controllers\Teacher\RoutineController;
use App\Http\Controllers\Teacher\NotificationController;
use App\Http\Controllers\Teacher\StudentGroupController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Therapist\TherapistDashboardController;

// ── Public ────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

// ── Profile — all roles ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',          [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Session/CSRF heartbeat — pinged periodically by JS while a tab is
    // open, so the session's last_activity stays fresh and the CSRF
    // token never goes stale. Prevents 419s on long-idle tabs instead
    // of just handling them after the fact.
    Route::get('/keep-alive', function (\Illuminate\Http\Request $request) {
        $request->session()->put('last_activity', time());
        return response()->json(['token' => csrf_token()]);
    })->name('keep-alive');
});

// ── Student ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:Student'])
     ->prefix('student')->name('student.')
     ->group(function () {
         Route::get('/dashboard',      [StudentDashboardController::class, 'index'])->name('dashboard');
         Route::get('/routine',        [StudentDashboardController::class, 'routine'])->name('routine');
         Route::get('/microstep',      [StudentDashboardController::class, 'microstep'])->name('microstep');
         Route::get('/rewards',        [StudentDashboardController::class, 'rewards'])->name('rewards');
         Route::get('/summary',        [StudentDashboardController::class, 'summary'])->name('summary');
         Route::get('/no-routine',     fn() => view('student.no-routine'))->name('no-routine');
         Route::post('/task/complete', [TaskProgressController::class, 'complete'])->name('task.complete');
         Route::post('/task/fail',     [TaskProgressController::class, 'fail'])->name('task.fail');
     });

// ── Teacher ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:Teacher'])
     ->prefix('teacher')->name('teacher.')
     ->group(function () {
         Route::get('/dashboard', [RoutineController::class, 'teacherDashboard'])->name('dashboard');
         Route::get('/students',  [RoutineController::class, 'teacherStudents'])->name('students');

         // Badge management
        Route::get('/badges',          [App\Http\Controllers\Teacher\BadgeController::class, 'index'])->name('badges.index');
        Route::post('/badges',         [App\Http\Controllers\Teacher\BadgeController::class, 'store'])->name('badges.store');
        Route::delete('/badges/{id}',  [App\Http\Controllers\Teacher\BadgeController::class, 'destroy'])->name('badges.destroy');

         // Group management
         Route::post('/groups',
                      [StudentGroupController::class, 'store'])->name('groups.store');
         Route::post('/groups/{studentId}/add-member',
                      [StudentGroupController::class, 'addMember'])->name('groups.add-member');
         Route::delete('/groups/{groupId}/remove-member/{studentId}',
                      [StudentGroupController::class, 'removeMember'])->name('groups.remove-member');
         Route::delete('/groups/{groupId}',
                      [StudentGroupController::class, 'destroy'])->name('groups.destroy');

         // Routine CRUD
         Route::resource('routines', RoutineController::class);
         Route::post('/routines/{routine}/assign',
                      [RoutineController::class, 'assign'])->name('routines.assign');

         // Remove a single routine assignment (the × button on each tag)
         Route::delete('/routines/{routineId}/assignments/{assignmentId}',
                      [RoutineController::class, 'removeAssignment'])
                      ->name('routines.assignments.destroy');

         // Notifications + map
         Route::get('/notifications/unread',
                      [NotificationController::class, 'unread'])->name('notifications.unread');
         Route::patch('/notifications/{notification}/read',
                      [NotificationController::class, 'markRead'])->name('notifications.read');
         Route::post('/task/skip',
                      [TaskProgressController::class, 'skip'])->name('task.skip');
         Route::get('/map-status',
                      [TaskProgressController::class, 'teacherMapStatus'])->name('map.status');
     });

// ── Parent ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Parent'])
     ->prefix('parent')->name('parent.')
     ->group(function () {
         Route::get('/dashboard',         [ParentController::class, 'dashboard'])->name('dashboard');
         Route::post('/comment',          [ParentController::class, 'storeComment'])->name('comment.store');
         Route::delete('/comment/{id}',   [ParentController::class, 'deleteComment'])->name('comment.delete');
         Route::get('/home-task',         [ParentController::class, 'homeTaskForm'])->name('home-task');
         Route::post('/home-task',        [ParentController::class, 'storeHomeTask'])->name('home-task.store');
         Route::get('/home-task/history', [ParentController::class, 'homeTaskHistory'])->name('home-task.history');
         Route::delete('/home-task/{id}', [ParentController::class, 'deleteHomeTask'])->name('home-task.delete');
         Route::get('/switch/{studentId}',[ParentController::class, 'switchChild'])->name('switch-child');
     });

// ── Administrator ─────────────────────────────────────────────
Route::middleware(['auth', 'role:Administrator'])
     ->prefix('admin')->name('admin.')
     ->group(function () {
         Route::get('/dashboard',            [StudentController::class, 'adminDashboard'])->name('dashboard');
         Route::post('/checkin/{studentId}', [StudentController::class, 'checkIn'])->name('checkin');

         Route::resource('students', StudentController::class);
         Route::post('/students/{student}/link-parent',
                      [StudentController::class, 'linkParent'])->name('students.link-parent');

         Route::resource('users', UserManagementController::class)->except(['show']);

         Route::get('/reports',         [ReportController::class, 'index'])->name('reports');
         Route::get('/reports/export',  [ReportController::class, 'exportCsv'])->name('reports.export');
         Route::get('/reports/preview', [ReportController::class, 'preview'])->name('reports.preview');
     });

// ── Therapist ─────────────────────────────────────────────────
Route::middleware(['auth', 'role:Therapist'])
     ->prefix('therapist')->name('therapist.')
     ->group(function () {
         Route::get('/dashboard',   [TherapistDashboardController::class, 'dashboard'])->name('dashboard');
         Route::get('/case-notes',  [TherapistDashboardController::class, 'caseNotes'])->name('case-notes');
         Route::post('/case-notes', [TherapistDashboardController::class, 'storeNote'])->name('case-notes.store');
         Route::delete('/case-notes/{note}',
                        [TherapistDashboardController::class, 'deleteNote'])->name('case-notes.delete');
     });
