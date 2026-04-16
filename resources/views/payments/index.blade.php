@extends('layouts.app')
@section('title', 'Payment History')
@section('page-title', 'Payment History')

@section('header-actions')
    <a href="{{ route('payments.checkout') }}" class="btn btn-primary"><i class="fas fa-credit-card"></i> New Payment</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-indian-rupee-sign" style="color:var(--gold-dark);margin-right:8px;"></i> Transactions</h3>
    </div>
    <div class="card-body" style="padding:0;">
        @if($payments->isEmpty())
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h4>No payments recorded</h4>
                <p>Payments will appear here once processed</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>School</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td style="font-family:monospace;font-size:12px;color:var(--text-primary);">{{ $payment->razorpay_payment_id ?? $payment->razorpay_order_id }}</td>
                        <td style="font-weight:600;">{{ $payment->school->name ?? '—' }}</td>
                        <td><span class="badge badge-gold">{{ $payment->subscription->plan->name ?? '—' }}</span></td>
                        <td style="font-weight:700;color:var(--text-primary);">₹{{ number_format($payment->amount) }}</td>
                        <td>{{ ucfirst($payment->method ?? 'card') }}</td>
                        <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y h:i A') : '—' }}</td>
                        <td>
                            @if($payment->status === 'captured')
                                <span class="badge badge-success">Captured</span>
                            @elseif($payment->status === 'failed')
                                <span class="badge badge-danger">Failed</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($payment->status) }}</span>
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
