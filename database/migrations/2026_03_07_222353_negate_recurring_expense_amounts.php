<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Negate amounts for recurring expense transactions so they are stored as negative.
     * Also negate the category split amounts within the categories JSON.
     */
    public function up(): void
    {
        // Negate the main amount for expense recurring transactions
        DB::table('recurring_transactions')
            ->where('type', 'expense')
            ->where('amount', '>', 0)
            ->update(['amount' => DB::raw('-amount')]);

        // Negate category split amounts in the JSON for expense recurring transactions
        DB::table('recurring_transactions')
            ->where('type', 'expense')
            ->whereNotNull('categories')
            ->eachById(function ($row) {
                $categories = json_decode($row->categories, true);
                if (!is_array($categories)) return;

                $updated = false;
                foreach ($categories as &$cat) {
                    if (isset($cat['amount']) && (float) $cat['amount'] > 0) {
                        $cat['amount'] = -(float) $cat['amount'];
                        $updated = true;
                    }
                }

                if ($updated) {
                    DB::table('recurring_transactions')
                        ->where('id', $row->id)
                        ->update(['categories' => json_encode($categories)]);
                }
            });
    }

    /**
     * Reverse: make expense amounts positive again.
     */
    public function down(): void
    {
        DB::table('recurring_transactions')
            ->where('type', 'expense')
            ->where('amount', '<', 0)
            ->update(['amount' => DB::raw('ABS(amount)')]);

        DB::table('recurring_transactions')
            ->where('type', 'expense')
            ->whereNotNull('categories')
            ->eachById(function ($row) {
                $categories = json_decode($row->categories, true);
                if (!is_array($categories)) return;

                $updated = false;
                foreach ($categories as &$cat) {
                    if (isset($cat['amount']) && (float) $cat['amount'] < 0) {
                        $cat['amount'] = abs((float) $cat['amount']);
                        $updated = true;
                    }
                }

                if ($updated) {
                    DB::table('recurring_transactions')
                        ->where('id', $row->id)
                        ->update(['categories' => json_encode($categories)]);
                }
            });
    }
};
