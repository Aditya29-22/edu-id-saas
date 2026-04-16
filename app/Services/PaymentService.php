<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    private Api $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder(int $schoolId, int $planId, string $billingCycle): array
    {
        $plan = Plan::findOrFail($planId);
        $school = School::findOrFail($schoolId);

        $amount = $billingCycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
        $amountInPaise = (int)($amount * 100);

        $startDate = now();
        $endDate = $billingCycle === 'yearly'
            ? now()->addYear()
            : now()->addMonth();

        return DB::transaction(function () use (
            $school, $plan, $billingCycle, $amount, $amountInPaise, $startDate, $endDate
        ) {
            $subscription = Subscription::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
                'status' => 'pending',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'amount' => $amount,
            ]);

            $receipt = 'rcpt_' . $school->code . '_' . time();

            $razorpayOrder = $this->razorpay->order->create([
                'receipt' => $receipt,
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'notes' => [
                    'school_id' => $school->id,
                    'plan_id' => $plan->id,
                    'subscription_id' => $subscription->id,
                ],
            ]);

            $payment = Payment::create([
                'school_id' => $school->id,
                'subscription_id' => $subscription->id,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'created',
                'receipt' => $receipt,
            ]);

            return [
                'order_id' => $razorpayOrder['id'],
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'key' => config('services.razorpay.key'),
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'school_name' => $school->name,
                'plan_name' => $plan->name,
            ];
        });
    }

    public function verifyPayment(
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $razorpaySignature
    ): array {
        $expectedSignature = hash_hmac(
            'sha256',
            $razorpayOrderId . '|' . $razorpayPaymentId,
            config('services.razorpay.secret')
        );

        if (!hash_equals($expectedSignature, $razorpaySignature)) {
            return [
                'success' => false,
                'message' => 'Payment verification failed. Invalid signature.',
            ];
        }

        return DB::transaction(function () use ($razorpayOrderId, $razorpayPaymentId, $razorpaySignature) {
            $payment = Payment::where('razorpay_order_id', $razorpayOrderId)->firstOrFail();

            $rzpPayment = $this->razorpay->payment->fetch($razorpayPaymentId);

            $payment->update([
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
                'status' => 'captured',
                'method' => $rzpPayment['method'] ?? null,
                'paid_at' => now(),
            ]);

            $subscription = Subscription::find($payment->subscription_id);
            $subscription->update(['status' => 'active']);

            School::where('id', $payment->school_id)->update([
                'subscription_status' => 'active'
            ]);

            return [
                'success' => true,
                'message' => 'Payment verified. Subscription activated.',
                'subscription_id' => $subscription->id,
            ];
        });
    }
}
