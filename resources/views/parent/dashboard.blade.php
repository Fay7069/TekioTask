
@extends('layouts.parent')
@section('title', 'Parent Dashboard')
@section('page-title', "My Child's Progress")

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="dashboard-grid">

    {{-- Child selector --}}
    @if ($children->count() > 1)
        <div class="card col-full">
            <div class="card-header">
                <span class="card-title">Viewing progress for</span>
            </div>
            <div class="flex gap-2" style="flex-wrap:wrap;">
                @foreach ($children as $c)
                    <a href="{{ route('parent.switch-child', $c->user_id) }}"
                       class="btn {{ $child?->user_id === $c->user_id ? 'btn-primary' : 'btn-outline' }}">
                        {{ $c->name }}
                        @if($c->diagnosis) ({{ $c->diagnosis }}) @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if (!$child)
        <div class="card col-full">
            <div class="empty-state">
                No children linked to your account yet. Please contact the administrator.
            </div>
        </div>
    @else

    {{-- Today's completion status --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Today's Routine — {{ $child->name }}</span>
        </div>

        @if ($todayTotal > 0)
            @php $pct = round(($todayCompleted / $todayTotal) * 100); @endphp
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <div style="font-size:15px; font-weight:600; color:#1e3a5f; margin-bottom:4px;">
                        {{ $todayCompleted }} of {{ $todayTotal }} tasks completed today
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                <span class="status-pill {{ $todayCompleted >= $todayTotal ? 'complete' : 'progress' }}">
                    {{ $pct }}%
                </span>
            </div>
        @else
            <p class="text-muted">No routine data for today yet.</p>
        @endif
    </div>

    {{-- Weekly chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">This Week</span>
        </div>
        <div class="chart-wrap">
            @foreach ($weeklyProgress as $i => $pct)
                <div class="bar-col">
                    <div class="bar {{ $pct == 0 ? 'empty' : '' }}"
                         style="height:{{ max($pct, 4) }}px"
                         title="{{ $weeklyLabels[$i] }}: {{ $pct }}%"></div>
                    <span class="bar-label">{{ substr($weeklyLabels[$i], 0, 1) }}</span>
                </div>
            @endforeach
        </div>
        @php $goodDays = count(array_filter($weeklyProgress, fn($v) => $v >= 80)); @endphp
        <div class="chart-summary">{{ $goodDays }} great day{{ $goodDays !== 1 ? 's' : '' }} this week</div>
        <div class="text-muted" style="font-size:11px; margin-top:2px;">A great day is 80% or more of that day's tasks completed</div>
    </div>

    {{-- Actions --}}
    <div class="card" style="display:flex; flex-direction:column; gap:12px;">
        <div class="card-header">
            <span class="card-title">Actions</span>
        </div>
        <button class="btn btn-primary btn-full" onclick="openCommentModal()">
            Add Comment
        </button>
        <a href="{{ route('parent.home-task') }}" class="btn btn-outline btn-full">
            Record Home Task
        </a>
        <a href="{{ route('parent.home-task.history') }}" class="btn btn-outline btn-full">
            View Home Task History
        </a>
    </div>

    {{-- Task history --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Task History</span>
        </div>
        @if ($taskHistory->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Micro-steps used</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskHistory as $log)
                            <tr>
                                <td style="font-size:12px;">
                                    {{-- attempt_timestamp is an Eloquent Carbon cast — safe to ->format() --}}
                                    {{ $log->attempt_timestamp->format('d M Y, H:i') }}
                                </td>
                                <td>{{ $log->task->title ?? '-' }}</td>
                                <td>
                                    @if($log->status === 'completed')
                                        <span class="status-pill complete">Completed</span>
                                    @elseif($log->status === 'failed')
                                        <span class="status-pill failed">Failed</span>
                                    @else
                                        <span class="status-pill skipped">Skipped</span>
                                    @endif
                                </td>
                                <td>{{ $log->was_adapted ? 'Yes' : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">No task history yet.</div>
        @endif
    </div>

    {{-- Recent comments --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">My Comments</span>
        </div>
        @forelse ($recentComments as $comment)
            <div style="display:flex; justify-content:space-between; align-items:flex-start;
                        padding:12px 0; border-bottom:1px solid #f3f4f6;">
                <div style="flex:1;">
                    <p style="font-size:13px; color:#374151; line-height:1.5;">
                        {{ $comment->comment_text }}
                    </p>
                    <span class="text-muted" style="font-size:11px;">
                        {{ \Carbon\Carbon::parse($comment->created_at)->format('d M Y, H:i') }}
                    </span>
                </div>
                <form method="POST"
                      action="{{ route('parent.comment.delete', $comment->comment_id) }}"
                      onsubmit="return confirm('Delete this comment?')"
                      style="margin-left:12px;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            style="font-size:11px; padding:3px 8px;">Delete</button>
                </form>
            </div>
        @empty
            <p class="text-muted">No comments yet.</p>
        @endforelse
    </div>

    {{-- Recent home tasks --}}
    <div class="card col-full">
        <div class="card-header">
            <span class="card-title">Recent Home Tasks</span>
            <a href="{{ route('parent.home-task.history') }}" class="btn btn-outline"
               style="font-size:12px; padding:4px 10px;">View All</a>
        </div>
        @forelse ($recentHomeTasks as $ht)
            <div style="padding:10px 0; border-bottom:1px solid #f3f4f6; font-size:13px;">
                <strong>{{ $ht->task_name }}</strong>
                <span class="text-muted" style="margin-left:10px;">
                    {{ \Carbon\Carbon::parse($ht->completed_date)->format('d M Y') }}
                </span>
            </div>
        @empty
            <p class="text-muted">No home tasks recorded yet.</p>
        @endforelse
    </div>

    @endif

</div>

{{-- Add Comment Modal --}}
<div id="commentModal" style="display:none; position:fixed; inset:0;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div class="card" style="max-width:440px; width:90%; margin:auto;">
        <div class="card-header">
            <span class="card-title">Add Comment</span>
            <button onclick="closeCommentModal()" class="btn btn-outline">Close</button>
        </div>
        <form method="POST" action="{{ route('parent.comment.store') }}">
            @csrf
            <input type="hidden" name="student_id" value="{{ $child?->user_id }}">
            <div class="form-group">
                <label>Note about {{ $child?->name }}'s behaviour at home</label>
                <textarea name="comment_text" rows="4" required
                          placeholder="e.g. Completed breakfast without prompting this morning."></textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Save Comment</button>
                <button type="button" onclick="closeCommentModal()" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openCommentModal()  { document.getElementById('commentModal').style.display = 'flex'; }
function closeCommentModal() { document.getElementById('commentModal').style.display = 'none'; }
</script>
@endsection
