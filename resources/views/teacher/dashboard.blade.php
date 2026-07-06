
@extends('layouts.teacher')
@section('title', 'Teacher Dashboard')
@section('page-title', 'Class Dashboard')

@section('content')

<div id="alertContainer"></div>

<div class="stats-row" id="statsRow">
    <div class="stat-chip done"><span id="statDone">0</span> Done</div>
    <div class="stat-chip progress"><span id="statProgress">0</span> In Progress</div>
    <div class="stat-chip stuck"><span id="statStuck">0</span> Stuck</div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Visual Routine Map</span>
        <span id="lastUpdated" style="font-size:11px; color:#6b7280;"></span>
    </div>
    <div class="student-grid" id="studentGrid">
        <div style="color:#6b7280; font-size:13px; padding:20px;">Loading student statuses...</div>
    </div>
</div>

<div id="studentModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:400px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title" id="modalName">Student</span>
            <button onclick="closeModal()" class="btn btn-outline">Close</button>
        </div>
        <p style="font-size:14px; color:#6b7280; margin-bottom:8px;">
            Current task: <strong id="modalTask">-</strong>
        </p>
        <p style="font-size:14px; color:#dc2626; margin-bottom:16px;">
            Consecutive failures: <strong id="modalFailures">0</strong>
        </p>
        <button class="btn btn-danger btn-full" onclick="skipTask()">Skip This Task</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentStudent = null;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function fetchMapStatus() {
    try {
        const res  = await fetch('{{ route("teacher.map.status") }}', {
            headers: { 'Accept': 'application/json' }
        });
        if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }
        const data = await res.json();
        renderMap(data);
        document.getElementById('lastUpdated').textContent =
            'Updated ' + new Date().toLocaleTimeString();
    } catch(e) { console.error('Map error:', e); }
}

function renderMap(students) {
    const grid = document.getElementById('studentGrid');
    let done = 0, progress = 0, stuck = 0;

    if (!students.length) {
        grid.innerHTML = '<div style="color:#6b7280;font-size:13px;padding:20px;">No students assigned today.</div>';
        return;
    }

    grid.innerHTML = students.map(s => {
        let css = 'progress';
        // Stuck (red) only once the student has hit the adaptive threshold (2 fails / microsteps)
        if (s.status === 'completed')   { css = 'done';     done++; }
        else if (s.failures >= 2)       { css = 'stuck';    stuck++; }
        else                            { css = 'progress'; progress++; }

        const taskId   = s.current_task_id || 0;
        // Skip button shows once the student has clicked Need Help at least once (failures >= 1)
        const canSkip  = taskId > 0 && s.status !== 'completed' && s.failures >= 1;
        const skipBtn  = canSkip
            ? `<button class="tile-skip-btn"
                       onclick="event.stopPropagation(); quickSkip(${s.user_id}, ${taskId}, '${esc(s.name)}', '${esc(s.current_task)}')">
                   Skip Task
               </button>`
            : '';

        return `<div class="student-tile ${css}"
                     onclick="openModal(${s.user_id},'${esc(s.name)}','${esc(s.current_task)}',${s.failures},${taskId})">
                    <div class="tile-avatar">${s.name.charAt(0).toUpperCase()}</div>
                    <div class="tile-name">${esc(s.name)}</div>
                    <div class="tile-status">${esc(s.current_task)}</div>
                    ${skipBtn}
                </div>`;
    }).join('');

    document.getElementById('statDone').textContent     = done;
    document.getElementById('statProgress').textContent = progress;
    document.getElementById('statStuck').textContent    = stuck;
}

async function fetchAlerts() {
    try {
        const res  = await fetch('{{ route("teacher.notifications.unread") }}', {
            headers: { 'Accept': 'application/json' }
        });
        if (res.status === 401) return;
        const data = await res.json();
        const container = document.getElementById('alertContainer');
        container.innerHTML = data.map(a => `
            <div class="alert-banner" id="alert-${a.notification_id}">
                <span class="alert-text">${esc(a.message)}</span>
                <button class="btn btn-outline" onclick="dismissAlert(${a.notification_id})">Dismiss</button>
            </div>`).join('');
    } catch(e) {}
}

async function dismissAlert(id) {
    await fetch(`/teacher/notifications/${id}/read`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    document.getElementById('alert-' + id)?.remove();
}

function openModal(userId, name, task, failures, taskId) {
    currentStudent = { userId, task, taskId };
    document.getElementById('modalName').textContent     = name;
    document.getElementById('modalTask').textContent     = task;
    document.getElementById('modalFailures').textContent = failures;
    document.getElementById('studentModal').style.display = 'flex';
}
function closeModal() { document.getElementById('studentModal').style.display = 'none'; }

async function quickSkip(userId, taskId, studentName, taskName) {
    if (!confirm('Skip "' + taskName + '" for ' + studentName + '?')) return;
    await doSkip(userId, taskId);
}

async function skipTask() {
    if (!currentStudent) return;
    if (!currentStudent.taskId) {
        alert('No active task found for this student.');
        return;
    }
    if (!confirm('Skip "' + currentStudent.task + '" for this student?')) return;
    await doSkip(currentStudent.userId, currentStudent.taskId);
    closeModal();
}

async function doSkip(userId, taskId) {
    try {
        const res = await fetch('{{ route("teacher.task.skip") }}', {
            method: 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CSRF,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ student_id: userId, task_id: taskId }),
        });

        if (res.ok) {
            showToast('Task skipped successfully.');
        } else {
            const body = await res.text();
            console.error('Skip failed:', res.status, body.substring(0, 300));
            showToast('Skip failed. Check console.', true);
        }
    } catch(e) {
        console.error('Skip network error:', e);
        showToast('Connection error.', true);
    }
    fetchMapStatus();
}

function showToast(msg, isError = false) {
    const existing = document.getElementById('skip-toast');
    if (existing) existing.remove();

    const el = document.createElement('div');
    el.id = 'skip-toast';
    el.textContent = msg;
    el.style.cssText =
        'position:fixed; bottom:24px; left:50%; transform:translateX(-50%);' +
        'background:' + (isError ? '#dc2626' : '#16a34a') + '; color:#fff;' +
        'padding:12px 28px; border-radius:8px; font-size:14px; font-weight:500;' +
        'z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.15);';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

function esc(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;')
        .replace(/'/g,'&#039;');
}

fetchMapStatus();
fetchAlerts();
setInterval(fetchMapStatus, 10000);
setInterval(fetchAlerts,    10000);
</script>

<style>
.tile-skip-btn {
    margin-top: 8px;
    width: 100%;
    padding: 6px 0;
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}
.tile-skip-btn:hover { background: #b91c1c; }
</style>
@endsection
