<?php

namespace App\Listeners;

use App\Events\FileUploaded;
use App\Services\DebtorDispatcherService;

class CreateNewImport
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private DebtorDispatcherService $debtorDispatcherService
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FileUploaded $event): void
    {
        $this->debtorDispatcherService->createAndDispatch($event->file, $event->email);
    }
}
