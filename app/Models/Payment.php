<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'school_id', 'subscription_id',
        'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature',
        'amount', 'currency', 'status', 'method', 'paid_at', 'receipt'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
