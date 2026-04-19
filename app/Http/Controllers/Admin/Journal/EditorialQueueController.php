<?php

namespace App\Http\Controllers\Admin\Journal;

use App\Exceptions\Editorial\AlreadyAssignedException;
use App\Exceptions\Editorial\IneligibleUserException;
use App\Exceptions\Editorial\RoleConflictException;
use App\Http\Controllers\Controller;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use App\Enums\SubmissionStatus;
use App\Exceptions\Editorial\IllegalTransitionException;
use App\Policies\SubmissionPolicy;
use App\Services\EditorialAssignmentService;
use App\Services\SubmissionStateMachine;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EditorialQueueController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        abort_unless($this->canAccessQueue($request->user()), 403);

        $submissions = Submission::whereNull('editor_id')
            ->whereIn('status', [SubmissionStatus::Submitted, SubmissionStatus::UnderInitialReview])
            ->with(['author', 'reviews.reviewer'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $eligibleEditors = User::withCapability(EditorialCapability::EDITOR)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.journal.queue', [
            'submissions' => $submissions,
            'eligibleEditors' => $eligibleEditors,
        ]);
    }

    public function take(Request $request, Submission $submission, EditorialAssignmentService $service)
    {
        $this->authorize('takeEditor', $submission);

        try {
            $service->takeEditor($submission, $request->user());
        } catch (AlreadyAssignedException | RoleConflictException | IneligibleUserException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Article pris en charge.');
    }

    public function assign(Request $request, Submission $submission, EditorialAssignmentService $service)
    {
        $this->authorize('assignEditor', $submission);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'override' => 'sometimes|boolean',
            'override_reason' => 'required_if:override,1|nullable|string|min:3|max:500',
        ], [
            'override_reason.required_if' => 'Le motif est obligatoire quand la séparation des rôles est forcée.',
            'override_reason.min' => 'Motif trop court (3 caractères minimum).',
        ]);

        $target = User::findOrFail($validated['user_id']);

        try {
            $service->assignEditor(
                $submission,
                $target,
                $request->user(),
                override: (bool) ($validated['override'] ?? false),
                overrideReason: $validated['override_reason'] ?? null,
            );
        } catch (RoleConflictException $e) {
            return back()->with('error', $e->getMessage() . ' Coche "forcer" pour outrepasser.');
        } catch (IneligibleUserException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Article assigné à {$target->name}.");
    }

    public function mine(Request $request)
    {
        abort_unless($request->user()->hasCapability(EditorialCapability::EDITOR), 403);

        $submissions = Submission::where('editor_id', $request->user()->id)
            ->with(['author', 'reviews'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('admin.journal.mine', ['submissions' => $submissions]);
    }

    public function transition(
        Request $request,
        Submission $submission,
        SubmissionStateMachine $stateMachine,
    ) {
        $validated = $request->validate([
            'target_status' => ['required', Rule::enum(SubmissionStatus::class)],
            'notes' => 'nullable|string|max:2000',
            'redirect_to_lepis' => 'sometimes|boolean',
        ]);

        $target = SubmissionStatus::from($validated['target_status']);

        // Case « Recommander pour Lepis » cochée : redirige la transition vers le
        // statut intermédiaire RejectedPendingLepis (invisible à l'auteur).
        if ($target === SubmissionStatus::Rejected && ($validated['redirect_to_lepis'] ?? false)) {
            $target = SubmissionStatus::RejectedPendingLepis;
        }

        abort_unless(
            app(SubmissionPolicy::class)->transitionTo($request->user(), $submission, $target),
            403,
            'Transition non autorisée.'
        );

        try {
            $stateMachine->transition(
                $submission,
                $target,
                $request->user(),
                $validated['notes'] ?? null,
            );
        } catch (IllegalTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Article passé en « {$target->label()} ».");
    }

    public function inviteReviewer(Request $request, Submission $submission, EditorialAssignmentService $service)
    {
        abort_unless(
            app(SubmissionPolicy::class)->assignReviewer($request->user(), $submission),
            403
        );

        $validated = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'override' => 'sometimes|boolean',
            'override_reason' => 'required_if:override,1|nullable|string|min:3|max:500',
        ], [
            'override_reason.required_if' => 'Le motif est obligatoire quand la séparation des rôles est forcée.',
            'override_reason.min' => 'Motif trop court (3 caractères minimum).',
        ]);

        $target = User::findOrFail($validated['reviewer_id']);

        try {
            $service->assignReviewer(
                $submission,
                $target,
                $request->user(),
                override: (bool) ($validated['override'] ?? false),
                overrideReason: $validated['override_reason'] ?? null,
            );
        } catch (RoleConflictException $e) {
            return back()->with('error', $e->getMessage());
        } catch (IneligibleUserException $e) {
            return back()->with('error', $e->getMessage());
        } catch (AlreadyAssignedException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "{$target->name} invité comme relecteur.");
    }

    public function reassignEditor(Request $request, Submission $submission, EditorialAssignmentService $service)
    {
        abort_unless(
            app(SubmissionPolicy::class)->assignEditor($request->user(), $submission),
            403
        );

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'override' => 'sometimes|boolean',
            'override_reason' => 'required_if:override,1|nullable|string|min:3|max:500',
        ], [
            'override_reason.required_if' => 'Le motif est obligatoire quand la séparation des rôles est forcée.',
            'override_reason.min' => 'Motif trop court (3 caractères minimum).',
        ]);

        $target = User::findOrFail($validated['user_id']);

        try {
            $service->assignEditor(
                $submission,
                $target,
                $request->user(),
                override: (bool) ($validated['override'] ?? false),
                overrideReason: $validated['override_reason'] ?? null,
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Éditeur changé : {$target->name}.");
    }

    public function assignLayoutEditor(Request $request, Submission $submission, EditorialAssignmentService $service)
    {
        abort_unless(
            app(SubmissionPolicy::class)->assignLayoutEditor($request->user(), $submission),
            403
        );

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $target = User::findOrFail($validated['user_id']);

        try {
            $service->assignLayoutEditor($submission, $target, $request->user());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Maquettiste assigné : {$target->name}.");
    }

    private function canAccessQueue(User $user): bool
    {
        return $user->isAdmin()
            || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
            || $user->hasCapability(EditorialCapability::EDITOR);
    }
}
