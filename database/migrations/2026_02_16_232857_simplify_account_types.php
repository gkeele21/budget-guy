<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert ENUM to VARCHAR for extensibility
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type VARCHAR(20) NOT NULL");

        // Migrate existing type values
        DB::table('accounts')->where('type', 'checking')->update(['type' => 'bank']);
        DB::table('accounts')->where('type', 'savings')->update(['type' => 'bank']);
        DB::table('accounts')->where('type', 'digital')->update(['type' => 'bank']);
        DB::table('accounts')->where('type', 'credit_card')->update(['type' => 'credit']);

        // Add is_on_budget flag for future loan/tracking accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->boolean('is_on_budget')->default(true)->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('is_on_budget');
        });

        // Reverse data migration (lossy: all bank -> checking)
        DB::table('accounts')->where('type', 'bank')->update(['type' => 'checking']);
        DB::table('accounts')->where('type', 'credit')->update(['type' => 'credit_card']);

        // Revert to ENUM
        DB::statement("ALTER TABLE accounts MODIFY COLUMN type ENUM('checking', 'savings', 'credit_card', 'cash') NOT NULL");
    }
};
