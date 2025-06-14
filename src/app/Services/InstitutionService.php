<?php

namespace App\Services;

use App\Models\Institution;

class InstitutionService
{
    public function updateInstitution($code, $amount)
    {
        $institution = Institution::where('code', $code)->first();
        if (!$institution) {
            $institution = new Institution();
            $institution->code = $code;
            $institution->amount = 0;
        }
        $institution->amount += $amount;

        $institution->save();

    }
}