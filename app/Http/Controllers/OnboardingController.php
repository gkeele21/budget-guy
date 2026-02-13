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
            'account_type' => 'nullable|in:checking,savings,credit_card,cash',
            'account_balance' => 'nullable|numeric',
            'use_template' => 'nullable|string|in:basic,detailed,minimal',
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
                    'type' => $validated['account_type'] ?? 'checking',
                    'starting_balance' => $validated['account_balance'] ?? 0,
                    'sort_order' => 0,
                ]);
            }

            // Create categories based on template
            $template = $validated['use_template'] ?? 'basic';
            $this->createCategoriesFromTemplate($budget, $template);
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

            // Create basic categories
            $this->createCategoriesFromTemplate($budget, 'minimal');
        });

        return redirect()->route('dashboard');
    }

    private function createCategoriesFromTemplate(Budget $budget, string $template): void
    {
        $templates = [
            'minimal' => [
                'Bills' => ['Rent/Mortgage', 'Utilities', 'Insurance'],
                'Everyday' => ['Groceries', 'Transportation', 'Dining Out'],
                'Savings' => ['Emergency Fund', 'Savings Goals'],
            ],
            'basic' => [
                'Bills' => [
                    'Rent/Mortgage' => 1500,
                    'Utilities' => 200,
                    'Phone' => 80,
                    'Internet' => 60,
                    'Insurance' => 150,
                ],
                'Everyday' => [
                    'Groceries' => 400,
                    'Transportation' => 200,
                    'Dining Out' => 150,
                    'Entertainment' => 100,
                    'Shopping' => 100,
                ],
                'Savings' => [
                    'Emergency Fund' => 200,
                    'Vacation' => 100,
                    'Savings Goals' => 100,
                ],
                'Debt' => [
                    'Credit Card' => 0,
                    'Student Loans' => 0,
                ],
            ],
            'detailed' => [
                'Housing' => [
                    'Rent/Mortgage' => 1500,
                    'Home Insurance' => 100,
                    'Property Tax' => 200,
                    'Home Maintenance' => 100,
                ],
                'Utilities' => [
                    'Electric' => 100,
                    'Gas' => 50,
                    'Water' => 40,
                    'Internet' => 60,
                    'Phone' => 80,
                ],
                'Food' => [
                    'Groceries' => 400,
                    'Dining Out' => 150,
                    'Coffee' => 40,
                ],
                'Transportation' => [
                    'Gas/Fuel' => 150,
                    'Car Insurance' => 100,
                    'Car Maintenance' => 50,
                    'Public Transit' => 0,
                    'Parking' => 0,
                ],
                'Personal' => [
                    'Clothing' => 100,
                    'Personal Care' => 50,
                    'Healthcare' => 100,
                    'Subscriptions' => 50,
                ],
                'Entertainment' => [
                    'Streaming Services' => 50,
                    'Hobbies' => 50,
                    'Books/Media' => 20,
                    'Events' => 50,
                ],
                'Savings' => [
                    'Emergency Fund' => 200,
                    'Retirement' => 200,
                    'Vacation' => 100,
                    'Big Purchases' => 100,
                ],
                'Debt Payments' => [
                    'Credit Card' => 0,
                    'Student Loans' => 0,
                    'Other Debt' => 0,
                ],
            ],
        ];

        $categoryData = $templates[$template] ?? $templates['minimal'];
        $groupOrder = 0;

        foreach ($categoryData as $groupName => $categories) {
            $group = CategoryGroup::create([
                'budget_id' => $budget->id,
                'name' => $groupName,
                'sort_order' => $groupOrder++,
            ]);

            $categoryOrder = 0;
            foreach ($categories as $categoryName => $defaultAmount) {
                // Handle both simple arrays and arrays with default amounts
                if (is_numeric($categoryName)) {
                    $categoryName = $defaultAmount;
                    $defaultAmount = 0;
                }

                Category::create([
                    'category_group_id' => $group->id,
                    'name' => $categoryName,
                    'default_amount' => $defaultAmount,
                    'sort_order' => $categoryOrder++,
                ]);
            }
        }
    }
}
