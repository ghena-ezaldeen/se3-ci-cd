<?php

namespace App\Listeners;

use App\Events\AccountActivityEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification
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

        $account = $event->account;
        $user = $account->owner;

        Mail::raw(
            $this->emailBody($event),
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Transaction Notification');
            }
        );
        //
        // Mail::to($event->account->user->email)->send(...);
        logger("Email sent for {$event->type}");
    }

    private function emailBody($transaction): string
    {
        return match ($transaction->type) {
            'deposit'  => "Deposit was successful.",
            'withdraw' => "Withdrawal was successful.",
            'transfer' => "Transfer was successful.",
            default    => "Transaction completed successfully."
        };
    }
}
