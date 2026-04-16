@extends('layouts.app')
@section('title', 'New Subscription')
@section('page-title', 'Activate Subscription')

@section('content')
<div style="max-width:580px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-crown" style="color:var(--gold-dark);margin-right:8px;"></i> Subscription Details</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf
                <div class="form-group">
                    <label>School *</label>
                    <select name="school_id" class="form-control" required>
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }} ({{ $school->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Plan *</label>
                    <select name="plan_id" class="form-control" required id="plan-select">
                        <option value="">Select Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-monthly="{{ $plan->price_monthly }}" data-yearly="{{ $plan->price_yearly }}">{{ $plan->name }} — ₹{{ number_format($plan->price_monthly) }}/mo</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Billing Cycle *</label>
                    <select name="billing_cycle" class="form-control" required id="cycle-select">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly (Save 15%+)</option>
                    </select>
                </div>
                <div id="price-display" style="padding:18px;background:var(--bg-cream);border-radius:12px;text-align:center;margin-bottom:18px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-weight:700;">Total Amount</div>
                    <div id="price-amount" style="font-size:32px;font-weight:800;color:var(--text-primary);margin-top:4px;">₹0</div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;font-size:15px;">
                    <i class="fas fa-bolt"></i> Activate Subscription
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updatePrice() {
    const plan = document.getElementById('plan-select');
    const cycle = document.getElementById('cycle-select').value;
    const selected = plan.options[plan.selectedIndex];
    if (!selected || !selected.dataset.monthly) { document.getElementById('price-amount').textContent = '₹0'; return; }
    const amount = cycle === 'monthly' ? selected.dataset.monthly : selected.dataset.yearly;
    document.getElementById('price-amount').textContent = '₹' + Number(amount).toLocaleString('en-IN');
}
document.getElementById('plan-select').addEventListener('change', updatePrice);
document.getElementById('cycle-select').addEventListener('change', updatePrice);
</script>
@endsection
