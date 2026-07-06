
@extends('layouts.therapist')
@section('title', 'Case Notes')
@section('page-title', 'Case Notes')

@section('content')
<div style="max-width:700px;">

    @if (session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    {{-- New note form --}}
    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">New Note Entry</span>
        </div>

        <form method="POST" action="{{ route('therapist.case-notes.store') }}">
            @csrf

            @if ($errors->any())
                <div class="alert alert-error mb-3">
                    @foreach ($errors->all() as $e)<div>- {{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="form-group">
                <label for="student_id">Student <span style="color:#dc2626;">*</span></label>
                <select id="student_id" name="student_id" required>
                    <option value="">- Select student -</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->user_id }}"
                            @selected(old('student_id') == $s->user_id)>
                            {{ $s->name }}
                            @if($s->diagnosis) ({{ $s->diagnosis }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="content">Confidential Note <span style="color:#dc2626;">*</span></label>
                <textarea id="content" name="content" rows="5"
                          placeholder="Describe the session, observations, and any recommendations..."
                          required>{{ old('content') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Note</button>
        </form>
    </div>

    {{-- Search + Filter --}}
    <div class="card mb-3" style="padding:16px;">
        <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">

            {{-- Live search box (client-side) --}}
            <div class="form-group" style="flex:1; margin-bottom:0; min-width:180px;">
                <label style="font-size:12px;">Search notes</label>
                <input type="text" id="noteSearch"
                       placeholder="Search by student name or note content..."
                       oninput="filterNotes()"
                       style="width:100%;">
            </div>

            {{-- Server-side filter by student --}}
            <form method="GET" action="{{ route('therapist.case-notes') }}"
                  style="display:flex; gap:8px; align-items:flex-end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:12px;">Filter by student</label>
                    <select name="student_id">
                        <option value="">All students</option>
                        @foreach ($students as $s)
                            <option value="{{ $s->user_id }}"
                                @selected(request('student_id') == $s->user_id)>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-outline">Filter</button>
                <a href="{{ route('therapist.case-notes') }}" class="btn btn-outline">Clear</a>
            </form>
        </div>
    </div>

    {{-- Notes list --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Past Notes</span>
            <span class="text-muted" style="font-size:12px;" id="noteCount">
                {{ $notes->count() }} note(s)
            </span>
        </div>

        <div id="notesList">
            @forelse ($notes as $note)
                <div class="note-row"
                     data-name="{{ strtolower($note->student->name ?? '') }}"
                     data-content="{{ strtolower($note->content) }}"
                     style="padding:16px 0; border-bottom:1px solid #f3f4f6;">
                    <div style="display:flex; justify-content:space-between;
                                align-items:flex-start; margin-bottom:8px;">
                        <div>
                            <strong style="font-size:14px;">
                                {{ $note->student->name ?? '-' }}
                            </strong>
                            <span class="text-muted" style="font-size:12px; margin-left:10px;">
                                {{ $note->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <form method="POST"
                              action="{{ route('therapist.case-notes.delete', $note->note_id) }}"
                              onsubmit="return confirm('Delete this case note?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                    style="font-size:11px; padding:3px 8px;">Delete</button>
                        </form>
                    </div>
                    <p style="font-size:13px; color:#374151; line-height:1.6;
                              white-space:pre-wrap;">{{ $note->content }}</p>
                </div>
            @empty
                <div class="empty-state" id="emptyState">No case notes found.</div>
            @endforelse

            <div id="noMatchState" style="display:none; padding:20px;
                 text-align:center; color:#9ca3af; font-size:13px;">
                No notes match your search.
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function filterNotes() {
    const query = document.getElementById('noteSearch').value.toLowerCase().trim();
    const rows  = document.querySelectorAll('.note-row');
    let visible = 0;

    rows.forEach(row => {
        const name    = row.dataset.name    || '';
        const content = row.dataset.content || '';
        const matches = !query || name.includes(query) || content.includes(query);
        row.style.display = matches ? '' : 'none';
        if (matches) visible++;
    });

    document.getElementById('noteCount').textContent = visible + ' note(s)';
    document.getElementById('noMatchState').style.display =
        (rows.length > 0 && visible === 0) ? '' : 'none';
}
</script>
@endsection
