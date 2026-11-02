<?php

namespace App\Services;

use App\Models\GymSubscription;
use App\Models\SaasPayment;
use Carbon\Carbon;

class SaasPaymentService
{
    public function recordPayment(array $data): SaasPayment
    {
        $data['recorded_by'] = auth()->id();

        $payment = SaasPayment::create($data);

        // If payment is completed, extend the subscription end date
        $status = $payment->paymentStatus;
        if ($status && $status->code === 'completed') {
            $this->extendSubscription($payment->subscription, $payment->billing_period_end);
        }

        return $payment->fresh(['subscription.gym', 'paymentMethod', 'paymentStatus']);
    }

    public function deletePayment(SaasPayment $payment): void
    {
        $payment->delete();
    }

    protected function extendSubscription(GymSubscription $subscription, string $newEndDate): void
    {
        $currentEnd = $subscription->end_date
            ? Carbon::parse($subscription->end_date)
            : Carbon::now();

        $proposedEnd = Carbon::parse($newEndDate);

        // Only extend if the new end date is further out
        if ($proposedEnd->greaterThan($currentEnd)) {
            $subscription->update(['end_date' => $proposedEnd->format('Y-m-d')]);
        }
    }
}
