
@extends('layouts.student')
@section('title', 'My Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-grid">

    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Today's Routine</span>
        </div>

        @if ($hasRoutine && $totalTasks > 0)

            @php $pct = $totalTasks > 0 ? round(($completedToday / $totalTasks) * 100) : 0; @endphp
            <div style="font-size:13px; color:#6b7280; margin-bottom:8px;">
                {{ $completedToday }} of {{ $totalTasks }} tasks completed
            </div>
            <div class="progress-bar-wrap" style="margin-bottom:20px;">
                <div class="progress-bar-fill" style="width:{{ $pct }}%"></div>
            </div>

            <div style="margin-bottom:20px;">
                @foreach ($tasks as $index => $task)
                    @php
                        $isDone    = $completedTaskIds->contains($task->task_id);
                        $isSkipped = $skippedTaskIds->contains($task->task_id);
                        $finished  = $isDone || $isSkipped;
                        $current   = !$finished && $index === $completedToday;
                    @endphp
                    <div style="display:flex; align-items:center; gap:12px;
                                padding:10px 14px; margin-bottom:6px; border-radius:8px;
                                background: {{ $isDone ? '#f0fdf4' : ($isSkipped ? '#f9fafb' : ($current ? '#eff6ff' : '#f9fafb')) }};
                                border: 1px solid {{ $isDone ? '#bbf7d0' : ($isSkipped ? '#e5e7eb' : ($current ? '#bfdbfe' : '#e5e7eb')) }};">
                        <div style="width:24px; height:24px; border-radius:50%; flex-shrink:0;
                                    display:flex; align-items:center; justify-content:center;
                                    background: {{ $isDone ? '#16a34a' : ($isSkipped ? '#9ca3af' : ($current ? '#2563eb' : '#e5e7eb')) }};
                                    color: {{ ($isDone || $isSkipped || $current) ? '#fff' : '#9ca3af' }};
                                    font-size:12px; font-weight:700;">
                            @if($isDone) &#10003;
                            @elseif($isSkipped) &#8594;
                            @else {{ $index + 1 }}
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px;
                                        font-weight:{{ $current ? '600' : '400' }};
                                        color:{{ $finished ? '#6b7280' : '#1e3a5f' }};
                                        text-decoration:{{ $isDone ? 'line-through' : 'none' }};">
                                {{ $task->title }}
                                @if($isSkipped)
                                    <span style="font-size:11px; color:#9ca3af; font-weight:400;">(skipped)</span>
                                @endif
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">
                                {{ floor($task->estimated_duration_seconds / 60) }}m
                                @if($task->estimated_duration_seconds % 60 > 0)
                                    {{ $task->estimated_duration_seconds % 60 }}s
                                @endif
                                @if($task->has_micro_steps)
                                    &nbsp;&middot;&nbsp;Has visual steps
                                @endif
                            </div>
                        </div>
                        @if($current)
                            <span style="font-size:11px; font-weight:600; color:#2563eb;
                                         background:#dbeafe; padding:2px 8px; border-radius:12px;">
                                Current
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($completedToday >= $totalTasks)
                <div class="alert alert-success mb-3">All tasks completed for today. Great job!</div>
                <a href="{{ route('student.summary') }}" class="btn btn-primary btn-lg">View Summary</a>
            @else
                <a href="{{ route('student.routine') }}" class="btn btn-primary btn-lg">
                    {{ $completedToday > 0 ? 'Continue Routine' : 'Start Routine' }}
                </a>
            @endif

        @else
            <div class="empty-state">
                <p>No routine assigned for today.</p>
                <p class="text-muted" style="margin-top:8px;">
                    Your teacher hasn't assigned a routine yet. Check back later.
                </p>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">My Rewards</span>
        </div>
        <div class="points-big">{{ $totalPoints }}</div>
        <div class="points-label">pts</div>
        @if (count($badges) > 0)
            <p style="font-size:13px; font-weight:600; color:#374151; margin:12px 0 10px;">My Badges</p>
            <div class="badges-row">
                @foreach ($badges as $badgeName)
                    <div class="badge-item">
                        <div class="badge-icon">{{ strtoupper(substr($badgeName, 0, 1)) }}</div>
                        <div class="badge-name">{{ $badgeName }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted" style="margin-top:12px;">Complete tasks to earn badges!</p>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Weekly Progress</span>
        </div>
        @php $days = ['M','T','W','T','F','S','S']; @endphp
        <div class="chart-wrap">
            @foreach ($weeklyProgress as $i => $pct)
                <div class="bar-col">
                    <div class="bar {{ $pct == 0 ? 'empty' : '' }}"
                         style="height:{{ max($pct, 4) }}px" title="{{ $pct }}%"></div>
                    <span class="bar-label">{{ $days[$i] }}</span>
                </div>
            @endforeach
        </div>
        @php $goodDays = count(array_filter($weeklyProgress, fn($v) => $v >= 80)); @endphp
        <div class="chart-summary">
            {{ $goodDays }} great day{{ $goodDays !== 1 ? 's' : '' }} this week
        </div>
    </div>

    <div class="motivation-banner col-full">
        <span class="motivation-text">{{ $motivational }}</span>
    </div>

</div>
@endsection
