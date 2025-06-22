<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Currencies;
use App\Models\Prices;
use Illuminate\Support\Facades\Http;

class UpdateExchangeRatesAndPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $apiUrl = env('EXCHANGE_RATE_API_URL');

        
        $registeredCurrencies = Currencies::pluck('name')->toArray();

        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $rates = $response->json()['rates'];

            // filtrar solo las monedas registradas
            foreach ($registeredCurrencies as $currency) {
                if (isset($rates[$currency])) {
                    Currencies::where('name', $currency)->update(['exchange_rate' => $rates[$currency]]);
                }
            }

            // actualizar los precios en bulk
            $currencies = Currencies::whereIn('name', $registeredCurrencies)->get();
            foreach ($currencies as $currency) {
                $prices = Prices::where('currency_id', $currency->id)->get();
                foreach ($prices as $price) {
                    $product = $price->product;
                    $basePrice = $product->manufacturing_cost + $product->tax_cost + $product->price;
                    $price->price = $basePrice * $currency->exchange_rate;
                    $price->save();
                }
            }

            \Log::info('UpdateExchangeRatesAndPricesJob executed successfully');
        } else {
            \Log::error('Failed to fetch exchange rates from API');
        }
    }
}
