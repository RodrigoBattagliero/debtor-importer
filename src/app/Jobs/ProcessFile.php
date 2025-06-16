<?php

namespace App\Jobs;

use App\Const\ImportJobStatus;
use App\Models\ImportJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ProcessFile implements ShouldQueue
{
    use Queueable;

    private const MAX_CHUNCK_BATCH = 100;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $id
    ) { }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $msg = "ImportJob {$this->id}: Starting";
            $this->log($msg);

            $importJob = ImportJob::find($this->id);
            if (!$importJob) {
                throw new \Exception('Not importedJob found.');
            }
            $importJob->update(['status' => ImportJobStatus::IN_PROGRESS]);
    
    
            $path = Storage::path($importJob->file);
            $currentChunk = [];
            $totalRows = 0;

            File::lines($path)
                ->each(function ($row) use (&$currentChunk, &$totalRows, $importJob) 
                    {
                        $data = $this->getDataFromRow($row);
                        $msg = "ImportJob {$this->id}: from: {$row} to: " . json_encode($data);
                        $this->log($msg);

                        $currentChunk[] = $data;

                        if (count($currentChunk) >= $this::MAX_CHUNCK_BATCH) {
                            UpdateDebtor::dispatch($importJob->id, $currentChunk);
                            $currentChunk = [];
                        }
                        $totalRows++;
                    }
                );

            if (!empty($currentChunk)) {
                UpdateDebtor::dispatch($importJob->id, $currentChunk);
            }
    
            $importJob->update(['total_rows' => $totalRows]);

            $msg = "ImportJob {$this->id}: {$totalRows} rows processed";
            $this->log($msg);

            $msg = "ImportJob {$this->id}: Finished";
            $this->log($msg);

        } catch (\Exception $e) {
            $msg = "ImportJob {$this->id}: Error: " . $e->getMessage();
            echo $msg . "\n";
            Log::channel('import')->error($msg);
        }
        
    }

    public function getDataFromRow(string $row): array
    {
        $data = [];
        $data['institution'] = (int) substr($row, 0, 5);
        $data['cuit'] = substr($row, 13, 11);
        $data['max_situation'] = (int) substr($row, 27, 2);
        $data['amount'] = (float) substr($row, 29, 12);

        return $data;
    }

    public function log(string $msg): void
    {
        echo $msg . "\n";
        Log::channel('import')->info($msg);

    }
}
