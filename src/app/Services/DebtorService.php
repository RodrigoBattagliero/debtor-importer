<?php

namespace App\Services;

use App\Models\Debtor;

class DebtorService
{
    public function updateDebtor($cuit, $max_situation, $amount)
    {
        $debtor = Debtor::where('cuit', $cuit)->first();

        if (!$debtor) {
            $debtor = $this->createDebtor($cuit);
        }

        if ($max_situation > $debtor->max_situation) {
            $debtor->max_situation = $max_situation;
        }

        $debtor->amount += $amount;

        $debtor->save();
    }

    public function createDebtor(string $cuit): Debtor
    {
        $debtor = new Debtor();
        $debtor->cuit = $cuit;
        $debtor->amount = 0;
        $debtor->max_situation = 0;
        return $debtor;
    }

    public function get(string $cuit): ?Debtor
    {
        return Debtor::where('cuit', $cuit)->first();
    }

    public function top(?int $n)
    {
        return Debtor::orderBy('amount')
            ->take($n)
            ->get();
    }
}