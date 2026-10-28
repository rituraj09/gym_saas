<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreExerciseTemplateRequest;
use App\Http\Requests\Owner\UpdateExerciseTemplateRequest;
use App\Models\DifficultyLevel;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\ExerciseTemplate;
use App\Models\MuscleGroup;
use App\Services\ExerciseTemplateService;
use Illuminate\Http\Request;

class OwnerExerciseTemplateController extends Controller
{
    public function __construct(
        protected ExerciseTemplateService $templateService
    ) {}

    protected function getGymId(): int
    {
        return auth()->user()->gymOwner->gym_id;
    }

    protected function authorizeGym(ExerciseTemplate $template): void
    {
        if ($template->gym_id !== $this->getGymId()) {
            abort(403);
        }
    }

    public function index()
    {
        $templates = ExerciseTemplate::with(['targetMuscleGroup', 'items.exercise'])
            ->forGym($this->getGymId())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('owner.exercise-templates.index', compact('templates'));
    }

    public function create()
    {
        $exercises = Exercise::active()
            ->with(['muscleGroup', 'equipmentType', 'difficultyLevel'])
            ->orderBy('name')
            ->get();

        $muscleGroups = MuscleGroup::orderBy('name')->get();

        return view('owner.exercise-templates.create', compact('exercises', 'muscleGroups'));
    }

    public function store(StoreExerciseTemplateRequest $request)
    {
        $template = $this->templateService->create(
            auth()->user()->gymOwner->gym,
            auth()->id(),
            $request->validated()
        );

        return redirect()
            ->route('owner.exercise-templates.show', $template)
            ->with('success', 'Exercise template created successfully.');
    }

    public function show(ExerciseTemplate $template)
    {
        $this->authorizeGym($template);

        $template->load(['items.exercise.muscleGroup', 'items.exercise.equipmentType', 'targetMuscleGroup', 'createdBy']);

        return view('owner.exercise-templates.show', compact('template'));
    }

    public function edit(ExerciseTemplate $template)
    {
        $this->authorizeGym($template);

        $template->load('items');

        $exercises = Exercise::active()
            ->with(['muscleGroup', 'equipmentType', 'difficultyLevel'])
            ->orderBy('name')
            ->get();

        $muscleGroups = MuscleGroup::orderBy('name')->get();

        return view('owner.exercise-templates.edit', compact('template', 'exercises', 'muscleGroups'));
    }

    public function update(UpdateExerciseTemplateRequest $request, ExerciseTemplate $template)
    {
        $this->authorizeGym($template);

        $this->templateService->update($template, $request->validated());

        return redirect()
            ->route('owner.exercise-templates.show', $template)
            ->with('success', 'Exercise template updated successfully.');
    }

    public function toggleStatus(ExerciseTemplate $template)
    {
        $this->authorizeGym($template);

        $this->templateService->toggleStatus($template);

        $status = $template->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Template {$status} successfully.");
    }

    public function destroy(ExerciseTemplate $template)
    {
        $this->authorizeGym($template);

        $template->delete();

        return redirect()
            ->route('owner.exercise-templates.index')
            ->with('success', 'Exercise template deleted successfully.');
    }
}
