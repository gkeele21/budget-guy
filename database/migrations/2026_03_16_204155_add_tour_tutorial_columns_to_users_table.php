<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_completed_plan_tutorial')->default(false)->after('has_completed_setup_tutorial');
            $table->boolean('has_completed_transactions_tutorial')->default(false)->after('has_completed_plan_tutorial');
            $table->boolean('has_completed_splits_tutorial')->default(false)->after('has_completed_transactions_tutorial');
            $table->boolean('has_completed_recurring_tutorial')->default(false)->after('has_completed_splits_tutorial');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'has_completed_plan_tutorial',
                'has_completed_transactions_tutorial',
                'has_completed_splits_tutorial',
                'has_completed_recurring_tutorial',
            ]);
        });
    }
};
