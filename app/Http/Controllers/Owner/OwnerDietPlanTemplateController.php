<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreDietPlanTemplateRequest;
use App\Http\Requests\Owner\UpdateDietPlanTemplateRequest;
use App\Models\DietPlanTemplate;
use App\Models\MealType;
use App\Services\DietPlanService;

class OwnerDietPlanTemplateController extends Controller
{
    public function __construct(
        protected DietPlanService $dietPlanService
    ) {}

    protected function getGymId(): int
    {
        return auth()->user()->gymOwner->gym_id;
    }

    protected function authorizeGym(DietPlanTemplate $template): void
    {
        if ($template->gym_id !== $this->getGymId()) {
            abort(403);
        }
    }

    public function index()
    {
        $templates = DietPlanTemplate::with(['meals.items'])
            ->forGym($this->getGymId())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('owner.diet-plan-templates.index', compact('templates'));
    }

    public function create()
    {
        $mealTypes = MealType::orderBy('sort_order')->get();

        return view('owner.diet-plan-templates.create', compact('mealTypes'));
    }

    public function store(StoreDietPlanTemplateRequest $request)
    {
        $template = $this->dietPlanService->createTemplate(
            auth()->user()->gymOwner->gym,
            auth()->id(),
            $request->validated()
        );

        return redirect()
            ->route('owner.diet-plan-templates.show', $template)
            ->with('success', 'Diet plan template created successfully.');
    }

    public function show(DietPlanTemplate $template)
    {
        $this->authorizeGym($template);

        $template->load(['meals.items', 'meals.mealType', 'createdBy']);

        return view('owner.diet-plan-templates.show', compact('template'));
    }

    public function edit(DietPlanTemplate $template)
    {
        $this->authorizeGym($template);

        $template->load('meals.items');

        $mealTypes = MealType::orderBy('sort_order')->get();

        return view('owner.diet-plan-templates.edit', compact('template', 'mealTypes'));
    }

    public function update(UpdateDietPlanTemplateRequest $request, DietPlanTemplate $template)
    {
        $this->authorizeGym($template);

        $this->dietPlanService->updateTemplate($template, $request->validated());

        return redirect()
            ->route('owner.diet-plan-templates.show', $template)
            ->with('success', 'Diet plan template updated successfully.');
    }

    public function toggleStatus(DietPlanTemplate $template)
    {
        $this->authorizeGym($template);

        $this->dietPlanService->toggleTemplateStatus($template);

        $status = $template->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Template {$status} successfully.");
    }

    public function destroy(DietPlanTemplate $template)
    {
        $this->authorizeGym($template);

        $template->delete();

        return redirect()
            ->route('owner.diet-plan-templates.index')
            ->with('success', 'Diet plan template deleted successfully.');
    }
}
