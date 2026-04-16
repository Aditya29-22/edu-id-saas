@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-building-columns"></i></div>
        <div class="stat-value">{{ $stats['total_schools'] }}</div>
        <div class="stat-label">Total Schools</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-value">{{ $stats['total_students'] }}</div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="fas fa-clipboard-check"></i></div>
        <div class="stat-value">{{ $stats['today_attendance'] }}</div>
        <div class="stat-label">Today's Attendance</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal"><i class="fas fa-credit-card"></i></div>
        <div class="stat-value">{{ $stats['active_subscriptions'] }}</div>
        <div class="stat-label">Active Subscriptions</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-indian-rupee-sign"></i></div>
        <div class="stat-value">₹{{ number_format($stats['revenue']) }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<!-- Charts & Tables Row -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Attendance Trend -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:#818cf8;margin-right:8px;"></i> Attendance Trend (7 Days)</h3>
        </div>
        <div class="card-body">
            @php $maxCount = max(array_column($attendance_trend, 'count')) ?: 1; @endphp
            <div class="chart-bars">
                @foreach($attendance_trend as $day)
                    <div class="chart-bar-item">
                        <div class="chart-bar-value">{{ $day['count'] }}</div>
                        <div class="chart-bar" style="height: {{ ($day['count'] / $maxCount) * 120 }}px;"></div>
                        <div class="chart-bar-label">{{ $day['date'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Schools -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-building-columns" style="color:#34d399;margin-right:8px;"></i> Recent Schools</h3>
            <a href="{{ route('schools') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="card-body" style="padding:0;">
            @if($recent_schools->isEmpty())
                <div class="empty-state" style="padding:32px;">
                    <i class="fas fa-building-columns"></i>
                    <h4>No schools yet</h4>
                    <p>Add your first school to get started</p>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_schools as $school)
                        <tr>
                            <td style="color:var(--text-primary);font-weight:500;">{{ $school->name }}</td>
                            <td><span class="badge badge-purple">{{ $school->code }}</span></td>
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
            @endif
        </div>
    </div>
</div>

<!-- Recent Students -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-user-graduate" style="color:#60a5fa;margin-right:8px;"></i> Recent Students</h3>
        <a href="{{ route('students') }}" class="btn btn-ghost btn-sm">View All</a>
    </div>
    <div class="card-body" style="padding:0;">
        @if($recent_students->isEmpty())
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h4>No students enrolled</h4>
                <p>Students will appear here once added</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll No.</th>
                        <th>School</th>
                        <th>Class</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_students as $student)
                    <tr>
                        <td style="color:var(--text-primary);font-weight:500;">{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td>{{ $student->roll_number }}</td>
                        <td>{{ $student->school->name ?? '—' }}</td>
                        <td>{{ $student->class_name }}{{ $student->section ? '-'.$student->section : '' }}</td>
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
        @endif
    </div>
</div>
@endsection
