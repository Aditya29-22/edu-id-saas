@extends('layouts.app')
@section('title', 'Add Student')
@section('page-title', 'Add New Student')

@section('content')
<div style="max-width:680px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-graduate" style="color:#60a5fa;margin-right:8px;"></i> Student Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>School *</label>
                    <select name="school_id" class="form-control" required>
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }} ({{ $school->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Profile Image</label>
                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg" style="padding-top:8px;">
                    <small style="color:var(--text-muted);font-size:11px;">Max 2MB (JPEG, PNG). Will be automatically compressed.</small>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" class="form-control" placeholder="John" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Doe" value="{{ old('last_name') }}" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Roll Number *</label>
                        <input type="text" name="roll_number" class="form-control" placeholder="2024001" value="{{ old('roll_number') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Class *</label>
                        <input type="text" name="class_name" class="form-control" placeholder="10" value="{{ old('class_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <input type="text" name="section" class="form-control" placeholder="A" value="{{ old('section') }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control">
                            <option value="">Select</option>
                            @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Guardian Name</label>
                        <input type="text" name="guardian_name" class="form-control" placeholder="Parent/Guardian" value="{{ old('guardian_name') }}">
                    </div>
                    <div class="form-group">
                        <label>Guardian Phone</label>
                        <input type="text" name="guardian_phone" class="form-control" placeholder="+91 9876543210" value="{{ old('guardian_phone') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Full address" value="{{ old('address') }}">
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Add Student</button>
                    <a href="{{ route('students') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
