<?php

namespace App\Services;

use App\Const\ImportJobStatus;
use App\Jobs\ProcessFile;
use App\Models\ImportJob;

class DebtorDispatcherService
{
    public function createDispatcher(string $filename): void
    {
        $importJob = new ImportJob();
        $importJob->status = ImportJobStatus::PENDING;
        $importJob->file = $filename;
        $importJob->save();
        
        ProcessFile::dispatch($importJob->id);
    }
}