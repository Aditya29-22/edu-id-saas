<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentWebController extends Controller
{
    public function subscriptions(Request $request)
    {
        $subscriptions = Subscription::with(['school', 'plan'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10);

        $stats = [
            'active' => Subscription::where('status', 'active')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'revenue' => Payment::where('status', 'captured')->sum('amount'),
        ];

        return view('subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function createSubscription()
    {
        $schools = School::where('is_active', true)->get();
        $plans = Plan::where('is_active', true)->get();
        return view('subscriptions.create', compact('schools', 'plans'));
    }

    public function storeSubscription(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $amount = $request->billing_cycle === 'monthly' ? $plan->price_monthly : $plan->price_yearly;

        $startDate = Carbon::today();
        $endDate = $request->billing_cycle === 'monthly'
            ? $startDate->copy()->addMonth()
            : $startDate->copy()->addYear();

        // Create subscription
        $subscription = Subscription::create([
            'school_id' => $request->school_id,
            'plan_id' => $request->plan_id,
            'billing_cycle' => $request->billing_cycle,
            'status' => 'active',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $amount,
            'auto_renew' => true,
        ]);

        // Create payment record (simulated Razorpay)
        $orderId = 'order_' . Str::random(14);
        Payment::create([
            'school_id' => $request->school_id,
            'subscription_id' => $subscription->id,
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => 'pay_' . Str::random(14),
            'razorpay_signature' => Str::random(32),
            'amount' => $amount,
            'currency' => 'INR',
            'status' => 'captured',
            'method' => 'card',
            'paid_at' => now(),
            'receipt' => 'rcpt_' . Str::random(8),
        ]);

        // Update school subscription status
        School::where('id', $request->school_id)->update([
            'subscription_status' => 'active',
        ]);

        return redirect()->route('subscriptions')
            ->with('success', "Subscription activated! ₹{$amount} payment captured.");
    }

    public function payments(Request $request)
    {
        $payments = Payment::with(['school', 'subscription.plan'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function checkout(Request $request)
    {
        $schools = School::where('is_active', true)->get();
        $plans = Plan::where('is_active', true)->get();
        return view('payments.checkout', compact('schools', 'plans'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'card_holder' => 'required|string',
        ]);

        return $this->storeSubscription($request);
    }
}
