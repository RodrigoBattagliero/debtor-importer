<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\UploadService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadFileController extends Controller
{
    public function __construct(
        private UploadService $uploadService
    ) { }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:csv,txt|max:2048',
                'email' => 'required|string',
            ]);
            $this->uploadService->upload($request->file('file'), $request->email);

            return response(null, Response::HTTP_NO_CONTENT);
            
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json(["message"=> $e->getMessage()], Response::HTTP_BAD_REQUEST));
        }
    }
}
