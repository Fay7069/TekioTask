
@extends('layouts.admin')
@section('title', 'Student Profiles')
@section('page-title', 'Student Profiles')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="page-actions">
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">+ Add Student</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Diagnosis</th>
                    <th>Accessibility</th>
                    <th>Email</th>
                    <th>Linked Parent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    @php $a = $student->accessibility_settings ?? []; @endphp
                    <tr>
                        <td><strong>{{ $student->name }}</strong></td>
                        <td>{{ $student->age ?? '-' }}</td>
                        <td>
                            @if($student->diagnosis)
                                <span class="status-pill progress">{{ $student->diagnosis }}</span>
                            @else - @endif
                        </td>
                        <td style="font-size:12px; color:#6b7280;">
                            @if(!empty($a['large_buttons'])) Large &nbsp; @endif
                            @if(!empty($a['high_contrast'])) Contrast &nbsp; @endif
                            @if(!empty($a['mute_sounds']))   Muted @endif
                            @if(empty($a)) - @endif
                        </td>
                        <td style="font-size:13px;">{{ $student->email }}</td>
                        <td style="font-size:12px;">
                            @forelse($student->linked_parents as $parent)
                                <div style="margin-bottom:2px;">
                                    <strong>{{ $parent->name }}</strong><br>
                                    <span style="color:#6b7280;">{{ $parent->email }}</span>
                                </div>
                            @empty
                                <span style="color:#9ca3af;">Not linked</span>
                            @endforelse
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('admin.students.edit', $student->user_id) }}"
                                   class="btn btn-outline" style="font-size:12px; padding:4px 8px;">
                                    Edit
                                </a>
                                <button class="btn btn-outline" style="font-size:12px; padding:4px 8px;"
                                        onclick="openLinkModal({{ $student->user_id }}, '{{ addslashes($student->name) }}')">
                                    Link Parent
                                </button>
                                <form method="POST"
                                      action="{{ route('admin.students.destroy', $student->user_id) }}"
                                      onsubmit="return confirm('Delete {{ $student->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            style="font-size:12px; padding:4px 8px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:#9ca3af; padding:20px;">
                            No students yet. <a href="{{ route('admin.students.create') }}">Add one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Link Parent Modal --}}
<div id="linkModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:420px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Link Parent to <span id="linkStudentName"></span></span>
            <button onclick="closeLinkModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" id="linkForm" action="">
            @csrf
            <div class="form-group">
                <label>Parent Email</label>
                <input type="email" name="parent_email"
                       placeholder="parent@example.com" required>
                <p class="form-hint">The parent must already have an account in the system.</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Link</button>
                <button type="button" onclick="closeLinkModal()" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openLinkModal(id, name) {
    document.getElementById('linkStudentName').textContent = name;
    document.getElementById('linkForm').action = '/admin/students/' + id + '/link-parent';
    document.getElementById('linkModal').style.display = 'flex';
}
function closeLinkModal() {
    document.getElementById('linkModal').style.display = 'none';
}
</script>
@endsection
