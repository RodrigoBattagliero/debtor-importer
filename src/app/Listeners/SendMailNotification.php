<?php

namespace App\Listeners;

use App\Events\ImportJobCompleted;
use App\Mail\ImportCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMailNotification
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
    public function handle(ImportJobCompleted $event): void
    {
        Mail::to($event->importJob->email_to_notify)
            ->queue(new ImportCompleted());
    }
}
