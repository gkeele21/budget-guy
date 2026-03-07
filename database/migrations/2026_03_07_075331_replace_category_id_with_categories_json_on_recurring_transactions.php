<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add categories JSON column
        Schema::table('recurring_transactions', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('category_id');
        });

        // Migrate existing data: convert category_id + amount into JSON format
        DB::table('recurring_transactions')->whereNotNull('category_id')->eachById(function ($row) {
            DB::table('recurring_transactions')
                ->where('id', $row->id)
                ->update([
                    'categories' => json_encode([
                        ['category_id' => $row->category_id, 'amount' => (float) $row->amount],
                    ]),
                ]);
        });

        // Drop category_id column
        Schema::table('recurring_transactions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('recurring_transactions', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('account_id')->constrained()->nullOnDelete();
        });

        // Migrate back: extract first category from JSON
        DB::table('recurring_transactions')->whereNotNull('categories')->eachById(function ($row) {
            $categories = json_decode($row->categories, true);
            if (!empty($categories)) {
                DB::table('recurring_transactions')
                    ->where('id', $row->id)
                    ->update(['category_id' => $categories[0]['category_id']]);
            }
        });

        Schema::table('recurring_transactions', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};
