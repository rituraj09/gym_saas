<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\DifficultyLevel;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MuscleGroup;
use Illuminate\Http\Request;

class OwnerExerciseController extends Controller
{
    public function index(Request $request)
    {
        $query = Exercise::with(['muscleGroup', 'equipmentType', 'difficultyLevel'])->active();

        if ($request->filled('muscle_group')) {
            $query->forMuscleGroup($request->muscle_group);
        }

        if ($request->filled('equipment')) {
            $query->forEquipment($request->equipment);
        }

        if ($request->filled('difficulty')) {
            $query->forDifficulty($request->difficulty);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $exercises = $query->orderBy('name')->paginate(18)->withQueryString();

        $muscleGroups = MuscleGroup::orderBy('name')->get();
        $equipmentTypes = EquipmentType::orderBy('name')->get();
        $difficultyLevels = DifficultyLevel::orderBy('id')->get();

        return view('owner.exercises.index', compact(
            'exercises', 'muscleGroups', 'equipmentTypes', 'difficultyLevels'
        ));
    }
}
