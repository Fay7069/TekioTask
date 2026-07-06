
@extends('layouts.admin')
@section('title', 'Reports')
@section('page-title', 'Reports & Export')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <span class="card-title">Generate Progress Report</span>
    </div>

    <div class="form-group">
        <label>Student</label>
        <select id="studentSelect">
            <option value="">All Students (Class Report)</option>
            @foreach ($students as $s)
                <option value="{{ $s->user_id }}">{{ $s->name }} ({{ $s->diagnosis ?? 'No diagnosis' }})</option>
            @endforeach
        </select>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
            <label>From</label>
            <input type="date" id="fromDate" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label>To</label>
            <input type="date" id="toDate" value="{{ now()->format('Y-m-d') }}">
        </div>
    </div>

    <button class="btn btn-primary mt-3" onclick="generatePreview()">Generate Preview</button>

    {{-- Preview section --}}
    <div id="previewSection" style="display:none; margin-top:24px; padding-top:24px; border-top:1px solid #e5e7eb;">
        <div class="card-title mb-3">Preview</div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:20px;">
            <div style="text-align:center;">
                <div id="prevTotal" style="font-size:28px; font-weight:500; color:#1e3a5f;">-</div>
                <div class="text-muted" style="font-size:13px;">Total Tasks</div>
            </div>
            <div style="text-align:center;">
                <div id="prevRate" style="font-size:28px; font-weight:500; color:#16a34a;">-</div>
                <div class="text-muted" style="font-size:13px;">Completion Rate</div>
            </div>
            <div style="text-align:center;">
                <div id="prevAdapt" style="font-size:28px; font-weight:500; color:#d97706;">-</div>
                <div class="text-muted" style="font-size:13px;">Adaptations</div>
            </div>
        </div>

        <div class="flex gap-2">
            <a id="csvLink" href="#" class="btn btn-primary">Download CSV</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function generatePreview() {
    const student = document.getElementById('studentSelect').value;
    const from    = document.getElementById('fromDate').value;
    const to      = document.getElementById('toDate').value;

    if (!from || !to) { alert('Please select a date range.'); return; }

    const params = new URLSearchParams({ from, to });
    if (student) params.append('student_id', student);

    const res  = await fetch(`{{ route('admin.reports.preview') }}?${params}`, {
        headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();

    document.getElementById('prevTotal').textContent = data.total;
    document.getElementById('prevRate').textContent  = data.rate + '%';
    document.getElementById('prevAdapt').textContent = data.adaptations;

    const csvUrl = `{{ route('admin.reports.export') }}?${params}`;
    document.getElementById('csvLink').href = csvUrl;

    document.getElementById('previewSection').style.display = '';
}
</script>
@endsection
