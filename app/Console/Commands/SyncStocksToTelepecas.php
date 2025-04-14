<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncStocksToTelepecas extends Command
{
    protected $signature = 'sync:stocks-telepecas';
    protected $description = 'Envia os stocks da base de dados para a API da Telepeças';

    public function handle()
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            $this->error('❌ Não foi possível obter o token de autenticação.');
            return 1;
        }

        $stocks = Stock::all();

        foreach ($stocks as $stock) {
            $payload = [
                'token' => env('TELEPECAS_PUBLIC_KEY'),
                'externalId' => (string) $stock->externalId,
                'externalReference' => (string) $stock->externalId,
                'stockType' => 'P',
                'compatibleCarModels' => [
                    [
                        'externalModelId' => (string) $stock->externalModelId,
                        'externalMakeId' => (string) $stock->externalMakeId,
                    ]
                ],
                'tags' => [
                    [
                        'description' => $stock->description,
                    ]
                ],
                'partInfo' => [
                    'externalPartId' => (string) $stock->externalId,
                    'partDescription' => [
                        [
                            'languageCode' => 'PT',
                            'content' => $stock->description,
                        ]
                    ],
                    'partFixedObs' => [
                        [
                            'languageCode' => 'PT',
                            'content' => $stock->description,
                        ]
                    ],
                ],
                'vehicleInfo' => [
                    'externalModelId' => (string) $stock->externalModelId,
                ],
                'price1' => $stock->priceOEM ? number_format($stock->priceOEM, 2, '.', '') : "0",
                'price2' => $stock->priceOEM ? number_format($stock->priceOEM, 2, '.', '') : "0",
                'price1StockOff' => "0",
                'price2StockOff' => "0",
                'vatIncluded' => "1",
                'state' => "A",
                'references' => [
                    [
                        'reference' => $stock->refOEM ?? "SEM_REF",
                        'isMaster' => "0"
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.telepecas.com/stock/createStock', $payload);

            if ($response->successful()) {
                $this->info("✔️ Stock #{$stock->externalId} enviado com sucesso.");
            } else {
                $this->error("❌ Erro ao enviar stock #{$stock->externalId}:");
                $this->error("Status: " . $response->status());
                $this->error("Resposta: " . $response->body());
            }
        }

        return 0;
    }

    private function getAccessToken()
    {
        return Cache::remember('telepecas_token', 3600, function () {
            $response = Http::withHeaders([
                'Authorization' => env('TELEPECAS_AUTH'),
            ])->asForm()->post('https://api.telepecas.com/auth/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            return null;
        });
    }
}
