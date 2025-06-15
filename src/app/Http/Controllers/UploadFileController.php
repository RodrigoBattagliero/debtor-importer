<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UploadService;

class UploadFileController extends Controller
{
    public function __construct(
        private UploadService $uploadService
    ) { }

    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv|max:2048']);
        $this->uploadService->upload($request->file('file'));
        return response()->json('Ok');
    }
}
