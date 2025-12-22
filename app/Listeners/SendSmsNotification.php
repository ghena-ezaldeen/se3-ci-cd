<?php

namespace App\Listeners;

use App\Events\AccountActivityEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSmsNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AccountActivityEvent $event): void
    {
        //
        logger("SMS sent for {$event->type}");
    }
}
