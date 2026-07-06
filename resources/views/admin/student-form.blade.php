
@extends('layouts.admin')
@section('title', isset($student) ? 'Edit Student' : 'Add Student')
@section('page-title', isset($student) ? 'Edit Student Profile' : 'Add New Student')

@section('content')
<div style="max-width:560px;">
    <form method="POST"
          action="{{ isset($student) ? route('admin.students.update', $student) : route('admin.students.store') }}">
        @csrf
        @if(isset($student)) @method('PUT') @endif

        @if ($errors->any())
            <div class="alert alert-error mb-3">
                @foreach ($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-header"><span class="card-title">Basic Info</span></div>

            <div class="form-group">
                <label>Full name <span style="color:#dc2626">*</span></label>
                <input type="text" name="name"
                       value="{{ old('name', $student->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Email <span style="color:#dc2626">*</span></label>
                <input type="email" name="email"
                       value="{{ old('email', $student->email ?? '') }}" required>
            </div>
            @if(!isset($student))
            <div class="form-group">
                <label>Password <span style="color:#dc2626">*</span></label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm password <span style="color:#dc2626">*</span></label>
                <input type="password" name="password_confirmation" required>
            </div>
            @else
            <div class="form-group">
                <label>New password <span style="font-weight:400; color:#6b7280;">(leave blank to keep current)</span></label>
                <input type="password" name="password">
            </div>
            <div class="form-group">
                <label>Confirm new password</label>
                <input type="password" name="password_confirmation">
            </div>
            @endif
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" min="2" max="25"
                           value="{{ old('age', $student->age ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Diagnosis</label>
                    <select name="diagnosis">
                        <option value="">— Optional —</option>
                        @foreach (['ADHD','Autism','Both','Other'] as $d)
                            <option value="{{ $d }}"
                                @selected(old('diagnosis', $student->diagnosis ?? '') === $d)>
                                {{ $d }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><span class="card-title">Accessibility Settings</span></div>

            @php $a = $student->accessibility_settings ?? []; @endphp

            <div class="pref-row">
                <label>Large buttons (60×60px min)</label>
                <input type="hidden" name="large_buttons" value="0">
                <input type="checkbox" name="large_buttons" value="1"
                       @checked(old('large_buttons', $a['large_buttons'] ?? false))>
            </div>
            <div class="pref-row">
                <label>High contrast mode</label>
                <input type="hidden" name="high_contrast" value="0">
                <input type="checkbox" name="high_contrast" value="1"
                       @checked(old('high_contrast', $a['high_contrast'] ?? false))>
            </div>
            <div class="pref-row">
                <label>Mute all sounds</label>
                <input type="hidden" name="mute_sounds" value="0">
                <input type="checkbox" name="mute_sounds" value="1"
                       @checked(old('mute_sounds', $a['mute_sounds'] ?? true))>
            </div>
            <div class="form-group" style="margin-top:12px;">
                <label>Text size</label>
                <select name="text_size">
                    @foreach ([14,16,18,20,24] as $size)
                        <option value="{{ $size }}"
                            @selected((int)old('text_size', $a['text_size'] ?? 16) === $size)>
                            {{ $size }}pt
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($student) ? ' Save Changes' : ' Add Student' }}
            </button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
