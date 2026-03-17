<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Database\Seeders\TutorialBudgetSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TutorialController extends Controller
{
    public function hub()
    {
        $user = Auth::user();

        return Inertia::render('Tutorial/Hub', [
            'hasCompletedLearn' => $user->has_completed_learn_tutorial,
            'hasCompletedSetup' => $user->has_completed_setup_tutorial,
            'hasCompletedPlan' => $user->has_completed_plan_tutorial,
            'hasCompletedTransactions' => $user->has_completed_transactions_tutorial,
            'hasCompletedSplits' => $user->has_completed_splits_tutorial,
            'hasCompletedRecurring' => $user->has_completed_recurring_tutorial,
            'hasBudget' => $user->currentBudget !== null,
        ]);
    }

    public function startLearn()
    {
        $user = Auth::user();

        // Create tutorial budget via seeder
        $seeder = new TutorialBudgetSeeder();
        $budget = $seeder->run($user);

        // Set as current budget and start tutorial
        $user->update([
            'current_budget_id' => $budget->id,
            'tutorial_track' => 'learn',
            'tutorial_step' => 'welcome',
        ]);

        return redirect()->route('budget.index');
    }

    public function startSetup()
    {
        return redirect()->route('onboarding.setup');
    }

    public function startPlan()
    {
        $user = Auth::user();
        $user->update([
            'tutorial_track' => 'plan',
            'tutorial_step' => 'welcome',
        ]);

        return redirect()->route('plan.index');
    }

    public function startTransactions()
    {
        $user = Auth::user();
        $user->update([
            'tutorial_track' => 'transactions',
            'tutorial_step' => 'welcome',
        ]);

        return redirect()->route('transactions.create');
    }

    public function startSplits()
    {
        $user = Auth::user();
        $user->update([
            'tutorial_track' => 'splits',
            'tutorial_step' => 'welcome',
        ]);

        return redirect()->route('transactions.create');
    }

    public function startRecurring()
    {
        $user = Auth::user();
        $user->update([
            'tutorial_track' => 'recurring',
            'tutorial_step' => 'welcome',
        ]);

        return redirect()->route('recurring.create');
    }

    public function updateStep(Request $request)
    {
        $validated = $request->validate([
            'step' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'tutorial_step' => $validated['step'],
        ]);

        return redirect()->back();
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'track' => 'required|string|in:learn,setup,plan,transactions,splits,recurring',
        ]);

        $user = Auth::user();

        $field = match($validated['track']) {
            'learn' => 'has_completed_learn_tutorial',
            'setup' => 'has_completed_setup_tutorial',
            'plan' => 'has_completed_plan_tutorial',
            'transactions' => 'has_completed_transactions_tutorial',
            'splits' => 'has_completed_splits_tutorial',
            'recurring' => 'has_completed_recurring_tutorial',
        };

        $updates = [
            $field => true,
            'tutorial_track' => null,
            'tutorial_step' => null,
        ];

        // If currently on a tutorial budget, switch back to real budget and clean up
        $currentBudget = $user->currentBudget;
        if ($currentBudget && $currentBudget->is_tutorial) {
            $realBudget = $user->budgets()->where('is_tutorial', false)->first();

            $updates['current_budget_id'] = $realBudget?->id;

            // Only delete the tutorial budget when completing the learn track
            if ($validated['track'] === 'learn') {
                $currentBudget->delete();
            }
        }

        $user->update($updates);

        return redirect()->back();
    }

    public function tips()
    {
        return Inertia::render('Tutorial/Tips');
    }

    public function tipShow(string $slug)
    {
        return Inertia::render('Tutorial/TipDetail', [
            'slug' => $slug,
        ]);
    }
}
