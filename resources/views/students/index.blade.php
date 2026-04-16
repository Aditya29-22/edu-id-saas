@extends('layouts.app')
@section('title', 'Students')
@section('page-title', 'Students')

@section('header-actions')
    <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Student</a>
@endsection

@section('content')
<!-- Search & Filter -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding:14px 22px;">
        <form method="GET" class="search-bar">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by name or roll number..." value="{{ request('search') }}">
            </div>
            <select name="school_id" class="form-control" style="max-width:200px;">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
            @if(request('search') || request('school_id'))
                <a href="{{ route('students') }}" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Clear</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body" style="padding:0;">
        @if($students->isEmpty())
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h4>No students found</h4>
                <p>Add students to see them here</p>
                <a href="{{ route('students.create') }}" class="btn btn-primary" style="margin-top:16px;"><i class="fas fa-plus"></i> Add Student</a>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Roll No.</th>
                        <th>School</th>
                        <th>Class</th>
                        <th>Guardian</th>
                        <th>Blood Group</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td style="color:var(--text-primary);font-weight:600;">
                            {{ $student->first_name }} {{ $student->last_name }}
                        </td>
                        <td><span class="badge badge-info">{{ $student->roll_number }}</span></td>
                        <td>{{ $student->school->name ?? '—' }}</td>
                        <td>{{ $student->class_name }}{{ $student->section ? '-'.$student->section : '' }}</td>
                        <td>
                            <div>{{ $student->guardian_name ?? '—' }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $student->guardian_phone ?? '' }}</div>
                        </td>
                        <td>{{ $student->blood_group ?? '—' }}</td>
                        <td>
                            @if($student->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($students->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }}
                </div>
                <div class="pagination-links">
                    {{ $students->appends(request()->query())->links('pagination.custom') }}
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
