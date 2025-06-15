<?php 

namespace App\Services;

use App\Events\FileUploaded;

class UploadService
{
    public function upload($file, $email): void
    {
        $path = $file->store();
        FileUploaded::dispatch($path, $email);
    }
}