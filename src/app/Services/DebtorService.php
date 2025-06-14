<?php

namespace App\Services;

use App\Models\Debtor;

class DebtorService
{
    public function updateDebtor($cuit, $max_situation, $amount)
    {
        $debtor = Debtor::where('cuit', $cuit)->first();
        if (!$debtor) {
            $debtor = new Debtor();
            $debtor->cuit = $cuit;
            $debtor->amount = 0;
            $debtor->max_situation = 0;
        }
        if ($debtor->max_situation > $max_situation) {
            $debtor->max_situation = $max_situation;
        }
        $debtor->amount += $amount;

        $debtor->save();
    }
}