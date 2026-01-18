<?php

namespace App\Console\Commands;

use App\Http\Controllers\RecurringTransactionController;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all due recurring transactions and create actual transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing recurring transactions...');

        $count = RecurringTransactionController::processDueTransactions();

        $this->info("Created {$count} transaction(s) from recurring templates.");

        return Command::SUCCESS;
    }
}
