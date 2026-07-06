
@extends('layouts.teacher')
@section('title', 'Routine Management')
@section('page-title', 'Routine Management')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="page-actions">
    <a href="{{ route('teacher.routines.create') }}" class="btn btn-primary">+ Create New Routine</a>
</div>

@forelse ($routines as $routine)
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <span class="card-title">{{ $routine->name }}</span>
                <span class="text-muted" style="font-size:12px; margin-left:8px;">
                    {{ $routine->tasks_count }} task{{ $routine->tasks_count !== 1 ? 's' : '' }}
                </span>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-outline"
                        onclick="openAssignModal({{ $routine->routine_id }}, '{{ addslashes($routine->name) }}')">
                    Assign
                </button>
                <a href="{{ route('teacher.routines.edit', $routine) }}" class="btn btn-outline">Edit</a>
                <form method="POST" action="{{ route('teacher.routines.destroy', $routine) }}"
                      onsubmit="return confirm('Delete routine &quot;{{ $routine->name }}&quot;? This will also remove all assignments.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>

        {{-- Assignment tags --}}
        @if ($routine->assignments->count())
            <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:4px;">
                @foreach ($routine->assignments as $assignment)
                    <div style="display:inline-flex; align-items:center; gap:6px;
                                font-size:12px; background:#eff6ff; color:#1e40af;
                                border:1px solid #bfdbfe; border-radius:20px;
                                padding:3px 6px 3px 12px;">
                        <span>
                            @if ($assignment->student)
                                {{ $assignment->student->name }}
                            @elseif ($assignment->group)
                                {{ $assignment->group->group_name }} (group)
                            @endif
                            <span style="color:#93c5fd; margin-left:4px;">
                                {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d M') }}
                            </span>
                        </span>
                        <form method="POST"
                              action="{{ route('teacher.routines.assignments.destroy', [$routine->routine_id, $assignment->assignment_id]) }}"
                              onsubmit="return confirm('Remove this assignment?')"
                              style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="background:none; border:none; cursor:pointer;
                                           color:#93c5fd; font-size:15px; font-weight:700;
                                           padding:0 2px; line-height:1;"
                                    title="Remove assignment">&times;</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div style="font-size:12px; color:#9ca3af; margin-top:4px;">Not yet assigned</div>
        @endif
    </div>
@empty
    <div class="empty-state">
        No routines yet. <a href="{{ route('teacher.routines.create') }}">Create your first one</a>.
    </div>
@endforelse

{{-- Assign Modal --}}
<div id="assignModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.4); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:440px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Assign: <span id="assignRoutineName"></span></span>
            <button onclick="closeAssignModal()" class="btn btn-outline">Close</button>
        </div>

        <form method="POST" id="assignForm">
            @csrf

            <div class="form-group mb-3">
                <label>Assign to</label>
                {{-- Increased gap between radio options for clarity --}}
                <div style="display:flex; gap:32px; margin-top:10px;">
                    <label style="display:flex; align-items:center; gap:8px;
                                  cursor:pointer; font-size:14px; font-weight:500;">
                        <input type="radio" name="assign_to" value="student" checked
                               onchange="toggleAssignType('student')"
                               style="width:16px; height:16px; accent-color:#2563eb;">
                        Individual student
                    </label>
                    <label style="display:flex; align-items:center; gap:8px;
                                  cursor:pointer; font-size:14px; font-weight:500;">
                        <input type="radio" name="assign_to" value="group"
                               onchange="toggleAssignType('group')"
                               style="width:16px; height:16px; accent-color:#2563eb;">
                        Group
                    </label>
                </div>
            </div>

            <div class="form-group mb-3" id="studentSelect">
                <label>Student</label>
                <select name="student_id">
                    <option value="">- Select student -</option>
                    @forelse ($students as $student)
                        <option value="{{ $student->user_id }}">
                            {{ $student->name }}{{ $student->diagnosis ? ' (' . $student->diagnosis . ')' : '' }}
                        </option>
                    @empty
                        <option disabled>No students registered yet</option>
                    @endforelse
                </select>
            </div>

            <div class="form-group mb-3" id="groupSelect" style="display:none;">
                <label>Group</label>
                <select name="group_id">
                    <option value="">- Select group -</option>
                    @forelse ($groups as $group)
                        <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                    @empty
                        <option disabled>No groups created yet</option>
                    @endforelse
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Assign</button>
                <button type="button" onclick="closeAssignModal()" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openAssignModal(id, name) {
    document.getElementById('assignRoutineName').textContent = name;
    document.getElementById('assignForm').action = '/teacher/routines/' + id + '/assign';
    // Reset to default (individual student) each time modal opens
    document.querySelector('input[name="assign_to"][value="student"]').checked = true;
    toggleAssignType('student');
    document.getElementById('assignModal').style.display = 'flex';
}
function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}
function toggleAssignType(type) {
    document.getElementById('studentSelect').style.display = type === 'student' ? '' : 'none';
    document.getElementById('groupSelect').style.display   = type === 'group'   ? '' : 'none';
}
</script>
@endsection
