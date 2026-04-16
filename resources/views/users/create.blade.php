@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Add New User')

@section('content')
<div style="max-width:580px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-plus" style="color:#818cf8;margin-right:8px;"></i> User Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="user@example.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="role" class="form-control" required>
                            <option value="">Select Role</option>
                            @foreach(['super_admin','school_admin','teacher','student','security_guard'] as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$role)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>School</label>
                        <select name="school_id" class="form-control">
                            <option value="">None (Super Admin)</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="+91 9876543210" value="{{ old('phone') }}">
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Create User</button>
                    <a href="{{ route('users') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
