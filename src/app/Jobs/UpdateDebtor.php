<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Const\ImportJobStatus;
use App\Services\DebtorService;
use App\Events\ImportJobCompleted;
use Illuminate\Support\Facades\Log;
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

                $msg = "ImportJob {$this->id}: Updating debtor and institution: " . json_encode($line);
                $this->log($msg);

                $debtorService->updateDebtor($line['cuit'], $line['max_situation'], $line['amount']);
                $institutionService->updateInstitution($line['institution'], $line['amount']);
                $processedRows++;
            }
            $importJob->update(['processed_rows' => $importJob->processed_rows + $processedRows]);

            $msg = "ImportJob {$this->id}: {$importJob->processed_rows} rows updated";
            $this->log($msg);

            if ($importJob->total_rows == $importJob->processed_rows) {
                $importJob->update(['status' => ImportJobStatus::DONE]);
                ImportJobCompleted::dispatch($importJob);
            }

            $msg = "ImportJob {$this->id}: updated finished";
            $this->log($msg);

        } catch (\Exception $e) {
            echo $msg . "\n";
            Log::channel('import')->error($msg);
        }
    }

    public function log(string $msg): void
    {
        echo $msg . "\n";
        Log::channel('import')->info($msg);

    }
}
