<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\UpdateExchangeRatesAndPricesJob;
use App\Models\Currencies;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateExchangeRatesJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_updates_exchange_rates()
    {
        // simular respuesta de la API, el worker al hacer la petición, sera interceptada
        // y se devolverá la respuesta simulada
        Http::fake([
            env('EXCHANGE_RATE_API_URL') => Http::response([
                'rates' => [
                    'USD' => 1.0,
                    'EUR' => 0.85,
                    'GBP' => 0.75,
                ],
            ], 200),
        ]);


        Currencies::factory()->create(['name' => 'USD', 'exchange_rate' => 1.0]);
        Currencies::factory()->create(['name' => 'EUR', 'exchange_rate' => 1.0]);
        Currencies::factory()->create(['name' => 'GBP', 'exchange_rate' => 1.0]);

        UpdateExchangeRatesAndPricesJob::dispatch();


        $this->assertDatabaseHas('currencies', [
            'name' => 'USD',
            'exchange_rate' => 1.0,
        ]);
        $this->assertDatabaseHas('currencies', [
            'name' => 'EUR',
            'exchange_rate' => 0.85,
        ]);
        $this->assertDatabaseHas('currencies', [
            'name' => 'GBP',
            'exchange_rate' => 0.75,
        ]);
    }
}