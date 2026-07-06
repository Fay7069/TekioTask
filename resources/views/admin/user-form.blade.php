
@extends('layouts.admin')
@section('title', isset($user) ? 'Edit Staff' : 'Add Staff')
@section('page-title', isset($user) ? 'Edit Staff Account' : 'Add Staff Account')

@section('content')
<div style="max-width:520px;">
    <div class="card">
        <form method="POST"
              action="{{ isset($user) ? route('admin.users.update', $user->user_id) : route('admin.users.store') }}">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            @if ($errors->any())
                <div class="alert alert-error mb-3">
                    @foreach($errors->all() as $e)<div>- {{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                       value="{{ old('name', $user->name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="{{ old('email', $user->email ?? '') }}" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="">- Select role -</option>
                    @foreach (['Administrator', 'Teacher', 'Therapist', 'Parent'] as $role)
                        <option value="{{ $role }}"
                            @selected(old('role', $user->role ?? '') === $role)>
                            {{ $role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Password
                    @if(isset($user))
                        <span style="font-weight:400; color:#9ca3af;">(leave blank to keep current)</span>
                    @else
                        <span style="color:#dc2626;">*</span>
                    @endif
                </label>
                <div style="position:relative;">
                    <input type="password" name="password" id="pw1"
                           {{ isset($user) ? '' : 'required' }} minlength="6">
                    <button type="button" onclick="togglePw('pw1', this)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer;
                                   font-size:13px; color:#6b7280;">Show</button>
                </div>
            </div>

            @if(!isset($user))
            <div class="form-group">
                <label>Confirm Password <span style="color:#dc2626;">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="pw2" required>
                    <button type="button" onclick="togglePw('pw2', this)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   background:none; border:none; cursor:pointer;
                                   font-size:13px; color:#6b7280;">Show</button>
                </div>
            </div>
            @endif

            <div class="flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    {{ isset($user) ? 'Save Changes' : 'Create Account' }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePw(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        btn.textContent = 'Hide';
    } else {
        field.type = 'password';
        btn.textContent = 'Show';
    }
}
</script>
@endsection
