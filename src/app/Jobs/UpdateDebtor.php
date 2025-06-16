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
                $processedRows++;
                $msg = "ImportJob {$this->id}: Updating debtor and institution: " . json_encode($line);
                $this->log($msg);

                $debtorService->updateDebtor($line['cuit'], $line['max_situation'], $line['amount']);
                $institutionService->updateInstitution($line['institution'], $line['amount']);
            }

            $total_rows = $importJob->processed_rows + $processedRows;
            $importJob->processed_rows = $total_rows;

            $msg = "ImportJob {$this->id}: total: {$importJob->total_rows}";
            $this->log($msg);

            $msg = "ImportJob {$this->id}: partial: {$importJob->processed_rows} ";
            $this->log($msg);

            if ($importJob->total_rows == $importJob->processed_rows) {
                $msg = "ImportJob {$this->id}: DONE";
                $this->log($msg);

                $importJob->status = ImportJobStatus::DONE;
                ImportJobCompleted::dispatch($importJob);
            }

            $importJob->save();

            $msg = "ImportJob {$this->id}: updated finished";
            $this->log($msg);

        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            Log::channel('import')->error($msg);
        }
    }

    public function log(string $msg): void
    {
        echo $msg . "\n";
        Log::channel('import')->info($msg);

    }
}
