<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean {days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up read notifications older than a given number of days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->argument('days');
        $date = Carbon::now()->subDays($days);

        $deletedCount = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('created_at', '<', $date)
            ->delete();

        $this->info("Successfully deleted {$deletedCount} read notifications older than {$days} days.");
    }
}
