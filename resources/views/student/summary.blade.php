
@extends('layouts.student')
@section('title', "All Done! – TekioTask")
@section('page-title', "Today's Summary")

@section('content')
<div style="max-width:500px; margin:0 auto; text-align:center;">

    <div class="card" style="padding:2rem;">

        <div style="font-size:56px; margin-bottom:12px;">✅</div>
        <h2 style="font-size:22px; font-weight:500; margin-bottom:8px;">
            All done for today!
        </h2>
        <p style="color:#6b7280; margin-bottom:24px;">
            {{ Auth::user()->name }}, you completed all your tasks. Great work!
        </p>

        <div style="text-align:left; margin-bottom:24px;">
            <div style="font-size:13px; font-weight:500; color:#374151; margin-bottom:10px;">
                Today's tasks:
            </div>

            @forelse($taskSummary as $t)
            <div style="display:flex; justify-content:space-between; align-items:center;
                        padding:8px 0; border-bottom:0.5px solid #e5e7eb; font-size:13px;">
                <span>
                    @if($t->status === 'completed') ✅
                    @elseif($t->status === 'skipped') ⏭
                    @elseif($t->status === 'failed') ❌
                    @else ⏳
                    @endif
                    {{ $t->title }}
                </span>
                <span style="color:#16a34a; font-weight:500;">
                    @if($t->points > 0) +{{ $t->points }} pts @endif
                </span>
            </div>
            @empty
            <p style="color:#6b7280; font-size:13px; text-align:center;">
                No tasks recorded today.
            </p>
            @endforelse
        </div>

        <div style="background:#EAF3DE; border-radius:8px; padding:12px; margin-bottom:20px;">
            <span style="font-size:14px; color:#3B6D11; font-weight:500;">
                Points earned today: +{{ $totalPoints }} pts
            </span>
        </div>

        <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-full btn-lg">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
