<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Jobs\ProcessFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadFileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_no_email()
    {
        $content = "0000720231111200428278520001 1526,0      ,0          ,0          ,0          ,0          ,0          1526,0      ,0          ,0          ,0          0           0000000   ";
        $file = UploadedFile::fake()->createWithContent('test.txt', $content);
        $data = [
            'file' => $file, 
        ];

        $response = $this->post('api/deudores/upload', $data);
                
        $response->assertBadRequest();
    }

    public function test_upload_no_file()
    {
        $data = [
            'email' => 'test@test.com',
        ];

        $response = $this->post('api/deudores/upload', $data);

        $response->assertBadRequest();
    }
    
    public function test_upload_ok()
    {
        Queue::fake();

        $content = "0000720231111200428278520001 1526,0      ,0          ,0          ,0          ,0          ,0          1526,0      ,0          ,0          ,0          0           0000000   ";
        $file = UploadedFile::fake()->createWithContent('test.txt', $content);
        $data = [
            'file' => $file, 
            'email' => 'test@test.com',
        ];

        $response = $this->post('api/deudores/upload', $data);

        $response->assertNoContent();
        Queue::assertPushed(ProcessFile::class);
    }
}
