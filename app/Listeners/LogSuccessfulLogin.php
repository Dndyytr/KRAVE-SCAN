<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        app(ActivityLogger::class)->log(
            action: 'login',
            model: $user,
            userId: $user->id,
            branchId: $user->branch_id
        );
    }
}
