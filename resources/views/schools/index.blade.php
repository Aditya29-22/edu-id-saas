@extends('layouts.app')
@section('title', 'Schools')
@section('page-title', 'Schools')

@section('header-actions')
    <a href="{{ route('schools.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add School</a>
@endsection

@section('content')
<!-- Search -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding:14px 22px;">
        <form method="GET" class="search-bar">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-ghost btn-sm">Search</button>
            @if(request('search'))
                <a href="{{ route('schools') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Clear</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body" style="padding:0;">
        @if($schools->isEmpty())
            <div class="empty-state">
                <i class="fas fa-building-columns"></i>
                <h4>No schools found</h4>
                <p>Add your first school to get started</p>
                <a href="{{ route('schools.create') }}" class="btn btn-primary" style="margin-top:16px;"><i class="fas fa-plus"></i> Add School</a>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Code</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Students</th>
                        <th>Users</th>
                        <th>Subscription</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schools as $school)
                    <tr>
                        <td style="color:var(--text-primary);font-weight:600;">
                            <div>{{ $school->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);font-weight:400;">{{ $school->city ?? '' }}{{ $school->state ? ', '.$school->state : '' }}</div>
                        </td>
                        <td><span class="badge badge-purple">{{ $school->code }}</span></td>
                        <td>{{ $school->email }}</td>
                        <td>{{ $school->phone }}</td>
                        <td style="font-weight:600;">{{ $school->students_count }}</td>
                        <td style="font-weight:600;">{{ $school->users_count }}</td>
                        <td>
                            @if($school->subscription_status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($school->subscription_status === 'trial')
                                <span class="badge badge-info">Trial</span>
                            @elseif($school->subscription_status === 'expired')
                                <span class="badge badge-danger">Expired</span>
                            @else
                                <span class="badge badge-warning">None</span>
                            @endif
                        </td>
                        <td>
                            @if($school->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($schools->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $schools->firstItem() }} to {{ $schools->lastItem() }} of {{ $schools->total() }}
                </div>
                <div class="pagination-links">
                    {{ $schools->links('pagination.custom') }}
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
