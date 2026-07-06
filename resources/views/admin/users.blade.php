
@extends('layouts.admin')
@section('title', 'Staff Management')
@section('page-title', 'Staff Management')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="page-actions">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add Staff</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="status-pill progress">{{ $user->role }}</span>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('admin.users.edit', $user->user_id) }}"
                                   class="btn btn-outline" style="font-size:12px; padding:4px 8px;">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user->user_id) }}"
                                      onsubmit="return confirm('Delete {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            style="font-size:12px; padding:4px 8px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#9ca3af; padding:20px;">
                            No staff accounts yet.
                            <a href="{{ route('admin.users.create') }}">Add the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
