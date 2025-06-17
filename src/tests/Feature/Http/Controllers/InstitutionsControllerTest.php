<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InstitutionsControllerTest extends TestCase
{
    public function test_get_empty_result(): void
    {
        $response = $this->get('/api/entidades/1');

        $response->assertContent('{}');
        $response->assertStatus(200);
    }

    public function test_get_with_result(): void
    {
        $institution = new Institution();
        $institution->code = 1;
        $institution->amount = 0;
        $institution->save();

        $response = $this->get('/api/entidades/1');

        $response->assertJsonStructure(['id', 'code', 'amount', 'created_at', 'updated_at']);
        $response->assertStatus(200);
    }
}
