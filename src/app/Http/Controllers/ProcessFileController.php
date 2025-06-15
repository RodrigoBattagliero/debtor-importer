<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DebtorDispatcherService;

class ProcessFileController extends Controller
{
    public function __construct(
        private DebtorDispatcherService $debtorDispatcherService
    ) { }
        

    public function processFile(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'email' => 'required|string|email',
        ]);
        $this->debtorDispatcherService->createAndDispatch($request->filename, $request->email);
        return response()->json('Ok');
    }
}
