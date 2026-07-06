
@extends('layouts.teacher')
@section('title', 'Students')
@section('page-title', 'Students & Groups')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="dashboard-grid">

    {{-- All students --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">All Students</span>
            <button class="btn btn-primary" onclick="openCreateGroupModal()">+ Create Group</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Diagnosis</th>
                        <th>Groups</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td><strong>{{ $student->name }}</strong></td>
                            <td>{{ $student->diagnosis ?? '-' }}</td>
                            <td style="font-size:12px; color:#6b7280;">
                                {{ $student->groups->pluck('group_name')->join(', ') ?: '-' }}
                            </td>
                            <td>
                                <button class="btn btn-outline"
                                        style="font-size:12px; padding:4px 10px;"
                                        onclick="openAddToGroupModal({{ $student->user_id }}, '{{ addslashes($student->name) }}')">
                                    Add to Group
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:#9ca3af; padding:20px;">
                                No students in the system yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Groups with members and remove buttons --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">My Groups</span>
        </div>

        @forelse ($groups as $group)
            <div style="padding:14px 0; border-bottom:1px solid #f3f4f6;">
                <div class="card-header" style="margin-bottom:10px;">
                    <strong>{{ $group->group_name }}</strong>
                    <form method="POST"
                          action="{{ route('teacher.groups.destroy', $group->group_id) }}"
                          onsubmit="return confirm('Delete this group and remove all members?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                style="font-size:12px; padding:3px 8px;">Delete Group</button>
                    </form>
                </div>

                @if($group->members->count())
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @foreach($group->members as $member)
                            <div style="display:flex; align-items:center; gap:6px;
                                        background:#f3f4f6; border-radius:20px;
                                        padding:4px 12px; font-size:13px;">
                                <span>{{ $member->name }}</span>
                                <form method="POST"
                                      action="{{ route('teacher.groups.remove-member', [$group->group_id, $member->user_id]) }}"
                                      onsubmit="return confirm('Remove {{ $member->name }} from {{ $group->group_name }}?')"
                                      style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="background:none; border:none; color:#dc2626;
                                                   cursor:pointer; font-size:14px; font-weight:700;
                                                   padding:0 2px; line-height:1;">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted" style="font-size:13px;">No members yet.</p>
                @endif
            </div>
        @empty
            <p class="text-muted">No groups yet. Create one above.</p>
        @endforelse
    </div>

</div>

{{-- Create Group Modal --}}
<div id="createGroupModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:400px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Create Group</span>
            <button onclick="closeCreateGroupModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" action="{{ route('teacher.groups.store') }}">
            @csrf
            <div class="form-group">
                <label>Group Name</label>
                <input type="text" name="group_name" placeholder="e.g. EIP Class A" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Create Group</button>
        </form>
    </div>
</div>

{{-- Add to Group Modal --}}
<div id="addToGroupModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:400px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Add <span id="addStudentName"></span> to Group</span>
            <button onclick="closeAddToGroupModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" id="addToGroupForm" action="">
            @csrf
            <div class="form-group">
                <label>Select Group</label>
                <select name="group_id" required>
                    <option value="">- Select group -</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Add to Group</button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openCreateGroupModal()  { document.getElementById('createGroupModal').style.display = 'flex'; }
function closeCreateGroupModal() { document.getElementById('createGroupModal').style.display = 'none'; }

function openAddToGroupModal(id, name) {
    document.getElementById('addStudentName').textContent = name;
    document.getElementById('addToGroupForm').action = '/teacher/groups/' + id + '/add-member';
    document.getElementById('addToGroupModal').style.display = 'flex';
}
function closeAddToGroupModal() { document.getElementById('addToGroupModal').style.display = 'none'; }
</script>
@endsection
