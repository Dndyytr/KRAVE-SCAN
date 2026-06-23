<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Services\AutomationEngine;

class TriggerOrderAutomations
{
    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        AutomationEngine::trigger($event);
    }
}
