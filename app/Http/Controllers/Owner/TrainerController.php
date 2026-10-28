<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreTrainerRequest;
use App\Http\Requests\Owner\UpdateTrainerRequest;
use App\Models\Trainer;
use App\Services\TrainerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerController extends Controller
{
    public function __construct(
        protected TrainerService $trainerService
    ) {}

    public function index(Request $request): View
    {
        $gym = $request->user()->gymOwner->gym;

        $query = Trainer::with('user.accountStatus')
            ->where('gym_id', $gym->id);

        if ($search = $request->filled('search') ? $request->search : null) {
            $query->where(function ($q) use ($search) {
                $q->where('trainer_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->whereHas('accountStatus', fn ($sq) => $sq->where('code', $request->status));
            });
        }

        $trainers = $query->latest('joining_date')->paginate(15)->withQueryString();

        return view('owner.trainers.index', compact('trainers', 'gym'));
    }

    public function create(): View
    {
        $genders = \App\Models\GenderType::orderBy('name')->get();
        $bloodGroups = \App\Models\BloodGroupType::orderBy('name')->get();

        return view('owner.trainers.create', compact('genders', 'bloodGroups'));
    }

    public function store(StoreTrainerRequest $request): RedirectResponse
    {
        $gym = $request->user()->gymOwner->gym;

        $this->trainerService->createTrainer(
            $gym,
            $request->validated(),
            $request->file('profile_photo')
        );

        return redirect()->route('owner.trainers.index')
            ->with('success', 'Trainer added successfully.');
    }

    public function show(Trainer $trainer): View
    {
        $this->authorizeGym($trainer);

        $trainer->load(['user.accountStatus', 'gym', 'gender', 'bloodGroup']);

        return view('owner.trainers.show', compact('trainer'));
    }

    public function edit(Trainer $trainer): View
    {
        $this->authorizeGym($trainer);

        $trainer->load('user');
        $genders = \App\Models\GenderType::orderBy('name')->get();
        $bloodGroups = \App\Models\BloodGroupType::orderBy('name')->get();

        return view('owner.trainers.edit', compact('trainer', 'genders', 'bloodGroups'));
    }

    public function update(UpdateTrainerRequest $request, Trainer $trainer): RedirectResponse
    {
        $this->authorizeGym($trainer);

        $this->trainerService->updateTrainer(
            $trainer,
            $request->validated(),
            $request->file('profile_photo')
        );

        return redirect()->route('owner.trainers.show', $trainer)
            ->with('success', 'Trainer updated successfully.');
    }

    public function toggleStatus(Trainer $trainer): RedirectResponse
    {
        $this->authorizeGym($trainer);

        $action = $this->trainerService->toggleStatus($trainer);

        return back()->with('success', "Trainer {$action} successfully.");
    }

    protected function authorizeGym(Trainer $trainer): void
    {
        $gymId = auth()->user()->gymOwner->gym_id;

        if ($trainer->gym_id !== $gymId) {
            abort(403, 'This trainer does not belong to your gym.');
        }
    }
}
