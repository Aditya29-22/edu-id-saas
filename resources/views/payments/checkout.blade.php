@extends('layouts.app')
@section('title', 'Payment Gateway')
@section('page-title', 'Payment Gateway')

@section('content')
<div class="grid-2" style="gap:28px;">
    <!-- Payment Form -->
    <div>
        <div class="card" style="border:2px solid var(--gold);">
            <div class="card-header" style="background:linear-gradient(135deg, #1a1a2e, #16213e);">
                <h3 style="color:white;"><i class="fas fa-lock" style="color:var(--gold-light);margin-right:8px;"></i> Secure Payment</h3>
                <div style="display:flex;gap:6px;">
                    <img src="https://img.icons8.com/color/28/visa.png" alt="Visa" style="height:22px;">
                    <img src="https://img.icons8.com/color/28/mastercard.png" alt="MC" style="height:22px;">
                    <img src="https://img.icons8.com/color/28/rupay.png" alt="RuPay" style="height:22px;">
                </div>
            </div>
            <div class="card-body" style="padding:28px;">
                <form method="POST" action="{{ route('payments.process') }}" id="payment-form">
                    @csrf
                    <div class="form-group">
                        <label>School *</label>
                        <select name="school_id" class="form-control" required id="pay-school">
                            <option value="">Select School</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Plan *</label>
                            <select name="plan_id" class="form-control" required id="pay-plan">
                                <option value="">Select</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" data-m="{{ $plan->price_monthly }}" data-y="{{ $plan->price_yearly }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Billing</label>
                            <select name="billing_cycle" class="form-control" required id="pay-cycle">
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div style="height:1px;background:var(--border);margin:20px 0;"></div>

                    <div class="form-group">
                        <label>Cardholder Name *</label>
                        <input type="text" name="card_holder" class="form-control" placeholder="John Doe" required>
                    </div>

                    <div class="form-group">
                        <label>Card Number</label>
                        <div style="position:relative;">
                            <input type="text" class="form-control" placeholder="4242 4242 4242 4242" maxlength="19" style="padding-left:44px;letter-spacing:2px;" id="card-number">
                            <i class="fas fa-credit-card" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:6px;"><i class="fas fa-info-circle"></i> Test mode — any card number works</div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Expiry</label>
                            <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="password" class="form-control" placeholder="•••" maxlength="4">
                        </div>
                    </div>

                    <div id="pay-total" style="padding:20px;background:linear-gradient(135deg, #1a1a2e, #16213e);border-radius:14px;text-align:center;margin-bottom:20px;">
                        <div style="font-size:11px;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1.5px;font-weight:700;">Payment Amount</div>
                        <div id="pay-amount" style="font-size:36px;font-weight:800;color:var(--gold-light);margin-top:4px;">₹0</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.4);margin-top:4px;">Includes 18% GST</div>
                    </div>

                    <button type="submit" id="pay-btn" class="btn btn-primary" style="width:100%;justify-content:center;padding:16px;font-size:16px;border-radius:14px;">
                        <i class="fas fa-shield-halved"></i> Pay Securely
                    </button>

                    <div style="text-align:center;margin-top:14px;font-size:11px;color:var(--text-muted);">
                        <i class="fas fa-lock" style="margin-right:4px;"></i> Secured by Razorpay · 256-bit SSL encryption
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div>
        <div class="card" style="position:sticky;top:90px;">
            <div class="card-header">
                <h3><i class="fas fa-receipt" style="color:var(--gold-dark);margin-right:8px;"></i> Order Summary</h3>
            </div>
            <div class="card-body">
                <div id="order-details">
                    <div class="empty-state" style="padding:24px;">
                        <i class="fas fa-shopping-cart" style="font-size:32px;"></i>
                        <h4 style="font-size:14px;">Select a plan</h4>
                        <p style="font-size:12px;">Choose a school and plan to see the summary</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security badges -->
        <div style="margin-top:16px;display:flex;gap:12px;">
            <div style="flex:1;padding:16px;background:var(--bg-cream);border-radius:12px;text-align:center;border:1px solid var(--border);">
                <i class="fas fa-shield-halved" style="font-size:24px;color:var(--gold-dark);margin-bottom:8px;display:block;"></i>
                <div style="font-size:11px;font-weight:700;color:var(--text-primary);">PCI DSS</div>
                <div style="font-size:10px;color:var(--text-muted);">Compliant</div>
            </div>
            <div style="flex:1;padding:16px;background:var(--bg-cream);border-radius:12px;text-align:center;border:1px solid var(--border);">
                <i class="fas fa-lock" style="font-size:24px;color:var(--gold-dark);margin-bottom:8px;display:block;"></i>
                <div style="font-size:11px;font-weight:700;color:var(--text-primary);">SSL 256-bit</div>
                <div style="font-size:10px;color:var(--text-muted);">Encrypted</div>
            </div>
            <div style="flex:1;padding:16px;background:var(--bg-cream);border-radius:12px;text-align:center;border:1px solid var(--border);">
                <i class="fas fa-rotate-left" style="font-size:24px;color:var(--gold-dark);margin-bottom:8px;display:block;"></i>
                <div style="font-size:11px;font-weight:700;color:var(--text-primary);">Refund</div>
                <div style="font-size:10px;color:var(--text-muted);">Guaranteed</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const plans = @json($plans);

function updatePayment() {
    const planId = document.getElementById('pay-plan').value;
    const cycle = document.getElementById('pay-cycle').value;
    const school = document.getElementById('pay-school');
    const schoolName = school.options[school.selectedIndex]?.text || '';

    if (!planId) return;

    const plan = plans.find(p => p.id == planId);
    if (!plan) return;

    const amount = cycle === 'monthly' ? plan.price_monthly : plan.price_yearly;
    document.getElementById('pay-amount').textContent = '₹' + Number(amount).toLocaleString('en-IN');

    document.getElementById('order-details').innerHTML = `
        <div style="margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted);font-size:13px;">School</span>
                <span style="font-weight:600;font-size:13px;">${schoolName}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted);font-size:13px;">Plan</span>
                <span class="badge badge-gold">${plan.name}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted);font-size:13px;">Billing</span>
                <span style="font-weight:600;font-size:13px;">${cycle === 'monthly' ? 'Monthly' : 'Yearly'}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted);font-size:13px;">Max Students</span>
                <span style="font-weight:600;font-size:13px;">${Number(plan.max_students).toLocaleString()}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted);font-size:13px;">Storage</span>
                <span style="font-weight:600;font-size:13px;">${plan.storage_gb} GB</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:14px 0;margin-top:8px;">
                <span style="font-weight:800;font-size:15px;">Total</span>
                <span style="font-weight:800;font-size:18px;color:var(--gold-dark);">₹${Number(amount).toLocaleString('en-IN')}</span>
            </div>
        </div>
    `;
}

['pay-school', 'pay-plan', 'pay-cycle'].forEach(id => {
    document.getElementById(id).addEventListener('change', updatePayment);
});

// Card number formatting
document.getElementById('card-number').addEventListener('input', function(e) {
    let v = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
    e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
});
</script>
@endsection
