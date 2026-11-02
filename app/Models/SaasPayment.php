<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class SaasPayment extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'gym_subscription_id',
        'payment_method_id',
        'payment_status_id',
        'amount',
        'payment_date',
        'billing_period_start',
        'billing_period_end',
        'reference_number',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(GymSubscription::class, 'gym_subscription_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentStatus(): BelongsTo
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function recordedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }
}
