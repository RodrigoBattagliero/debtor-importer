<?php

namespace App\Http\Controllers;

use App\Services\DebtorService;
use Illuminate\Http\Request;

class DebtorsController extends Controller
{
    public function __construct(
        private DebtorService $debtorService
    ) { }

    public function get(request $request)
    {
        $cuit = $request->cuit;
        return response()->json($this->debtorService->get($cuit));
    }

    public function top(Request $request)
    {
        $n = $request->n;
        return response()->json($this->debtorService->top($n));
    }
}
