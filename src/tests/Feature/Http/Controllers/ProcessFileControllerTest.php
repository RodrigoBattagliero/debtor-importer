<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Jobs\ProcessFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessFileControllerTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_process_file_no_filename(): void
    {
        $data = [
            'email' => 'test@test.com'
        ];

        $response = $this->post('/api/deudores/procesar-archivo', $data);

        $response->assertBadRequest();
    }

    public function test_process_file_no_email(): void
    {
        $data = [
            'filename' => 'test.txt'
        ];

        $response = $this->post('/api/deudores/procesar-archivo', $data);

        $response->assertBadRequest();
    }

    public function test_process_file_ok(): void
    {
        Queue::fake();

        $data = [
            'filename' => 'test.txt',
            'email' => 'test@test.com',
        ];

        $response = $this->post('/api/deudores/procesar-archivo', $data);

        $response->assertNoContent();
        Queue::assertPushed(ProcessFile::class);
    }
}
