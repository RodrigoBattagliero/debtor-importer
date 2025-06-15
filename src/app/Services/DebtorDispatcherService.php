<?php

namespace App\Services;

use App\Const\ImportJobStatus;
use App\Jobs\ProcessFile;
use App\Models\ImportJob;

class DebtorDispatcherService
{
    public function createAndDispatch(string $filename, string $email): void
    {
        $importJob = new ImportJob();
        $importJob->status = ImportJobStatus::PENDING;
        $importJob->email_to_notify = $email;
        $importJob->file = $filename;
        $importJob->save();
        
        ProcessFile::dispatch($importJob->id);
    }
}