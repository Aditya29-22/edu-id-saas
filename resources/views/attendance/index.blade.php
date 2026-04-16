@extends('layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Attendance Tracker')

@section('content')
<!-- Summary Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-value">{{ $summary['total'] }}</div>
        <div class="stat-label">Total Records</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value">{{ $summary['on_time'] }}</div>
        <div class="stat-label">On Time</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
        <div class="stat-value">{{ $summary['late'] }}</div>
        <div class="stat-label">Late Arrivals</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-arrow-right-from-bracket"></i></div>
        <div class="stat-value">{{ $summary['exited'] }}</div>
        <div class="stat-label">Exited</div>
    </div>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding:14px 22px;">
        <form method="GET" class="search-bar">
            <div class="form-group" style="margin-bottom:0;">
                <input type="date" name="date" class="form-control" value="{{ $date }}" style="max-width:180px;">
            </div>
            <select name="school_id" class="form-control" style="max-width:200px;">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost btn-sm"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clipboard-check" style="color:#fbbf24;margin-right:8px;"></i> Records for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($records->isEmpty())
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <h4>No attendance records</h4>
                <p>No records found for the selected date</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>School</th>
                        <th>Entry Time</th>
                        <th>Exit Time</th>
                        <th>Late?</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td style="color:var(--text-primary);font-weight:600;">
                            {{ $record->student->first_name ?? '' }} {{ $record->student->last_name ?? '' }}
                            <div style="font-size:11px;color:var(--text-muted);font-weight:400;">Roll: {{ $record->student->roll_number ?? '' }}</div>
                        </td>
                        <td>{{ $record->student->school->name ?? '—' }}</td>
                        <td>{{ $record->entry_time ? \Carbon\Carbon::parse($record->entry_time)->format('h:i A') : '—' }}</td>
                        <td>{{ $record->exit_time ? \Carbon\Carbon::parse($record->exit_time)->format('h:i A') : '—' }}</td>
                        <td>
                            @if($record->is_late)
                                <span class="badge badge-warning">Late</span>
                            @else
                                <span class="badge badge-success">On Time</span>
                            @endif
                        </td>
                        <td>
                            @if($record->status === 'entered')
                                <span class="badge badge-info">In School</span>
                            @elseif($record->status === 'exited')
                                <span class="badge badge-purple">Exited</span>
                            @else
                                <span class="badge badge-danger">Absent</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($records->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }}
                </div>
                <div class="pagination-links">
                    {{ $records->appends(request()->query())->links('pagination.custom') }}
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
