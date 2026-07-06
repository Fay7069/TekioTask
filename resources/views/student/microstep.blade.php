
@extends('layouts.student')
@section('title', "Let's Break It Down – TekioTask")
@section('page-title', 'Micro Steps')

@section('content')
<div style="max-width: 500px; margin: 0 auto; padding: 24px 16px;">

    @if(!$task)
        <div style="text-align:center; color:#6b7280; padding:40px 0;">
            <p>No active task found. <a href="{{ route('student.routine') }}">Back to routine</a></p>
        </div>
    @else

    <div style="text-align:center; margin-bottom: 28px;">
        <h2 style="font-size:22px; font-weight:700; color:#1e3a5f; margin-bottom:6px;">
            Let's break it down
        </h2>
        <p style="font-size:14px; color:#6b7280;">
            {{ $task->title }} &mdash; follow each step below
        </p>
    </div>

    <div id="stepList" style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
        @forelse($microSteps->sortBy('step_order') as $step)
        <label for="s{{ $step->step_id }}"
               class="microstep-item"
               style="display:flex; align-items:center; gap:14px; background:#fff;
                      border:2px solid #e5e7eb; border-radius:12px; padding:16px 18px;
                      cursor:pointer; transition:border-color 0.2s, background 0.2s;">
            <input type="checkbox" id="s{{ $step->step_id }}"
                   onchange="updateProgress()"
                   style="width:20px; height:20px; accent-color:#2563eb; cursor:pointer; flex-shrink:0;">
            <span style="font-size:15px; color:#1e3a5f; font-weight:500;">
                {{ $step->description }}
            </span>
        </label>
        @empty
        <p style="color:#6b7280; text-align:center; font-size:14px;">
            No micro-steps found for this task.
        </p>
        @endforelse
    </div>

    <div id="stepProgress"
         style="text-align:center; font-size:13px; color:#6b7280; margin-bottom:16px;">
        Step 0 of {{ $microSteps->count() }}
    </div>

    <button id="finishBtn" onclick="finishTask()" disabled
            style="width:100%; padding:16px; font-size:16px; font-weight:700;
                   border-radius:12px; border:none; background:#16a34a; color:#fff;
                   cursor:not-allowed; opacity:0.5; margin-bottom:12px;
                   transition: opacity 0.2s;">
        Finish
    </button>

    <a href="{{ route('student.routine') }}"
       style="display:block; width:100%; text-align:center; padding:14px;
              border:2px solid #d1d5db; border-radius:12px; color:#6b7280;
              font-size:14px; font-weight:500; text-decoration:none;">
        Back to task
    </a>

    @endif

</div>
@endsection

@section('scripts')
<script>
const finishBtn = document.getElementById('finishBtn');
const stepProg  = document.getElementById('stepProgress');

function updateProgress() {
    const all   = document.querySelectorAll('#stepList input[type="checkbox"]');
    const done  = [...all].filter(cb => cb.checked).length;
    const total = all.length;

    stepProg.textContent = 'Step ' + done + ' of ' + total;

    all.forEach(cb => {
        const row = cb.closest('.microstep-item');
        if (cb.checked) {
            row.style.borderColor = '#2563eb';
            row.style.background  = '#eff6ff';
        } else {
            row.style.borderColor = '#e5e7eb';
            row.style.background  = '#fff';
        }
    });

    if (done >= total && total > 0) {
        finishBtn.disabled      = false;
        finishBtn.style.cursor  = 'pointer';
        finishBtn.style.opacity = '1';
    } else {
        finishBtn.disabled      = true;
        finishBtn.style.cursor  = 'not-allowed';
        finishBtn.style.opacity = '0.5';
    }
}

async function finishTask() {
    finishBtn.disabled      = true;
    finishBtn.style.opacity = '0.7';
    finishBtn.textContent   = 'Saving...';

    try {
        const res = await fetch('{{ route("student.task.complete") }}', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({
                task_id            : {{ $task->task_id }},
                time_taken_seconds : 0,
            }),
        });

        if (res.ok) {
            window.location.href = "{{ route('student.routine') }}";
        } else {
            const body = await res.text();
            console.error('Finish error:', res.status, body.substring(0, 400));
            finishBtn.disabled      = false;
            finishBtn.style.opacity = '1';
            finishBtn.textContent   = 'Finish';
        }
    } catch (e) {
        console.error('Finish network error:', e);
        finishBtn.disabled      = false;
        finishBtn.style.opacity = '1';
        finishBtn.textContent   = 'Finish';
    }
}
</script>
@endsection
