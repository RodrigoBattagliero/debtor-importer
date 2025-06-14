<?php

namespace App\Jobs;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessFile implements ShouldQueue
{
    use Queueable;

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
        $lineNumber = 0;
        $currentChunk = [];
        $totalRows = 0;

        try {
            File::lines($path)
                ->each(function ($line) use (&$lineNumber, &$currentChunk, &$totalRows) 
                    {    
                        $totalRows++;
                        $da['institution'] = (int) substr($line, 0, 5);
                        $da['cuit'] = substr($line, 13, 11);
                        $da['max_situation'] = (int) substr($line, 27, 2);
                        $da['amount'] = (float) substr($line, 29, 12);
                        $currentChunk[] =  $da;
                    
                        $lineNumber++;

                        if (count($currentChunk) >= 100) {
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
