<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    // ── Reports page ──────────────────────────────────────────
    public function index()
    {
        $students = User::where('role', 'Student')->orderBy('name')->get();
        return view('admin.reports', compact('students'));
    }

    // ── CSV export — includes attendance + progress ───────────
    public function exportCsv(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable|exists:users,user_id',
            'from'       => 'required|date',
            'to'         => 'required|date|after_or_equal:from',
        ]);

        $query = ProgressLog::with(['task.routine', 'student'])
            ->whereBetween('attempt_timestamp', [
                $request->from . ' 00:00:00',
                $request->to   . ' 23:59:59',
            ]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $logs = $query->orderBy('attempt_timestamp')->get();

        // Load attendance for the same date range
        $attendanceQuery = DB::table('attendance')
            ->whereBetween('attendance_date', [$request->from, $request->to]);

        if ($request->filled('student_id')) {
            $attendanceQuery->where('student_id', $request->student_id);
        }

        $attendance = $attendanceQuery->get()->groupBy('student_id');

        $filename = 'tekiotask_report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs, $attendance) {
            $file = fopen('php://output', 'w');

            // Section 1: Task Progress
            fputcsv($file, ['=== TASK PROGRESS ===']);
            fputcsv($file, [
                'Date', 'Time', 'Student Name', 'Diagnosis',
                'Routine', 'Task', 'Status', 'Time Taken (s)', 'Micro-Steps Used',
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->attempt_timestamp->format('d/m/Y'),
                    $log->attempt_timestamp->format('H:i:s'),
                    $log->student->name ?? '-',
                    $log->student->diagnosis ?? '-',
                    $log->task->routine->name ?? '-',
                    $log->task->title ?? '-',
                    ucfirst($log->status),
                    $log->time_taken_seconds ?? '-',
                    $log->was_adapted ? 'Yes' : 'No',
                ]);
            }

            // Section 2: Attendance
            fputcsv($file, []);
            fputcsv($file, ['=== ATTENDANCE ===']);
            fputcsv($file, ['Student ID', 'Date', 'Check-in Time']);

            foreach ($attendance as $studentId => $records) {
                foreach ($records as $record) {
                    fputcsv($file, [
                        $studentId,
                        $record->attendance_date,
                        $record->checked_in_at,
                    ]);
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // ── Preview stats (JSON) ──────────────────────────────────
    public function preview(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable|exists:users,user_id',
            'from'       => 'required|date',
            'to'         => 'required|date|after_or_equal:from',
        ]);

        $query = ProgressLog::whereBetween('attempt_timestamp', [
            $request->from . ' 00:00:00',
            $request->to   . ' 23:59:59',
        ]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $logs        = $query->get();
        $total       = $logs->count();
        $completed   = $logs->where('status', 'completed')->count();
        $adaptations = $logs->where('was_adapted', true)->count();
        $rate        = $total > 0 ? round(($completed / $total) * 100) : 0;

        // Attendance count for the period
        $attQuery = DB::table('attendance')
            ->whereBetween('attendance_date', [$request->from, $request->to]);

        if ($request->filled('student_id')) {
            $attQuery->where('student_id', $request->student_id);
        }

        $attendanceDays = $attQuery->count();

        return response()->json([
            'total'          => $total,
            'completed'      => $completed,
            'rate'           => $rate,
            'adaptations'    => $adaptations,
            'attendance_days' => $attendanceDays,
        ]);
    }
}
