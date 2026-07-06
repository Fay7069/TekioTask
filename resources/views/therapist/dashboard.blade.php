
@extends('layouts.therapist')
@section('title', 'Therapist Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-grid">

    {{-- Welcome card --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Welcome, {{ Auth::user()->name }}</span>
            <a href="{{ route('therapist.case-notes') }}" class="btn btn-primary">+ New Case Note</a>
        </div>
        <p class="text-muted">
            You have <strong>{{ $students->count() }}</strong> student(s) in the system.
            Case notes are confidential and not visible to parents or teachers.
        </p>
    </div>

    {{-- Recent case notes --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Recent Case Notes</span>
            <a href="{{ route('therapist.case-notes') }}" class="btn btn-outline">View All</a>
        </div>

        @forelse ($recentNotes as $note)
            <div style="padding:14px 0;border-bottom:1px solid #f3f4f6;">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <strong style="font-size:14px;">{{ $note->student->name ?? '-' }}</strong>
                    <span class="text-muted" style="font-size:12px;">
                        {{ $note->created_at->format('d M Y, H:i') }}
                    </span>
                </div>
                <p style="font-size:13px;color:#374151;line-height:1.5;">
                    {{ Str::limit($note->content, 140) }}
                </p>
            </div>
        @empty
            <div class="empty-state">No case notes yet. <a href="{{ route('therapist.case-notes') }}">Write your first one</a>.</div>
        @endforelse
    </div>

</div>
@endsection
