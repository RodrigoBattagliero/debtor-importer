<?php

namespace App\Jobs;

use App\Const\ImportJobStatus;
use App\Events\ImportJobCompleted;
use App\Models\ImportJob;
use App\Services\DebtorService;
use App\Services\InstitutionService;
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
        try {

            $processedRows = 0;
            $importJob = ImportJob::find($this->id);
            if (!$importJob) {
                throw new \Exception('Not importedJob found.');
            }
            foreach ($this->data as $line) {
                $debtorService->updateDebtor($line['cuit'], $line['max_situation'], $line['amount']);
                $institutionService->updateInstitution($line['institution'], $line['amount']);
                $processedRows++;
            }
            $importJob->update(['processed_rows' => $importJob->processed_rows + $processedRows]);

            if ($importJob->total_rows == $importJob->processed_rows) {
                $importJob->update(['status' => ImportJobStatus::DONE]);
                ImportJobCompleted::dispatch($importJob);
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
