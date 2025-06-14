<?php

namespace App\Jobs;

use App\Services\DebtorService;
use App\Services\InstitutionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateDebtor implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
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
        foreach ($this->data as $line) {
            $debtorService->updateDebtor($line['cuit'], $line['max_situation'], $line['amount']);
            $institutionService->updateInstitution($line['institution'], $line['amount']);
        }
    }
}
