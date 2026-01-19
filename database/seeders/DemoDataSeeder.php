<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\MonthlyBudget;
use App\Models\Payee;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Create realistic budget demo data.
     * Run with: php artisan db:seed --class=DemoDataSeeder
     *
     * Creates:
     * - 1 demo user
     * - 1 budget
     * - 7 accounts (checking, savings, 3 credit cards, cash, emergency fund)
     * - 5 category groups with ~20 categories
     * - 5 recurring transactions
     * - 3 months of realistic transactions
     */
    public function run(): void
    {
        // Create demo user
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@budgetguy.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create budget
        $budget = Budget::create([
            'name' => 'Personal Budget',
            'owner_id' => $user->id,
            'default_monthly_income' => 5500.00,
        ]);

        // Associate user with budget
        $budget->users()->attach($user->id, [
            'role' => 'owner',
            'accepted_at' => now(),
        ]);

        // Set as current budget
        $user->update(['current_budget_id' => $budget->id]);

        // Create accounts
        $accounts = $this->createAccounts($budget);

        // Create category groups and categories
        $categories = $this->createCategories($budget);

        // Create payees
        $payees = $this->createPayees($budget, $categories);

        // Create recurring transactions
        $this->createRecurringTransactions($budget, $accounts, $categories, $payees);

        // Create monthly budgets for last 3 months
        $this->createMonthlyBudgets($categories);

        // Create transactions for the last 3 months
        $this->createTransactions($budget, $user, $accounts, $categories, $payees);
    }

    private function createAccounts(Budget $budget): array
    {
        $accountsData = [
            ['name' => 'Main Checking', 'type' => 'checking', 'starting_balance' => 2500.00],
            ['name' => 'Savings Account', 'type' => 'savings', 'starting_balance' => 15000.00],
            ['name' => 'Chase Sapphire', 'type' => 'credit_card', 'starting_balance' => -450.00],
            ['name' => 'Amazon Card', 'type' => 'credit_card', 'starting_balance' => -125.00],
            ['name' => 'Capital One', 'type' => 'credit_card', 'starting_balance' => -200.00],
            ['name' => 'Cash Wallet', 'type' => 'cash', 'starting_balance' => 150.00],
        ];

        $accounts = [];
        foreach ($accountsData as $index => $data) {
            $accounts[$data['name']] = Account::create([
                'budget_id' => $budget->id,
                'name' => $data['name'],
                'type' => $data['type'],
                'starting_balance' => $data['starting_balance'],
                'sort_order' => $index,
            ]);
        }

        return $accounts;
    }

    private function createCategories(Budget $budget): array
    {
        $groupsData = [
            'Housing' => [
                ['name' => 'Rent/Mortgage', 'icon' => '🏠', 'default_amount' => 1800.00],
                ['name' => 'Utilities', 'icon' => '💡', 'default_amount' => 150.00],
                ['name' => 'Internet', 'icon' => '📶', 'default_amount' => 75.00],
                ['name' => 'Home Insurance', 'icon' => '🛡️', 'default_amount' => 100.00],
            ],
            'Transportation' => [
                ['name' => 'Gas', 'icon' => '⛽', 'default_amount' => 200.00],
                ['name' => 'Car Payment', 'icon' => '🚗', 'default_amount' => 350.00],
                ['name' => 'Car Insurance', 'icon' => '📋', 'default_amount' => 120.00],
                ['name' => 'Parking', 'icon' => '🅿️', 'default_amount' => 50.00],
            ],
            'Food & Dining' => [
                ['name' => 'Groceries', 'icon' => '🛒', 'default_amount' => 500.00],
                ['name' => 'Restaurants', 'icon' => '🍽️', 'default_amount' => 200.00],
                ['name' => 'Coffee', 'icon' => '☕', 'default_amount' => 50.00],
            ],
            'Personal' => [
                ['name' => 'Clothing', 'icon' => '👕', 'default_amount' => 100.00],
                ['name' => 'Health & Medical', 'icon' => '💊', 'default_amount' => 100.00],
                ['name' => 'Haircut', 'icon' => '✂️', 'default_amount' => 40.00],
                ['name' => 'Subscriptions', 'icon' => '📱', 'default_amount' => 50.00],
                ['name' => 'Gym', 'icon' => '💪', 'default_amount' => 45.00],
            ],
            'Entertainment & Fun' => [
                ['name' => 'Entertainment', 'icon' => '🎬', 'default_amount' => 100.00],
                ['name' => 'Hobbies', 'icon' => '🎮', 'default_amount' => 75.00],
                ['name' => 'Travel', 'icon' => '✈️', 'default_amount' => 200.00],
                ['name' => 'Gifts', 'icon' => '🎁', 'default_amount' => 50.00],
            ],
            'Savings Goals' => [
                ['name' => 'Emergency Fund', 'icon' => '🛟', 'default_amount' => 0.00],
                ['name' => 'General Savings', 'icon' => '🐷', 'default_amount' => 500.00],
            ],
        ];

        $categories = [];
        $groupOrder = 0;

        foreach ($groupsData as $groupName => $groupCategories) {
            $group = CategoryGroup::create([
                'budget_id' => $budget->id,
                'name' => $groupName,
                'sort_order' => $groupOrder++,
            ]);

            foreach ($groupCategories as $catIndex => $catData) {
                $category = Category::create([
                    'group_id' => $group->id,
                    'name' => $catData['name'],
                    'icon' => $catData['icon'],
                    'default_amount' => $catData['default_amount'],
                    'sort_order' => $catIndex,
                ]);
                $categories[$catData['name']] = $category;
            }
        }

        return $categories;
    }

    private function createPayees(Budget $budget, array $categories): array
    {
        $payeesData = [
            // Housing
            ['name' => 'Landlord - Rent', 'category' => 'Rent/Mortgage'],
            ['name' => 'Electric Company', 'category' => 'Utilities'],
            ['name' => 'Water Utility', 'category' => 'Utilities'],
            ['name' => 'Comcast Internet', 'category' => 'Internet'],

            // Transportation
            ['name' => 'Shell Gas', 'category' => 'Gas'],
            ['name' => 'Chevron', 'category' => 'Gas'],
            ['name' => 'Toyota Financial', 'category' => 'Car Payment'],
            ['name' => 'Geico Insurance', 'category' => 'Car Insurance'],
            ['name' => 'City Parking', 'category' => 'Parking'],

            // Food & Dining
            ['name' => 'Costco', 'category' => 'Groceries'],
            ['name' => 'Safeway', 'category' => 'Groceries'],
            ['name' => 'Trader Joes', 'category' => 'Groceries'],
            ['name' => 'Whole Foods', 'category' => 'Groceries'],
            ['name' => 'Chipotle', 'category' => 'Restaurants'],
            ['name' => 'Thai Kitchen', 'category' => 'Restaurants'],
            ['name' => 'Pizza Hut', 'category' => 'Restaurants'],
            ['name' => 'Starbucks', 'category' => 'Coffee'],
            ['name' => 'Local Coffee Shop', 'category' => 'Coffee'],

            // Personal
            ['name' => 'Target', 'category' => 'Clothing'],
            ['name' => 'Amazon', 'category' => null],
            ['name' => 'CVS Pharmacy', 'category' => 'Health & Medical'],
            ['name' => 'Supercuts', 'category' => 'Haircut'],
            ['name' => 'Netflix', 'category' => 'Subscriptions'],
            ['name' => 'Spotify', 'category' => 'Subscriptions'],
            ['name' => 'Planet Fitness', 'category' => 'Gym'],

            // Entertainment
            ['name' => 'AMC Theaters', 'category' => 'Entertainment'],
            ['name' => 'Steam', 'category' => 'Hobbies'],

            // Income
            ['name' => 'Employer - Paycheck', 'category' => null],
            ['name' => 'Side Gig Income', 'category' => null],
        ];

        $payees = [];
        foreach ($payeesData as $data) {
            $payee = Payee::create([
                'budget_id' => $budget->id,
                'name' => $data['name'],
                'default_category_id' => $data['category'] ? ($categories[$data['category']]->id ?? null) : null,
            ]);
            $payees[$data['name']] = $payee;
        }

        return $payees;
    }

    private function createRecurringTransactions(Budget $budget, array $accounts, array $categories, array $payees): void
    {
        $checking = $accounts['Main Checking'];
        $today = Carbon::today();

        $recurringData = [
            [
                'payee' => 'Employer - Paycheck',
                'amount' => 2750.00, // Positive for income
                'type' => 'income',
                'frequency' => 'biweekly',
                'category' => null,
                'next_date' => $today->copy()->next('Friday'),
            ],
            [
                'payee' => 'Landlord - Rent',
                'amount' => -1800.00,
                'type' => 'expense',
                'frequency' => 'monthly',
                'category' => 'Rent/Mortgage',
                'next_date' => $today->copy()->startOfMonth()->addDays(0), // 1st of month
            ],
            [
                'payee' => 'Electric Company',
                'amount' => -120.00,
                'type' => 'expense',
                'frequency' => 'monthly',
                'category' => 'Utilities',
                'next_date' => $today->copy()->startOfMonth()->addDays(14), // 15th of month
            ],
            [
                'payee' => 'Comcast Internet',
                'amount' => -75.00,
                'type' => 'expense',
                'frequency' => 'monthly',
                'category' => 'Internet',
                'next_date' => $today->copy()->startOfMonth()->addDays(9), // 10th of month
            ],
            [
                'payee' => 'Netflix',
                'amount' => -15.99,
                'type' => 'expense',
                'frequency' => 'monthly',
                'category' => 'Subscriptions',
                'next_date' => $today->copy()->startOfMonth()->addDays(4), // 5th of month
            ],
        ];

        foreach ($recurringData as $data) {
            RecurringTransaction::create([
                'budget_id' => $budget->id,
                'account_id' => $checking->id,
                'category_id' => $data['category'] ? ($categories[$data['category']]->id ?? null) : null,
                'payee_id' => $payees[$data['payee']]->id ?? null,
                'amount' => $data['amount'],
                'type' => $data['type'],
                'frequency' => $data['frequency'],
                'next_date' => $data['next_date'],
                'is_active' => true,
            ]);
        }
    }

    private function createMonthlyBudgets(array $categories): void
    {
        // Budget for 3 complete months only (not the current partial month)
        // The current month will start with $0 budgeted so user can see "Ready to Assign"
        $firstMonth = Carbon::now()->subMonths(3)->format('Y-m');
        $months = [
            $firstMonth,
            Carbon::now()->subMonths(2)->format('Y-m'),
            Carbon::now()->subMonth()->format('Y-m'),
        ];

        // YNAB-style budgeting: toBudget = startingBalances + income + expenses - budgeted
        // (expenses are negative, so they reduce the available amount)
        //
        // For $0 toBudget: budgeted = startingBalances + income + expenses
        //
        // Our totals across 3 months (approximate):
        // - Starting balances: $16,875 (includes -$775 credit card debt)
        // - Income: ~$16,500 (6 paychecks × $2,750)
        // - Expenses: ~-$13,000 (varies with random amounts)
        //
        // Total available: ~$20,375
        //
        // Default expense budgets per month: ~$4,355
        // Over 3 months: ~$13,065
        // Remaining for savings: ~$7,310
        //
        // Strategy: Budget expense defaults each month, put remaining in savings
        // First month: $10,000 to Emergency Fund (matches most of savings account)
        // Other months: $0 to Emergency Fund (it's fully funded)
        // All months: Skip General Savings default to avoid overbudgeting
        //
        // This way total budgeted ≈ ($4,355 - $500) × 3 + $10,000 = $11,565 + $10,000 = $21,565
        // Close enough to available that we'll have a small "Ready to Assign" amount

        foreach ($categories as $name => $category) {
            foreach ($months as $month) {
                $budgetedAmount = $category->default_amount;

                // Emergency Fund: $10k in first month only, then $0
                if ($name === 'Emergency Fund') {
                    $budgetedAmount = ($month === $firstMonth) ? 10000.00 : 0.00;
                }

                // General Savings: $1,500/month (surplus income goes here)
                // This absorbs the extra income beyond expense budgets
                if ($name === 'General Savings') {
                    $budgetedAmount = 1500.00;
                }

                MonthlyBudget::create([
                    'category_id' => $category->id,
                    'month' => $month,
                    'budgeted_amount' => $budgetedAmount,
                ]);
            }
        }
    }

    private function createTransactions(Budget $budget, User $user, array $accounts, array $categories, array $payees): void
    {
        $checking = $accounts['Main Checking'];
        $savings = $accounts['Savings Account'];
        $chase = $accounts['Chase Sapphire'];
        $amazon = $accounts['Amazon Card'];
        $cash = $accounts['Cash Wallet'];

        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();

        // Helper to create a transaction
        $createTx = function ($date, $account, $payee, $category, $amount, $type = 'expense', $cleared = true, $memo = null) use ($budget, $user, $categories, $payees) {
            Transaction::create([
                'budget_id' => $budget->id,
                'account_id' => $account->id,
                'category_id' => $category ? ($categories[$category]->id ?? null) : null,
                'payee_id' => $payee ? ($payees[$payee]->id ?? null) : null,
                'amount' => $amount,
                'type' => $type,
                'date' => $date,
                'cleared' => $cleared,
                'memo' => $memo,
                'created_by' => $user->id,
            ]);
        };

        // Generate transactions for each month
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            $isCurrentMonth = $monthStart->month === Carbon::now()->month;

            // Paychecks - every 2 weeks (2-3 per month)
            $payDate = $monthStart->copy()->next('Friday');
            while ($payDate <= $monthEnd) {
                if ($payDate <= $endDate) {
                    $cleared = !$isCurrentMonth || $payDate < Carbon::now()->subDays(3);
                    $createTx($payDate, $checking, 'Employer - Paycheck', null, 2750.00, 'income', $cleared);
                }
                $payDate->addWeeks(2);
            }

            // Rent - 1st of month
            $rentDate = $monthStart->copy()->addDays(0);
            if ($rentDate <= $endDate) {
                $cleared = !$isCurrentMonth || $rentDate < Carbon::now()->subDays(3);
                $createTx($rentDate, $checking, 'Landlord - Rent', 'Rent/Mortgage', -1800.00, 'expense', $cleared);
            }

            // Utilities - mid month
            $utilityDate = $monthStart->copy()->addDays(14);
            if ($utilityDate <= $endDate) {
                $cleared = !$isCurrentMonth || $utilityDate < Carbon::now()->subDays(3);
                $amount = -1 * rand(100, 180); // Variable utility bill
                $createTx($utilityDate, $checking, 'Electric Company', 'Utilities', $amount, 'expense', $cleared);
            }

            // Internet
            $internetDate = $monthStart->copy()->addDays(9);
            if ($internetDate <= $endDate) {
                $cleared = !$isCurrentMonth || $internetDate < Carbon::now()->subDays(3);
                $createTx($internetDate, $checking, 'Comcast Internet', 'Internet', -75.00, 'expense', $cleared);
            }

            // Car payment - 5th
            $carDate = $monthStart->copy()->addDays(4);
            if ($carDate <= $endDate) {
                $cleared = !$isCurrentMonth || $carDate < Carbon::now()->subDays(3);
                $createTx($carDate, $checking, 'Toyota Financial', 'Car Payment', -350.00, 'expense', $cleared);
            }

            // Car insurance - quarterly, check every 3rd month
            if ($monthStart->month % 3 === 1) {
                $insuranceDate = $monthStart->copy()->addDays(7);
                if ($insuranceDate <= $endDate) {
                    $cleared = !$isCurrentMonth || $insuranceDate < Carbon::now()->subDays(3);
                    $createTx($insuranceDate, $checking, 'Geico Insurance', 'Car Insurance', -360.00, 'expense', $cleared, 'Quarterly payment');
                }
            }

            // Netflix subscription
            $netflixDate = $monthStart->copy()->addDays(4);
            if ($netflixDate <= $endDate) {
                $cleared = !$isCurrentMonth || $netflixDate < Carbon::now()->subDays(3);
                $createTx($netflixDate, $checking, 'Netflix', 'Subscriptions', -15.99, 'expense', $cleared);
            }

            // Spotify
            $spotifyDate = $monthStart->copy()->addDays(11);
            if ($spotifyDate <= $endDate) {
                $cleared = !$isCurrentMonth || $spotifyDate < Carbon::now()->subDays(3);
                $createTx($spotifyDate, $checking, 'Spotify', 'Subscriptions', -10.99, 'expense', $cleared);
            }

            // Gym
            $gymDate = $monthStart->copy()->addDays(0);
            if ($gymDate <= $endDate) {
                $cleared = !$isCurrentMonth || $gymDate < Carbon::now()->subDays(3);
                $createTx($gymDate, $checking, 'Planet Fitness', 'Gym', -24.99, 'expense', $cleared);
            }

            // Weekly grocery shopping (4 per month)
            for ($week = 0; $week < 4; $week++) {
                $groceryDate = $monthStart->copy()->addDays(($week * 7) + rand(5, 7));
                if ($groceryDate <= $endDate && $groceryDate <= $monthEnd) {
                    $cleared = !$isCurrentMonth || $groceryDate < Carbon::now()->subDays(3);
                    $stores = ['Costco', 'Safeway', 'Trader Joes', 'Whole Foods'];
                    $store = $stores[array_rand($stores)];
                    $amount = -1 * (rand(80, 180) + (rand(0, 99) / 100));
                    $createTx($groceryDate, $chase, $store, 'Groceries', round($amount, 2), 'expense', $cleared);
                }
            }

            // Gas - 2x per month
            for ($i = 0; $i < 2; $i++) {
                $gasDate = $monthStart->copy()->addDays(rand(1, 28));
                if ($gasDate <= $endDate && $gasDate <= $monthEnd) {
                    $cleared = !$isCurrentMonth || $gasDate < Carbon::now()->subDays(3);
                    $stations = ['Shell Gas', 'Chevron'];
                    $station = $stations[array_rand($stations)];
                    $amount = -1 * (rand(40, 65) + (rand(0, 99) / 100));
                    $createTx($gasDate, $chase, $station, 'Gas', round($amount, 2), 'expense', $cleared);
                }
            }

            // Restaurants - 4-6 per month
            $restaurantCount = rand(4, 6);
            for ($i = 0; $i < $restaurantCount; $i++) {
                $diningDate = $monthStart->copy()->addDays(rand(1, 28));
                if ($diningDate <= $endDate && $diningDate <= $monthEnd) {
                    $cleared = !$isCurrentMonth || $diningDate < Carbon::now()->subDays(3);
                    $restaurants = ['Chipotle', 'Thai Kitchen', 'Pizza Hut'];
                    $restaurant = $restaurants[array_rand($restaurants)];
                    $amount = -1 * (rand(15, 55) + (rand(0, 99) / 100));
                    $createTx($diningDate, $chase, $restaurant, 'Restaurants', round($amount, 2), 'expense', $cleared);
                }
            }

            // Coffee - 6-10 per month
            $coffeeCount = rand(6, 10);
            for ($i = 0; $i < $coffeeCount; $i++) {
                $coffeeDate = $monthStart->copy()->addDays(rand(1, 28));
                if ($coffeeDate <= $endDate && $coffeeDate <= $monthEnd) {
                    $cleared = !$isCurrentMonth || $coffeeDate < Carbon::now()->subDays(3);
                    $shops = ['Starbucks', 'Local Coffee Shop'];
                    $shop = $shops[array_rand($shops)];
                    $amount = -1 * (rand(4, 8) + (rand(0, 99) / 100));
                    $createTx($coffeeDate, $amazon, $shop, 'Coffee', round($amount, 2), 'expense', $cleared);
                }
            }

            // Amazon purchases - 2-4 per month
            $amazonCount = rand(2, 4);
            for ($i = 0; $i < $amazonCount; $i++) {
                $amazonDate = $monthStart->copy()->addDays(rand(1, 28));
                if ($amazonDate <= $endDate && $amazonDate <= $monthEnd) {
                    $cleared = !$isCurrentMonth || $amazonDate < Carbon::now()->subDays(3);
                    $amazonCategories = ['Hobbies', 'Clothing', 'Entertainment'];
                    $cat = $amazonCategories[array_rand($amazonCategories)];
                    $amount = -1 * (rand(15, 85) + (rand(0, 99) / 100));
                    $createTx($amazonDate, $amazon, 'Amazon', $cat, round($amount, 2), 'expense', $cleared);
                }
            }

            // Occasional entertainment
            if (rand(1, 100) > 30) {
                $entertainmentDate = $monthStart->copy()->addDays(rand(10, 25));
                if ($entertainmentDate <= $endDate && $entertainmentDate <= $monthEnd) {
                    $cleared = !$isCurrentMonth || $entertainmentDate < Carbon::now()->subDays(3);
                    $amount = -1 * (rand(15, 40) + (rand(0, 99) / 100));
                    $createTx($entertainmentDate, $chase, 'AMC Theaters', 'Entertainment', round($amount, 2), 'expense', $cleared);
                }
            }

            // Cash withdrawal 1-2x per month
            $withdrawalCount = rand(1, 2);
            for ($i = 0; $i < $withdrawalCount; $i++) {
                $withdrawDate = $monthStart->copy()->addDays(rand(1, 25));
                if ($withdrawDate <= $endDate && $withdrawDate <= $monthEnd) {
                    $cleared = !$isCurrentMonth || $withdrawDate < Carbon::now()->subDays(3);
                    // Transfer from checking to cash
                    $tx1 = Transaction::create([
                        'budget_id' => $budget->id,
                        'account_id' => $checking->id,
                        'amount' => -100.00,
                        'type' => 'transfer',
                        'date' => $withdrawDate,
                        'cleared' => $cleared,
                        'memo' => 'ATM Withdrawal',
                        'created_by' => $user->id,
                    ]);

                    $tx2 = Transaction::create([
                        'budget_id' => $budget->id,
                        'account_id' => $cash->id,
                        'amount' => 100.00,
                        'type' => 'transfer',
                        'date' => $withdrawDate,
                        'cleared' => $cleared,
                        'transfer_pair_id' => $tx1->id,
                        'memo' => 'ATM Withdrawal',
                        'created_by' => $user->id,
                    ]);

                    $tx1->update(['transfer_pair_id' => $tx2->id]);
                }
            }

            // Monthly savings transfer
            $savingsDate = $monthStart->copy()->addDays(2);
            if ($savingsDate <= $endDate) {
                $cleared = !$isCurrentMonth || $savingsDate < Carbon::now()->subDays(3);
                $tx1 = Transaction::create([
                    'budget_id' => $budget->id,
                    'account_id' => $checking->id,
                    'amount' => -500.00,
                    'type' => 'transfer',
                    'date' => $savingsDate,
                    'cleared' => $cleared,
                    'memo' => 'Monthly savings',
                    'created_by' => $user->id,
                ]);

                $tx2 = Transaction::create([
                    'budget_id' => $budget->id,
                    'account_id' => $savings->id,
                    'amount' => 500.00,
                    'type' => 'transfer',
                    'date' => $savingsDate,
                    'cleared' => $cleared,
                    'transfer_pair_id' => $tx1->id,
                    'memo' => 'Monthly savings',
                    'created_by' => $user->id,
                ]);

                $tx1->update(['transfer_pair_id' => $tx2->id]);
            }

            // Move to next month
            $currentDate = $monthEnd->copy()->addDay();
        }

        // Add a few side gig income transactions sporadically
        $sideGigDate = Carbon::now()->subMonth()->addDays(rand(5, 20));
        if ($sideGigDate <= $endDate) {
            $createTx($sideGigDate, $checking, 'Side Gig Income', null, 350.00, 'income', true, 'Freelance project');
        }

        // Add haircut every 6 weeks-ish
        $haircutDate = Carbon::now()->subMonths(2)->addDays(10);
        if ($haircutDate <= $endDate) {
            $createTx($haircutDate, $cash, 'Supercuts', 'Haircut', -25.00, 'expense', true);
        }
        $haircutDate2 = Carbon::now()->subDays(20);
        if ($haircutDate2 <= $endDate) {
            $createTx($haircutDate2, $cash, 'Supercuts', 'Haircut', -25.00, 'expense', true);
        }

        // Pharmacy visit
        $pharmacyDate = Carbon::now()->subMonth()->addDays(5);
        if ($pharmacyDate <= $endDate) {
            $createTx($pharmacyDate, $amazon, 'CVS Pharmacy', 'Health & Medical', -32.50, 'expense', true);
        }
    }
}
