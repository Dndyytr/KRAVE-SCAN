<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;

        if ($user) {
            app(ActivityLogger::class)->log(
                action: 'logout',
                model: $user,
                userId: $user->id,
                branchId: $user->branch_id
            );
        }
    }
}
