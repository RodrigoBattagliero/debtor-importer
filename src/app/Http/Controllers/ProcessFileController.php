<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\DebtorDispatcherService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcessFileController extends Controller
{
    public function __construct(
        private DebtorDispatcherService $debtorDispatcherService
    ) { }
        

    public function processFile(Request $request)
    {
        try {
            $request->validate([
                'filename' => 'required|string',
                'email' => 'required|string|email',
            ]);
            $this->debtorDispatcherService->createAndDispatch($request->filename, $request->email);

            return response(null, Response::HTTP_NO_CONTENT);
            
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json(["message"=> $e->getMessage()], Response::HTTP_BAD_REQUEST));
        }
    }
}
