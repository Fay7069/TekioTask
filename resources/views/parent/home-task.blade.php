
@extends('layouts.parent')
@section('title', 'Record Home Task')
@section('page-title', 'Record Home Task')

@section('content')
<div style="max-width:520px;">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $e)<div>- {{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- Record form --}}
    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">Log a Home Task</span>
        </div>

        <form method="POST" action="{{ route('parent.home-task.store') }}">
            @csrf

            @if ($children->count() > 1)
                <div class="form-group">
                    <label>Child</label>
                    <select name="student_id" required>
                        <option value="">- Select child -</option>
                        @foreach ($children as $c)
                            <option value="{{ $c->user_id }}"
                                @selected(old('student_id') == $c->user_id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="student_id"
                       value="{{ $children->first()?->user_id }}">
            @endif

            <div class="form-group">
                <label>Task Name</label>
                <input type="text" name="task_name"
                       value="{{ old('task_name') }}"
                       placeholder="e.g. Brushed teeth before bed" required>
            </div>

            <div class="form-group">
                <label>Date Completed</label>
                <input type="date" name="completed_date"
                       value="{{ old('completed_date', today()->format('Y-m-d')) }}"
                       max="{{ today()->format('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>Notes <span style="font-weight:400; color:#9ca3af;">(optional)</span></label>
                <textarea name="notes" rows="3"
                          placeholder="Any observations about how the task went...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Save Task</button>
                <a href="{{ route('parent.home-task.history') }}" class="btn btn-outline">
                    View History
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
