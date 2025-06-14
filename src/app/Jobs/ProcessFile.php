<?php

namespace App\Jobs;

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
        private string $filename
    ) { }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $path = Storage::path($this->filename);
        $currentChunk = [];

        try {
            File::lines($path)
                ->each(function ($line) use (&$currentChunk) 
                    {
                        $currentChunk[]['institution'] = (int) substr($line, 0, 5);
                        $currentChunk[]['cuit'] = substr($line, 13, 11);
                        $currentChunk[]['max_situation'] = (int) substr($line, 27, 2);
                        $currentChunk[]['amount'] = (float) substr($line, 29, 12);

                        if (count($currentChunk) >= $this::MAX_CHUNCK_BATCH) {
                            UpdateDebtor::dispatch($currentChunk);
                            $currentChunk = [];
                        }
                    }
                );

        } catch (\Exception $e) {
            die($e->getMessage());
        }

        if (!empty($currentChunk)) {
            UpdateDebtor::dispatch($currentChunk);
        }
    }
}
