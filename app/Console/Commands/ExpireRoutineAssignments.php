<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RoutineAssignment;

class ExpireRoutineAssignments extends Command
{
    protected $signature   = 'assignments:expire';
    protected $description = 'Deactivate routine assignments older than 24 hours';

    public function handle()
    {
        $count = RoutineAssignment::where('is_active', true)
            ->where('assigned_date', '<', now()->toDateString())
            ->update(['is_active' => false]);

        $this->info("Expired {$count} assignment(s).");
    }
}
