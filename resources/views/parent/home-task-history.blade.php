
@extends('layouts.parent')
@section('title', 'Home Task History')
@section('page-title', 'Home Task History')

@section('content')
<div style="max-width:700px;">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <span class="card-title">Recorded Home Tasks</span>
            <a href="{{ route('parent.home-task') }}" class="btn btn-primary">+ Record New</a>
        </div>

        @if ($homeTasks->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Task</th>
                            <th>Student</th>
                            <th>Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($homeTasks as $ht)
                            <tr>
                                <td style="font-size:12px; color:#6b7280; white-space:nowrap;">
                                    {{ \Carbon\Carbon::parse($ht->completed_date)->format('d M Y') }}
                                </td>
                                <td><strong>{{ $ht->task_name }}</strong></td>
                                <td style="font-size:13px;">{{ $ht->student_name ?? '-' }}</td>
                                <td style="font-size:12px; color:#6b7280;">
                                    {{ $ht->notes ?? '-' }}
                                </td>
                                <td style="white-space:nowrap;">
                                    <form method="POST"
                                          action="{{ route('parent.home-task.delete', $ht->home_task_id) }}"
                                          onsubmit="return confirm('Delete this home task entry?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                                style="font-size:11px; padding:3px 8px;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $homeTasks->links() }}
            </div>
        @else
            <div class="empty-state">
                No home tasks recorded yet.
                <a href="{{ route('parent.home-task') }}" style="margin-left:6px;">Record one now</a>.
            </div>
        @endif
    </div>

</div>
@endsection
