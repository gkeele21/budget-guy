<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    public function welcome()
    {
        return Inertia::render('Onboarding/Index');
    }

    public function setup()
    {
        $user = Auth::user();

        // If user already has a budget, redirect to dashboard
        if ($user->currentBudget) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Onboarding/Setup');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // If user already has a budget, redirect to dashboard
        if ($user->currentBudget) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'budget_name' => 'required|string|max:255',
            'start_month' => 'nullable|string|date_format:Y-m',
            'account_name' => 'nullable|string|max:255',
            'account_type' => 'nullable|in:bank,cash,credit',
            'account_balance' => 'nullable|numeric',
            'use_template' => 'nullable|string|in:starter,none',
        ]);

        DB::transaction(function () use ($validated, $user) {
            // Create budget
            $budget = Budget::create([
                'name' => $validated['budget_name'],
                'owner_id' => $user->id,
                'start_month' => $validated['start_month'] ?? now()->format('Y-m'),
            ]);

            // Add user as owner
            $budget->users()->attach($user->id, [
                'role' => 'owner',
                'accepted_at' => now(),
            ]);

            // Set as current budget
            $user->update(['current_budget_id' => $budget->id]);

            // Create account if provided
            if (!empty($validated['account_name'])) {
                Account::create([
                    'budget_id' => $budget->id,
                    'name' => $validated['account_name'],
                    'type' => $validated['account_type'] ?? 'bank',
                    'starting_balance' => $validated['account_balance'] ?? 0,
                    'sort_order' => 0,
                ]);
            }

            // Create categories based on template
            $template = $validated['use_template'] ?? 'starter';
            if ($template !== 'none') {
                $this->createCategoriesFromTemplate($budget);
            }
        });

        return redirect()->route('dashboard');
    }

    public function skip()
    {
        $user = Auth::user();

        // If user already has a budget, redirect to dashboard
        if ($user->currentBudget) {
            return redirect()->route('dashboard');
        }

        DB::transaction(function () use ($user) {
            // Create a default budget
            $budget = Budget::create([
                'name' => 'My Budget',
                'owner_id' => $user->id,
            ]);

            // Add user as owner
            $budget->users()->attach($user->id, [
                'role' => 'owner',
                'accepted_at' => now(),
            ]);

            // Set as current budget
            $user->update(['current_budget_id' => $budget->id]);

            // Create starter categories
            $this->createCategoriesFromTemplate($budget);
        });

        return redirect()->route('dashboard');
    }

    private function createCategoriesFromTemplate(Budget $budget): void
    {
        $categoryData = [
            'Bills' => [
                'Rent/Mortgage' => '🏠',
                'Utilities' => '⚡',
                'Phone' => '📱',
                'Internet' => '🌐',
                'Insurance' => '🛡️',
            ],
            'Everyday' => [
                'Groceries' => '🛒',
                'Transportation' => '🚗',
                'Dining Out' => '🍽️',
                'Entertainment' => '🎬',
                'Shopping' => '🛍️',
            ],
            'Savings' => [
                'Emergency Fund' => '🆘',
                'Vacation' => '✈️',
                'Savings Goals' => '🎯',
            ],
            'Debt' => [
                'Credit Card' => '💳',
                'Student Loans' => '🎓',
            ],
        ];
        $groupOrder = 0;

        foreach ($categoryData as $groupName => $categories) {
            $group = CategoryGroup::create([
                'budget_id' => $budget->id,
                'name' => $groupName,
                'sort_order' => $groupOrder++,
            ]);

            $categoryOrder = 0;
            foreach ($categories as $categoryName => $icon) {
                Category::create([
                    'group_id' => $group->id,
                    'name' => $categoryName,
                    'icon' => $icon,
                    'sort_order' => $categoryOrder++,
                ]);
            }
        }
    }
}
