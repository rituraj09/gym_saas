<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\MealType;
use App\Models\Member;
use App\Models\MemberDietPlan;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DietPlanController extends Controller
{
    public function index()
    {
        $trainer = Auth::user()->trainer;

        $plans = MemberDietPlan::with(['member.user'])
            ->where('gym_id', $trainer->gym_id)
            ->latest()
            ->paginate(15);

        return view('trainer.diet-plans.index', compact('plans'));
    }

    public function create()
    {
        $trainer = Auth::user()->trainer;

        $members = Member::with('user')
            ->where('gym_id', $trainer->gym_id)
            ->leftJoin('users', 'members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('members.*')
            ->get();

        $mealTypes = MealType::orderBy('sort_order')->get();

        return view('trainer.diet-plans.create', compact('members', 'mealTypes'));
    }

    public function store(Request $request)
    {
        $trainer = Auth::user()->trainer;

        $request->validate([
            'member_id'      => 'required|exists:members,id',
            'plan_name'      => 'required|string|max:255',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'notes'          => 'nullable|string|max:1000',
            'total_calories' => 'nullable|integer|min:0',
            'meals'          => 'required|array|min:1',
            'meals.*.meal_type_id' => 'required|exists:meal_types,id',
            'meals.*.time'         => 'nullable|string|max:10',
            'meals.*.items'        => 'required|array|min:1',
            'meals.*.items.*.food_name' => 'required|string|max:255',
            'meals.*.items.*.quantity'  => 'required|string|max:100',
            'meals.*.items.*.calories' => 'nullable|integer|min:0',
            'meals.*.items.*.protein'  => 'nullable|numeric|min:0',
            'meals.*.items.*.carbs'    => 'nullable|numeric|min:0',
            'meals.*.items.*.fats'     => 'nullable|numeric|min:0',
        ]);

        if (! $trainer->canManageMember($request->member_id)) {
            abort(403);
        }

        $plan = DB::transaction(function () use ($request, $trainer) {
            MemberDietPlan::where('member_id', $request->member_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $plan = MemberDietPlan::create([
                'member_id'      => $request->member_id,
                'trainer_id'     => $trainer->id,
                'gym_id'         => $trainer->gym_id,
                'plan_name'      => $request->plan_name,
                'start_date'     => $request->start_date,
                'end_date'       => $request->end_date,
                'notes'          => $request->notes,
                'total_calories' => $request->total_calories,
                'is_active'      => true,
            ]);

            foreach ($request->meals as $mealIndex => $meal) {
                $dietMeal = $plan->meals()->create([
                    'meal_type_id' => $meal['meal_type_id'],
                    'time'         => $meal['time'] ?? null,
                    'sort_order'   => $mealIndex + 1,
                ]);

                foreach ($meal['items'] as $itemIndex => $item) {
                    $dietMeal->items()->create([
                        'food_name'  => $item['food_name'],
                        'quantity'   => $item['quantity'],
                        'calories'   => $item['calories'] ?? null,
                        'protein'    => $item['protein'] ?? null,
                        'carbs'      => $item['carbs'] ?? null,
                        'fats'       => $item['fats'] ?? null,
                        'sort_order' => $itemIndex + 1,
                    ]);
                }
            }

            return $plan;
        });

        // Notify the member
        $member = Member::with('user')->find($request->member_id);
        if ($member && $member->user) {
            NotificationService::notifyMemberDietPlan(
                $member->user_id,
                $trainer->gym_id,
                $plan->plan_name,
                Auth::user()->name
            );
        }

        return redirect()->route('trainer.diet-plans.index')
            ->with('success', 'Diet plan created and member notified.');
    }

    public function show(MemberDietPlan $diet_plan)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer->canManageMember($diet_plan->member_id)) {
            abort(403);
        }

       $diet_plan->load([
            'member.user',
            'dietPlanTemplate.meals.mealType',
            'dietPlanTemplate.meals.items',
        ]);

        return view('trainer.diet-plans.show', compact('diet_plan'));
    }

    public function toggleActive(MemberDietPlan $diet_plan)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer->canManageMember($diet_plan->member_id)) {
            abort(403);
        }

        if (! $diet_plan->is_active) {
            MemberDietPlan::where('member_id', $diet_plan->member_id)
                ->where('id', '!=', $diet_plan->id)
                ->update(['is_active' => false]);
        }

        $diet_plan->update(['is_active' => ! $diet_plan->is_active]);

        return back()->with('success', 'Diet plan status updated.');
    }

    public function destroy(MemberDietPlan $diet_plan)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer->canManageMember($diet_plan->member_id)) {
            abort(403);
        }

        $diet_plan->delete();

        return redirect()->route('trainer.diet-plans.index')
            ->with('success', 'Diet plan deleted.');
    }
}
