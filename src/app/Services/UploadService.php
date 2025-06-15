<?php 

namespace App\Services;

use App\Events\FileUploaded;

class UploadService
{
    public function upload($file): void
    {
        $path = $file->store();
        FileUploaded::dispatch($path);
    }
}