<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {}

    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $result = $this->paymentService->createOrder(
            $request->school_id,
            $request->plan_id,
            $request->billing_cycle
        );

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $result = $this->paymentService->verifyPayment(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        $statusCode = $result['success'] ? 200 : 400;

        return response()->json($result, $statusCode);
    }

    public function history(Request $request): JsonResponse
    {
        $payments = \App\Models\Payment::with(['subscription.plan', 'school'])
            ->when($request->tenant_id, fn($q) => $q->where('school_id', $request->tenant_id))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $payments]);
    }
}
