<?php

namespace App\Http\Controllers;

use App\Services\InstitutionService;
use Illuminate\Http\Request;

class InstitutionsController extends Controller
{
    public function __construct(
        private InstitutionService $institutionService
    )
    {
        
    }
    public function get(Request $request)
    {
        $code = $request->code;
        return response()->json($this->institutionService->get($code));
    }
}
