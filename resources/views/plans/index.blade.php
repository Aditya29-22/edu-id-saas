@extends('layouts.app')
@section('title', 'Subscription Plans')
@section('page-title', 'Subscription Plans')

@section('content')
<div class="grid-3">
    @foreach($plans as $index => $plan)
    <div class="plan-card {{ $index === 1 ? 'featured' : '' }}">
        <div class="plan-name">{{ $plan->name }}</div>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">{{ $plan->description }}</p>

        <div class="plan-price">
            ₹{{ number_format($plan->price_monthly) }} <span>/mo</span>
        </div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:16px;">
            or ₹{{ number_format($plan->price_yearly) }}/year
        </div>

        <ul class="plan-features">
            <li><i class="fas fa-check"></i> Up to {{ number_format($plan->max_students) }} students</li>
            <li><i class="fas fa-check"></i> Up to {{ $plan->max_users }} users</li>
            <li><i class="fas fa-check"></i> {{ $plan->storage_gb }} GB storage</li>
            <li>
                <i class="fas {{ $plan->custom_templates ? 'fa-check' : 'fa-xmark' }}"></i>
                Custom Templates
            </li>
            <li>
                <i class="fas {{ $plan->analytics_access ? 'fa-check' : 'fa-xmark' }}"></i>
                Analytics Dashboard
            </li>
            <li>
                <i class="fas {{ $plan->api_access ? 'fa-check' : 'fa-xmark' }}"></i>
                API Access
            </li>
        </ul>

        <button class="btn {{ $index === 1 ? 'btn-primary' : 'btn-ghost' }}" style="width:100%;justify-content:center;">
            {{ $index === 1 ? 'Get Started' : 'Choose Plan' }}
        </button>
    </div>
    @endforeach
</div>
@endsection
