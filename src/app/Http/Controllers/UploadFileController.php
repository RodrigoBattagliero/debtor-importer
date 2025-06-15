<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UploadService;
use Exception;

class UploadFileController extends Controller
{
    public function __construct(
        private UploadService $uploadService
    ) { }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv|max:2048',
            'email' => 'required|string',
        ]);
        $this->uploadService->upload($request->file('file'), $request->email);
        return response()->json('Ok');
    }
}
