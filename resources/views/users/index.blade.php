@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'Users')

@section('header-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</a>
@endsection

@section('content')
<!-- Search & Filter -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding:14px 22px;">
        <form method="GET" class="search-bar">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <select name="role" class="form-control" style="max-width:180px;">
                <option value="">All Roles</option>
                @foreach(['super_admin','school_admin','teacher','student','security_guard'] as $role)
                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$role)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
            @if(request('search') || request('role'))
                <a href="{{ route('users') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Clear</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body" style="padding:0;">
        @if($users->isEmpty())
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h4>No users found</h4>
                <p>Users will appear here once created</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>School</th>
                        <th>Last Login</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <span style="color:var(--text-primary);font-weight:600;">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @php
                                $roleColors = [
                                    'super_admin' => 'badge-danger',
                                    'school_admin' => 'badge-purple',
                                    'teacher' => 'badge-info',
                                    'student' => 'badge-success',
                                    'security_guard' => 'badge-warning',
                                ];
                            @endphp
                            <span class="badge {{ $roleColors[$u->role] ?? 'badge-info' }}">
                                {{ ucwords(str_replace('_', ' ', $u->role)) }}
                            </span>
                        </td>
                        <td>{{ $u->school->name ?? '—' }}</td>
                        <td style="font-size:12px;">{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}</td>
                        <td>
                            @if($u->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($users->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }}
                </div>
                <div class="pagination-links">
                    {{ $users->appends(request()->query())->links('pagination.custom') }}
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
