<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\GymMemberPayment;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function index()
    {
        $member = Auth::user()->member;

        $member->load([
            'latestMembership.membershipPlan',
            'latestMembership.membershipStatus',
            'gym',
        ]);

        $membership = $member->latestMembership;

        $daysRemaining = null;
        $isExpired = false;
        $isExpiringSoon = false;

        if ($membership && $membership->end_date) {
            $daysRemaining = (int) now()->startOfDay()->diffInDays($membership->end_date, false);
            $isExpired = $daysRemaining < 0;
            $isExpiringSoon = ! $isExpired && $daysRemaining <= 7;
        }

        $payments = GymMemberPayment::where('member_id', $member->id)
            ->with(['paymentMethod', 'paymentStatus'])
            ->latest('payment_date')
            ->take(10)
            ->get();

        return view('member.membership.index', compact(
            'member', 'membership', 'daysRemaining', 'isExpired', 'isExpiringSoon', 'payments'
        ));
    }
}
