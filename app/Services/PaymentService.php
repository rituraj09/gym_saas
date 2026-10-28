<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymMemberPayment;
use App\Models\Member;
use App\Models\PaymentStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function record(Gym $gym, array $data, ?UploadedFile $receipt = null): GymMemberPayment
    {
        $member = Member::findOrFail($data['member_id']);

        if ($member->gym_id !== $gym->id) {
            abort(403, 'Member does not belong to your gym.');
        }

        $receiptPath = $receipt ? $receipt->store('payment-receipts', 'public') : null;

        $completedStatusId = PaymentStatus::where('code', 'completed')->value('id');

        return GymMemberPayment::create([
            'gym_id'                => $gym->id,
            'member_id'             => $member->id,
            'membership_id'         => $data['membership_id'] ?? null,
            'payment_method_id'     => $data['payment_method_id'],
            'payment_status_id'     => $completedStatusId,
            'amount'                => $data['amount'],
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'payment_date'          => $data['payment_date'],
            'notes'                 => $data['notes'] ?? null,
            'receipt'               => $receiptPath,
        ]);
    }
}
