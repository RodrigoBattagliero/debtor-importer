<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFile;

class DebtorsController extends Controller
{
    public function index()
    {
        $filename = 'deudores.txt';
        ProcessFile::dispatch($filename);
        return response()->json('Ok');
    }
}
