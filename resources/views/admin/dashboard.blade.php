
@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="dashboard-grid">

    {{-- Attendance --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Today's Attendance — {{ now()->format('d M Y') }}</span>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.create') }}" class="btn btn-outline">+ Student</a>
            </div>
        </div>

        <div style="font-size:32px; font-weight:800; color:#1e3a5f; margin-bottom:4px;">
            <span id="presentCount">{{ $present }}</span> / <span id="totalCount">{{ $total }}</span>
        </div>
        <div class="text-muted mb-4">students present today</div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        @php $checkedIn = isset($checkins[$student->user_id]); @endphp
                        <tr id="row-{{ $student->user_id }}">
                            <td><strong>{{ $student->name }}</strong></td>
                            <td>{{ $student->diagnosis ?? '-' }}</td>
                            <td>
                                <span class="status-pill {{ $checkedIn ? 'complete' : 'failed' }}"
                                      id="status-{{ $student->user_id }}">
                                    {{ $checkedIn ? 'Present' : 'Absent' }}
                                </span>
                            </td>
                            <td style="font-size:12px; color:#6b7280;" id="time-{{ $student->user_id }}">
                                {{ $checkedIn ? $checkins[$student->user_id] : '-' }}
                            </td>
                            <td>
                                @if (!$checkedIn)
                                    <button class="btn btn-outline"
                                            id="btn-{{ $student->user_id }}"
                                            onclick="checkIn({{ $student->user_id }})"
                                            style="font-size:12px; padding:4px 12px;">
                                        Check In
                                    </button>
                                @else
                                    <span id="btn-{{ $student->user_id }}"
                                          style="font-size:12px; color:#16a34a;">Checked in</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:#9ca3af; padding:20px;">
                                No students yet. <a href="{{ route('admin.students.create') }}">Add one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Quick Actions</span>
        </div>
        <div class="flex gap-2" style="flex-wrap:wrap;">
            <a href="{{ route('admin.students.index') }}" class="btn btn-primary">Manage Students</a>
            <a href="{{ route('admin.users.index') }}"    class="btn btn-outline">Manage Staff</a>
            <a href="{{ route('admin.reports') }}"        class="btn btn-outline">Reports</a>
            <a href="{{ route('profile.index') }}"        class="btn btn-outline">My Profile</a>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function checkIn(studentId) {
    const btn = document.getElementById('btn-' + studentId);
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const res  = await fetch('/admin/checkin/' + studentId, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();

    if (data.status === 'ok') {
        document.getElementById('status-' + studentId).className = 'status-pill complete';
        document.getElementById('status-' + studentId).textContent = 'Present';
        document.getElementById('time-' + studentId).textContent = data.time;
        btn.outerHTML = '<span id="btn-' + studentId + '" style="font-size:12px;color:#16a34a;">Checked in</span>';
        document.getElementById('presentCount').textContent = data.present;
        document.getElementById('totalCount').textContent   = data.total;
    }
}
</script>
@endsection
