<?php

namespace App\Services;

use App\Models\Institution;

class InstitutionService
{
    public function updateInstitution($code, $amount)
    {
        $institution = Institution::where('code', $code)->first();
        if (!$institution) {
            $institution = $this->createInstitution($code);
        }
        $institution->amount += $amount;

        $institution->save();

    }

    public function createInstitution(string $code): Institution
    {
        $institution = new Institution();
        $institution->code = $code;
        $institution->amount = 0;
        return $institution;
    }

    public function get(string $code): ?Institution
    {
        return Institution::where('code', $code)->first();
    }
}