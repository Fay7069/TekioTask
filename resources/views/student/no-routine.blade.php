
@extends('layouts.student')
@section('title', 'No Routine – TekioTask')
@section('page-title', 'Active Task')

@section('content')
<div style="max-width:480px; margin:0 auto; text-align:center; padding-top:40px;">
    <div class="card">
        <div style="font-size:64px; margin-bottom:16px;">📭</div>
        <h2 style="font-size:20px; font-weight:700; color:#1e3a5f; margin-bottom:8px;">
            No active task right now
        </h2>
        <p style="color:#6b7280; font-size:14px; line-height:1.6; margin-bottom:24px;">
            Your teacher hasn't started a routine yet today.
            Sit tight — you'll be notified when it's time to begin!
        </p>
        <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-full">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection
