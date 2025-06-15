<?php

namespace App\Jobs;

use App\Const\ImportJobStatus;
use App\Models\ImportJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $importJob = ImportJob::find($this->id);
        if (!$importJob) {
            return;
        }
        $importJob->update(['status' => ImportJobStatus::IN_PROGRESS]);


        $path = Storage::path($importJob->file);
        $currentChunk = [];
        $totalRows = 0;

        try {
            File::lines($path)
                ->each(function ($line) use (&$currentChunk, &$totalRows, $importJob) 
                    {
                        $data = [];

                        $data['institution'] = (int) substr($line, 0, 5);
                        $data['cuit'] = substr($line, 13, 11);
                        $data['max_situation'] = (int) substr($line, 27, 2);
                        $data['amount'] = (float) substr($line, 29, 12);

                        $currentChunk[] = $data;

                        if (count($currentChunk) >= $this::MAX_CHUNCK_BATCH) {
                            UpdateDebtor::dispatch($importJob->id, $currentChunk);
                            $currentChunk = [];
                        }
                        $totalRows++;
                    }
                );

        } catch (\Exception $e) {
            die($e->getMessage());
        }

        if (!empty($currentChunk)) {
            UpdateDebtor::dispatch($importJob->id, $currentChunk);
        }

        $importJob->update(['total_rows' => $totalRows]);
    }
}
