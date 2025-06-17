<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debtor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtorsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_empty_result(): void
    {   
        $response = $this->get('/api/deudores/99999999999');

        $response->assertOk();
        $response->assertContent('{}');
    }

    public function test_get_with_result(): void
    {
        $debtor = new Debtor();
        $debtor->cuit = '99999999999';
        $debtor->amount = 0;
        $debtor->max_situation = 0;
        $debtor->save();
        
        $response = $this->get('/api/deudores/99999999999');

        $response->assertOk();
        $response->assertJsonStructure(['id', 'cuit', 'max_situation', 'amount', 'created_at', 'updated_at']);
    }

    public function test_top_empty_result(): void
    {
        $response = $this->get('/api/deudores/top/1');

        $response->assertContent('[]');
        $response->assertStatus(200);
    }

    public function test_top_with_result(): void
    {
        $debtor = new Debtor();
        $debtor->cuit = '99999999999';
        $debtor->amount = 0;
        $debtor->max_situation = 0;
        $debtor->save();

        $response = $this->get('/api/deudores/top/1');

        $response->assertJsonIsArray();
        $response->assertStatus(200);
    }
}
