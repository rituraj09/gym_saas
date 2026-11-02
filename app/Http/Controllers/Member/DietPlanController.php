<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DietPlanController extends Controller
{
    public function index()
    {
        $member = Auth::user()->member;
        $plan = $member->activeDietPlan;

        if ($plan) {
            $plan->load(['meals.mealType', 'meals.items', 'trainer.user']);
        }

        return view('member.diet-plan.index', compact('plan'));
    }
}
