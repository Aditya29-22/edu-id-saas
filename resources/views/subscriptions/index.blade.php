@extends('layouts.app')
@section('title', 'Subscriptions')
@section('page-title', 'Subscriptions')

@section('header-actions')
    <a href="{{ route('subscriptions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Subscription</a>
@endsection

@section('content')
<!-- Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value">{{ $stats['active'] }}</div>
        <div class="stat-label">Active</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
        <div class="stat-value">{{ $stats['expired'] }}</div>
        <div class="stat-label">Expired</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-value">{{ $stats['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-indian-rupee-sign"></i></div>
        <div class="stat-value">₹{{ number_format($stats['revenue']) }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-credit-card" style="color:var(--gold-dark);margin-right:8px;"></i> All Subscriptions</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($subscriptions->isEmpty())
            <div class="empty-state">
                <i class="fas fa-credit-card"></i>
                <h4>No subscriptions yet</h4>
                <p>Activate subscriptions for schools</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Plan</th>
                        <th>Cycle</th>
                        <th>Amount</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $sub)
                    <tr>
                        <td style="font-weight:600;color:var(--text-primary);">{{ $sub->school->name ?? '—' }}</td>
                        <td><span class="badge badge-gold">{{ $sub->plan->name ?? '—' }}</span></td>
                        <td>{{ ucfirst($sub->billing_cycle) }}</td>
                        <td style="font-weight:700;">₹{{ number_format($sub->amount) }}</td>
                        <td>{{ \Carbon\Carbon::parse($sub->start_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($sub->end_date)->format('M d, Y') }}</td>
                        <td>
                            @if($sub->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($sub->status === 'expired')
                                <span class="badge badge-danger">Expired</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($sub->status) }}</span>
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
