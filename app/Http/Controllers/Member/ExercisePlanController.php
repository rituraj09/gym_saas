<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExercisePlanController extends Controller
{
    public function index()
    {
        $member = Auth::user()->member;
        $plan = $member->activeExercisePlan;

        if ($plan) {
            $plan->load(['workoutDays.exercises.exercise', 'trainer.user']);
        }

        $todayDayName = now()->format('l');

        return view('member.exercise-plan.index', compact('plan', 'todayDayName'));
    }
}
