
@extends('layouts.student')
@section('title', 'My Rewards')
@section('page-title', 'My Rewards')

@section('content')
<div class="dashboard-grid">

    {{-- Points card --}}
    <div class="card" style="text-align:center;">
        <div class="card-header">
            <span class="card-title">Total Points</span>
        </div>
        <div class="points-big">{{ $totalPoints }}</div>
        <div class="points-label">pts earned</div>

        @php
            $nextBadgeAt = 50;
            $pct = min(round(($totalPoints / $nextBadgeAt) * 100), 100);
        @endphp
        <p style="font-size:12px; color:#6b7280; margin:12px 0 6px;">
            Progress to next reward
        </p>
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" style="width:{{ $pct }}%"></div>
        </div>
        <p class="text-muted">{{ $totalPoints }} / {{ $nextBadgeAt }} pts</p>
    </div>

    {{-- Badges card --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">My Badges</span>
        </div>

        @if (count($badges) > 0)
            <div class="badges-row" style="flex-wrap:wrap; gap:16px;">
                @foreach ($badges as $badgeName)
                    <div class="badge-item">
                        <div class="badge-icon" style="width:56px; height:56px; font-size:20px;">
                            {{ strtoupper(substr($badgeName, 0, 1)) }}
                        </div>
                        <div class="badge-name" style="font-size:12px;">{{ $badgeName }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p>No badges yet!</p>
                <p class="text-muted" style="margin-top:8px;">
                    Complete tasks to earn your first badge.
                </p>
            </div>
        @endif
    </div>

    {{-- How to earn points --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">How to Earn</span>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; text-align:center;">
            <div style="padding:16px; background:#eff6ff; border-radius:10px;">
                <div style="font-size:14px; font-weight:600; color:#1e3a5f;">Complete a task</div>
                <div style="font-size:20px; font-weight:800; color:#2563eb; margin-top:6px;">+10 pts</div>
            </div>
            <div style="padding:16px; background:#f0fdf4; border-radius:10px;">
                <div style="font-size:14px; font-weight:600; color:#1e3a5f;">First task ever</div>
                <div style="font-size:13px; color:#16a34a; font-weight:600; margin-top:6px;">First Task badge</div>
            </div>
            <div style="padding:16px; background:#fefce8; border-radius:10px;">
                <div style="font-size:14px; font-weight:600; color:#1e3a5f;">Complete 10 tasks</div>
                <div style="font-size:13px; color:#d97706; font-weight:600; margin-top:6px;">Ten Done badge</div>
            </div>
        </div>
    </div>

    {{-- Task history --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Completed Task History</span>
        </div>

        @if ($taskHistory->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Task</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskHistory as $log)
                            <tr>
                                <td style="font-size:12px; color:#6b7280;">
                                    {{ $log->attempt_timestamp->format('d M Y, H:i') }}
                                </td>
                                <td>{{ $log->task->title ?? '-' }}</td>
                                <td style="color:#16a34a; font-weight:600;">+10 pts</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">No completed tasks yet. Start your routine!</div>
        @endif
    </div>

</div>
@endsection
