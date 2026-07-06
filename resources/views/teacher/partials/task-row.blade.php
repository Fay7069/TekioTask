

@php
    $ti          = $ti ?? 0;
    $task        = $task ?? null;
    $isTemplate  = $isTemplate ?? false;
@endphp

<div class="task-row card mb-2" style="border-left: 3px solid #4f46e5;">
    <div class="card-header">
        <span class="card-title" style="font-size:13px;">Task {{ $isTemplate ? '' : $ti + 1 }}</span>
        <button type="button" class="btn btn-danger" onclick="removeTask(this)">✕ Remove</button>
    </div>

    <div style="display:grid; grid-template-columns:1fr auto; gap:12px; margin-bottom:8px;">
        <div class="form-group">
            <label>Task name <span style="color:#dc2626">*</span></label>
            <input type="text"
                   name="tasks[{{ $ti }}][title]"
                   value="{{ old("tasks.{$ti}.title", $task->title ?? '') }}"
                   placeholder="e.g. Brush Teeth" required>
        </div>
        <div class="form-group" style="width:140px;">
            <label>Duration (seconds)</label>
            <input type="number"
                   name="tasks[{{ $ti }}][estimated_duration_seconds]"
                   value="{{ old("tasks.{$ti}.estimated_duration_seconds", $task->estimated_duration_seconds ?? 120) }}"
                   min="10" max="3600" required>
        </div>
    </div>

    {{-- Micro-steps section --}}
    <div>
        <button type="button" class="btn btn-outline" style="font-size:12px;"
                onclick="toggleMicroSteps(this, {{ $ti }})">
            {{ ($task && $task->microSteps->count()) ? '− Hide Micro Steps' : '+ Add Micro Steps' }}
        </button>

        <div id="micro-area-{{ $ti }}"
             style="{{ ($task && $task->microSteps->count()) ? '' : 'display:none;' }} margin-top:10px;">

            <div id="micro-list-{{ $ti }}">
                @if($task && $task->microSteps->count())
                    @foreach ($task->microSteps as $si => $step)
                        <div class="micro-step-row flex gap-2 mb-2">
                            <input type="text"
                                   name="tasks[{{ $ti }}][micro_steps][{{ $si }}][description]"
                                   value="{{ $step->description }}"
                                   placeholder="Step {{ $si + 1 }} description"
                                   style="flex:1;" required>
                            <button type="button" class="btn btn-danger"
                                    onclick="this.closest('.micro-step-row').remove()">✕</button>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" class="btn btn-outline" style="font-size:12px; margin-top:4px;"
                    onclick="addMicroStep({{ $ti }})">+ Add Step</button>
        </div>
    </div>
</div>
