<?php

namespace App\Jobs;

use App\Const\ImportJobStatus;
use App\Events\ImportJobCompleted;
use App\Models\ImportJob;
use App\Services\DebtorService;
use App\Services\InstitutionService;
use Exception;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateDebtor implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $id,
        private array $data
    ) { }

    /**
     * Execute the job.
     */
    public function handle(
        DebtorService $debtorService,
        InstitutionService $institutionService,

    ): void
    {
        $processedRows = 0;
        $importJob = ImportJob::find($this->id);
        if (!$importJob) {
            return;
        }
        
        try {
            foreach ($this->data as $line) {
                $debtorService->updateDebtor($line['cuit'], $line['max_situation'], $line['amount']);
                $institutionService->updateInstitution($line['institution'], $line['amount']);
                $processedRows++;
            }
        } catch (Exception $e) {    
            echo $e->getMessage();
        }

        $processedTotalRows = $importJob->processed_rows + $processedRows;
        $importJob->update(['processed_rows' => $processedTotalRows]);

        if ($importJob->total_rows == $importJob->processed_rows) {
            $importJob->update(['status' => ImportJobStatus::DONE]);
            ImportJobCompleted::dispatch($importJob);
        }
    }
}
